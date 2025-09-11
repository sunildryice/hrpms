<?php

namespace Modules\GoodRequest\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\GoodRequestForwarded;
use Modules\Master\Repositories\UnitRepository;
use Modules\GoodRequest\Notifications\GoodRequestSubmitted;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\GoodRequest\Requests\StoreRequest;
use Modules\GoodRequest\Requests\UpdateRequest;

use DataTables;
use Illuminate\Support\Facades\DB;

class GoodRequestController extends Controller
{
    protected $destinationPath;
    public function __construct(
        protected EmployeeRepository    $employees,
        protected FiscalYearRepository  $fiscalYears,
        protected GoodRequestRepository $goodRequests,
        protected Helper                $helper,
        protected ProjectCodeRepository $projectCodes,
        protected UserRepository        $users
    )
    {
        $this->destinationPath = 'goodRequest';
    }

    /**
     * Display a listing of the good requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->with(['status', 'logs', 'requester'])
                ->where(function ($q) use ($authUser) {
                    $q->whereCreatedBy($authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('good.requests.show', $row->id) . '" rel="tooltip" title="View Good Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('good.requests.edit', $row->id) . '" rel="tooltip" title="Edit Good Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('good.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::index');
    }

    /**
     * Show the form for creating a new good request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $projectCodes = $this->projectCodes->getActiveProjectCodes();
        return view('GoodRequest::create')
            ->withProjectCodes($projectCodes);
    }

    /**
     * Store a newly created good request in storage.
     *
     * @param \Modules\GoodRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->create($inputs);

        if ($goodRequest) {
            $message = 'Good request is successfully added.';
            return redirect()->route('good.requests.edit', $goodRequest->id)
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Good Request can not be added.');
    }

    /**
     * Show the specified good request.
     *
     * @param $goodRequestId
     * @return mixed
     */
    public function show($goodRequestId)
    {
        $authUser = auth()->user();
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $goodRequestAssets = $goodRequest->goodRequestAssets;

        return view('GoodRequest::show')
            ->withGoodRequest($goodRequest)
            ->withGoodRequestAssets($goodRequestAssets);
    }

    /**
     * Show the form for editing the specified good request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $goodRequest = $this->goodRequests->find($id);
        $this->authorize('update', $goodRequest);

        $reviewers = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-good-request');

        $projectCodes = $this->projectCodes->getActiveProjectCodes();

        return view('GoodRequest::edit')
            ->withAuthUser($authUser)
            ->withGoodRequest($goodRequest)
            ->withProjectCodes($projectCodes)
            ->withApprovers($approvers)
            ->withReviewers($reviewers);
    }

    /**
     * Update the specified good request in storage.
     *
     * @param \Modules\GoodRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $goodRequest = $this->goodRequests->find($id);
        $this->authorize('update', $goodRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->update($id, $inputs);

        if ($goodRequest) {
            $message = 'Good request is successfully updated.';
            if ($goodRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Good request is successfully submitted.';
                if ($goodRequest->reviewer_id) {
                    $goodRequest->reviewer->notify(new GoodRequestSubmitted($goodRequest));
                } else {
                    $goodRequest->approver->notify(new GoodRequestForwarded($goodRequest));
                }
            }
            if($inputs['btn'] == 'save'){
                return redirect()->back()->withInput()->withSuccessMessage($message);
            }
            return redirect()->route('good.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Good Request can not be updated.');
    }

    /**
     * Remove the specified good request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $goodRequest = $this->goodRequests->find($id);
        $this->authorize('delete', $goodRequest);
        $flag = $this->goodRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Good request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Good request can not deleted.',
        ], 422);
    }

    public function updateReceiverNote(Request $request, $id)
    {
        $goodRequest = $this->goodRequests->find($id);

        $this->authorize('addReceiverNote', $goodRequest);

        DB::beginTransaction();
        try {
            // Updating the receiver_note in good_requests table
            $goodRequest->receiver_note = $request->receiver_note;
            $goodRequest->received_at = now();
            $goodRequest->save();

            DB::commit();
            return redirect()->back()->withSuccessMessage('Receiver note added.');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return redirect()->back()->withInput()->withWarningMessage('Receiver note could not be added.');
        }
    }
}
