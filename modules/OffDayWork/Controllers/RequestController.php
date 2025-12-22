<?php

namespace Modules\OffDayWork\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\Holiday;
use Modules\OffDayWork\Notifications\OffDayWorkSubmitted;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\OffDayWork\Requests\StoreRequest;
use Modules\OffDayWork\Requests\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\HolidayRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\OffDayWork\Repositories\OffDayWorkLogRepository;
use Modules\Privilege\Models\User;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class RequestController extends Controller
{

    public function __construct(
        protected ProjectCodeRepository $projects,
        protected UserRepository $users,
        protected OffDayWorkRepository $offDayWork,
        protected OffDayWorkLogRepository $offDayWorkLogs,
        protected HolidayRepository $holidays,
        protected FiscalYearRepository $fiscalYears
    ) {}


    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->offDayWork
                ->where('requester_id', '=', auth()->id())
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('request_date', function ($row) {
                    return  $row->getRequestDate();
                })
                ->addColumn('request_id', function ($row) {
                    return $row->getRequestId();
                })
                ->editColumn('date', function ($row) {
                    return $row->getOffDayWorkDate();
                })
                ->addColumn('project', function ($row) {
                    return $row->getProjectNames() ?? '-';
                })
                ->addColumn('status', function ($row) {

                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {

                    $authUser = auth()->user();
                    $btn = '<a href="' . route('off.day.work.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye"></i> 
                    </a>';

                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('off.day.work.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Off Day Work Request"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('OffDayWork::index');
    }


    public function create()
    {
        $authUser = User::find(auth()->id());

        $projects = $this->projects->pluck('short_name', 'id');
        $supervisors = $this->users->getSupervisors($authUser)->pluck('full_name', 'id');


        return view('OffDayWork::create', [
            'projects' => $projects,
            'supervisors' => $supervisors,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();


        $inputs = $request->validated();

        try {
            $inputs['requester_id'] = auth()->id();
            $inputs['approver_id'] = $inputs['send_to'];
            $inputs['deliverables'] = $inputs['deliverables'];
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $inputs['office_id'] = $authUser->employee->office_id;
            $inputs['department_id'] = $authUser->employee->department_id;
            $inputs['created_by'] = auth()->id();
            $inputs['request_date'] = now();

            $projectIds   = $inputs['project_ids'] ?? [];
            $deliverables = $inputs['deliverables'] ?? [];

            DB::beginTransaction();

            if ($inputs['btn'] === 'submit') {


                $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

                $offDayWorkRequest = $this->offDayWork->create($inputs);

                $pivotData = [];
                foreach ($projectIds as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($deliverables[$projectId] ?? []),
                    ];
                }

                $offDayWorkRequest->projects()->sync($pivotData);

                $logInputs = [
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Off Day Work request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => $offDayWorkRequest->status_id,
                    'off_day_work_id' => $offDayWorkRequest->id,
                ];


                $this->offDayWorkLogs->create($logInputs);


                $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();

                $fiscalYear = $this->fiscalYears->find($inputs['fiscal_year_id']);

                $inputs['off_day_work_number'] = $this->offDayWork->getOffDayWorkRequestNumber($fiscalYear);
                $offDayWorkRequest->off_day_work_number = $inputs['off_day_work_number'];
                $offDayWorkRequest->fiscal_year_id = $inputs['fiscal_year_id'];
                $offDayWorkRequest->save();

                $offDayWorkRequest->approver->notify(new OffDayWorkSubmitted($offDayWorkRequest));
            } else {
                $inputs['status_id'] = config('constant.CREATED_STATUS');

                $offDayWorkRequest = $this->offDayWork->create($inputs);

                $pivotData = [];
                foreach ($projectIds as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($deliverables[$projectId] ?? []),
                    ];
                }

                $offDayWorkRequest->projects()->sync($pivotData);
            }

            DB::commit();

            return redirect()->route('off.day.work.index')->with('success_message', 'Off Day Work request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $offDayWork = $this->offDayWork->with(['requester', 'approver', 'projects', 'logs.user'])->findOrFail($id);

        // $this->authorize('view', $lieuLeaveRequest);

        return view('OffDayWork::show', [
            'offDayWork' => $offDayWork,
        ]);
    }

    public function edit($id)
    {
        $offDayWork = $this->offDayWork
            ->with(['logs', 'projects'])
            ->findOrFail($id);

        $selectedProjectIds = $offDayWork->projects->pluck('id')->all();

        $deliverables = [];
        foreach ($offDayWork->projects as $project) {
            $deliverables[$project->id] = $project->pivot && $project->pivot->deliverables
                ? (json_decode($project->pivot->deliverables, true) ?: [])
                : [];
        };

        return view('OffDayWork::edit', [
            'offDayWork'         => $offDayWork,
            'projects'           => $this->projects->pluck('short_name', 'id'),
            'supervisors'        => $this->users
                ->getSupervisors(auth()->user())
                ->pluck('full_name', 'id'),
            'selectedProjectIds' => $selectedProjectIds,
            'deliverables'       => $deliverables,
        ]);
    }


    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $inputs   = $request->validated();

        $projectIds   = $inputs['project_ids'] ?? [];
        $deliverables = $inputs['deliverables'] ?? [];

        try {
            DB::beginTransaction();

            $inputs['requester_id']     = auth()->id();
            $inputs['approver_id']      = $inputs['send_to'];
            $inputs['original_user_id'] = session()->get('original_user');
            $inputs['office_id']        = $authUser->employee->office_id;
            $inputs['department_id']    = $authUser->employee->department_id;
            $inputs['updated_by']       = auth()->id();

            if ($inputs['btn'] === 'submit') {
                $inputs['status_id']    = config('constant.SUBMITTED_STATUS');

                $offDayWork = $this->offDayWork->update($id, $inputs);

                $pivotData = [];
                foreach ($projectIds as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($deliverables[$projectId] ?? []),
                    ];
                }
                $offDayWork->projects()->sync($pivotData);

                $logInputs = [
                    'user_id'          => $authUser->id,
                    'log_remarks'      => 'Work From Home request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id'        => $offDayWork->status_id,
                    'off_day_work_id' => $offDayWork->id,
                ];

                $this->offDayWorkLogs->create($logInputs);

                $inputs['fiscal_year_id'] = $this->fiscalYears->getCurrentFiscalYearId();

                $fiscalYear = $this->fiscalYears->find($inputs['fiscal_year_id']);

                $inputs['off_day_work_number'] = $this->offDayWork->getOffDayWorkRequestNumber($fiscalYear);
                $offDayWork->off_day_work_number = $inputs['off_day_work_number'];
                $offDayWork->fiscal_year_id = $inputs['fiscal_year_id'];
                $offDayWork->save();

                $offDayWork->approver->notify(
                    new OffDayWorkSubmitted($offDayWork)
                );
            } else {

                $offDayWork = $this->offDayWork->update($id, $inputs);

                $pivotData = [];
                foreach ($projectIds as $projectId) {
                    $pivotData[$projectId] = [
                        'deliverables' => json_encode($deliverables[$projectId] ?? []),
                    ];
                }

                $offDayWork->projects()->sync($pivotData);
            }

            DB::commit();

            return redirect()
                ->route('off.day.work.index')
                ->with('success_message', 'Off Day Work request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error_message', 'Something went wrong! ' . $e->getMessage());
        }
    }
}
