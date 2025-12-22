<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelRequest\Notifications\TravelReportSubmitted;
use Modules\TravelRequest\Repositories\TravelReportRepository;
use Modules\TravelRequest\Repositories\TravelReportRecommendationRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestEstimateRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Requests\TravelReport\StoreRequest;
use Modules\TravelRequest\Requests\TravelReport\UpdateRequest;


class TravelReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees ,
     * @param TravelReportRepository $travelReport ,
     * @param TravelReportRecommendationRepository $travelReportRecommendation ,
     * @param TravelRequestRepository $travelRequest ,
     * @param TravelRequestEstimateRepository $travelRequestEstimate ,
     * @param TravelRequestItineraryRepository $travelRequestItinerary ,
     * @param RoleRepository $roles ,
     * @param StatusRepository $status ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        EmployeeRepository                   $employees,
        TravelReportRepository               $travelReport,
        TravelReportRecommendationRepository $travelReportRecommendation,
        TravelRequestRepository              $travelRequest,
        TravelRequestEstimateRepository      $travelRequestEstimate,
        TravelRequestItineraryRepository     $travelRequestItinerary,
        RoleRepository                       $roles,
        StatusRepository                     $status,
        UserRepository                       $user
    )
    {
        $this->employees = $employees;
        $this->travelReport = $travelReport;
        $this->travelReportRecommendation = $travelReportRecommendation;
        $this->travelRequest = $travelRequest;
        $this->travelRequestEstimate = $travelRequestEstimate;
        $this->travelRequestItinerary = $travelRequestItinerary;
        $this->roles = $roles;
        $this->status = $status;
        $this->user = $user;
        $this->destinationPath = 'travelreport';
    }

    /**
     * Display a listing of the travel report by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->travelReport->with(['travelRequest', 'logs'])
                ->where(function ($q) use ($authUser) {
                    $q->where('created_by', $authUser->id);
                })
                // ->orWhereHas('logs', function ($q) use ($authUser) {
                //     $q->where('user_id', $authUser->id);
                //     $q->orWhere('original_user_id', $authUser->id);
                // })
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('departure_date', function ($row) {
                    return $row->travelRequest->getDepartureDate();
                })->addColumn('return_date', function ($row) {
                    return $row->travelRequest->getReturnDate();
                })->addColumn('final_destination', function ($row) {
                    return $row->travelRequest->final_destination;
                })->addColumn('travel_number', function ($row) {
                    return $row->travelRequest->getTravelRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getReporterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('travel.reports.show', $row->id) . '" rel="tooltip"';
                    $btn .= ' title="View Travel Report"><i class="bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('travel.reports.edit', $row->id) . '" rel="tooltip" title="Edit Travel Report">';
                        $btn .= '<i class="bi bi-pencil-square"></i></a>';
                    } else if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('travel.report.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.reports.destroy', $row->id) . '"';
                        $btn .= ' rel="tooltip" title="Delete Travel Report">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TravelRequest::TravelReport.index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($travelRequestId)
    {
        $authUser = auth()->user();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $travelReport = $this->travelReport->select('*')->where('travel_request_id', $travelRequestId)->first();
        $supervisors = $this->user->getSupervisors($authUser);

        if ($travelReport) {
            $id = $travelReport->id;
            $travelReportRecommendation = $this->travelReportRecommendation
                ->where('travel_report_id', '=', $id)
                ->orderby('id', 'asc')
                ->get();
            if (in_array($travelReport->status_id, [3, 6, 8])) {
                return view('TravelRequest::TravelReport.view')
                    ->withAuthUser($authUser)
                    ->withTravelReport($travelReport)
                    ->withTravelRequest($travelRequest)
                    ->withTravelReportRecommendation($travelReportRecommendation)
                    ->withRoles($this->roles->get());
            }

            return view('TravelRequest::TravelReport.edit')
                ->withAuthUser($authUser)
                ->withSupervisors($supervisors)
                ->withTravelReport($travelReport)
                ->withTravelRequest($travelRequest)
                ->withTravelReportRecommendations($travelReportRecommendation)
                ->withRoles($this->roles->get());
        } else {
            $this->authorize('createReport', $travelRequest);
            return view('TravelRequest::TravelReport.create')
                ->withSupervisors($supervisors)
                ->withTravelRequest($travelRequest);
        }
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param StoreRequest $request
     * @param $travelRequestId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $travelRequestId)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();

        foreach ($inputs['recommendation']['day_number'] as $index => $subject) {
            $recommendationInputs = ['day_number' => $inputs['recommendation']['day_number'][$index],
                'activity_date' => $inputs['recommendation']['activity_date'][$index],
                'completed_tasks' => $inputs['recommendation']['completed_tasks'][$index],
                'remarks' => $inputs['recommendation']['remarks'][$index]
            ];
            if (count(array_filter($recommendationInputs))) {
                $inputs['recommendation_input'][$index] = $recommendationInputs;
            }
        }
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $inputs['travel_request_id'] = $travelRequest->id;
        $inputs['created_by'] = $authUser->id;
        $latestTenure = $authUser->employee->latestTenure;
        $supervisor_id = $this->user->select('id')
            ->whereIn('employee_id', [$latestTenure->supervisor_id])
            ->get();
        $inputs['approver_id'] = $supervisor_id[0]['id'];
        $inputs['status_id'] = 1;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelReport = $this->travelReport->create($inputs);
        if ($travelReport) {
            return redirect()->route('travel.reports.index')
                ->withSuccessMessage('Travel Report successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Travel Report can not be added.');
    }

    /**
     * Show the form for editing a travel report by user.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($reportId)
    {
        $authUser = auth()->user();
        $travelReport = $this->travelReport->find($reportId);
        $this->authorize('update', $travelReport);
        $travelReportRecommendation = $this->travelReportRecommendation->where('travel_report_id', '=', $reportId)
            ->orderby('id', 'asc')
            ->get();
        $supervisors = $this->user->getSupervisors($authUser);

        return view('TravelRequest::TravelReport.edit')
            ->withAuthUser($authUser)
            ->withSupervisors($supervisors)
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest)
            ->withTravelReportRecommendations($travelReportRecommendation)
            ->withRoles($this->roles->get());
    }

    /**
     * Store a newly created travel report in storage.
     *
     * @param UpdateRequest $request
     * @param $travelReportId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $travelReportId)
    {
        $authUser = auth()->user();
        $travelReport = $this->travelReport->find($travelReportId);
        $this->authorize('update', $travelReport);
        $inputs = $request->validated();

        foreach ($inputs['recommendation']['day_number'] as $index => $subject) {
            $recommendationInputs = ['day_number' => $inputs['recommendation']['day_number'][$index],
                'activity_date' => $inputs['recommendation']['activity_date'][$index],
                'completed_tasks' => $inputs['recommendation']['completed_tasks'][$index],
                'remarks' => $inputs['recommendation']['remarks'][$index]
            ];
            if (count(array_filter($recommendationInputs))) {
                $inputs['recommendation_input'][$index] = $recommendationInputs;
            }
        }

        $inputs['updated_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $travelReport = $this->travelReport->update($travelReport->id, $inputs);
        if ($travelReport) {
            $message = 'Travel report is successfully updated.';
            if ($travelReport->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Travel report is successfully submitted.';
                $travelReport->approver->notify(new TravelReportSubmitted($travelReport));
            }
            return redirect()->route('travel.reports.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Travel report can not be updated.');
    }

    /**
     * View the details the specified travel request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $travelReport = $this->travelReport->find($id);
//        $this->authorize('view', $travelReport);

        return view('TravelRequest::TravelReport.show')
            ->withAuthUser($authUser)
            ->withTravelReport($travelReport)
            ->withTravelRequest($travelReport->travelRequest)
            ->withRoles($this->roles->get());
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $travelReport = $this->travelReport->find($id);
        $this->authorize('delete', $travelReport);
        $flag = $this->travelReport->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Travel report is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel report can not deleted.',
        ], 422);
    }
}
