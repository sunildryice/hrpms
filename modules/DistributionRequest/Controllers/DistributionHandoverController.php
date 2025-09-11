<?php

namespace Modules\DistributionRequest\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\DistributionRequest\Notifications\DistributionHandoverSubmitted;
use Modules\DistributionRequest\Repositories\DistributionHandoverRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\DistributionRequest\Requests\Handover\UpdateRequest;

use DataTables;

class DistributionHandoverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistributionHandoverRepository $distributionHandovers
     * @param DistributionRequestRepository $distributionRequests
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param OfficeRepository $offices
     * @param ProjectCodeRepository $projectCodes
     * @param UserRepository $users
     */
    public function __construct(
        protected DistributionHandoverRepository $distributionHandovers,
        protected DistributionRequestRepository  $distributionRequests,
        protected DistrictRepository             $districts,
        protected EmployeeRepository             $employees,
        protected FiscalYearRepository           $fiscalYears,
        protected InventoryItemRepository        $inventoryItems,
        protected ItemRepository                 $items,
        protected LocalLevelRepository           $localLevels,
        protected OfficeRepository               $offices,
        protected ProjectCodeRepository          $projectCodes,
        protected UserRepository                 $users,
    )
    {
        $this->destinationPath = 'distributionRequest';
    }

    /**
     * Display a listing of the distribution handovers
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->distributionHandovers->with(['status', 'projectCode', 'district'])->select(['*'])
                ->whereCreatedBy($authUser->id);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })->addColumn('project', function ($row) {
                    return $row->getProjectCode();
                })->addColumn('requisition_number', function ($row) {
                    return $row->getDistributionHandoverNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('distribution.requests.handovers.show', $row->id) . '" rel="tooltip" title="View Distribution Handover"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('distribution.requests.handovers.edit', $row->id) . '" rel="tooltip" title="Edit Distribution Handover"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Distribution Handover"';
                        $btn .= 'href="' . route('distribution.requests.handovers.print', $row->id) . '" target="_blank">';
                        $btn .= '<i class="bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('distribution.requests.handovers.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('DistributionRequest::Handover.index');
    }

    /**
     * Show the form for creating a new distribution handover by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {

    }

    /**
     * Store a newly created distribution handover in storage.
     *
     * @param Request $request
     * @param $distributionRequestId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(Request $request, $distributionRequestId)
    {
        $distributionRequest = $this->distributionRequests->find($distributionRequestId);
        $this->authorize('createHandover', $distributionRequest);
        $authUser = auth()->user();
        $inputs['created_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionHandover = $this->distributionHandovers->createHandover($distributionRequest, $inputs);
        if ($request->wantsJson()) {
            if ($distributionHandover) {
                return response()->json([
                    'type' => 'success',
                    'message' => 'Distribution handover is successfully created from distribution request.',
                    'handoverId' => $distributionHandover->id,
                ], 200);
            }
            return response()->json([
                'type' => 'error',
                'message' => 'Distribution handover can not be created.',
            ], 422);
        } else {
            if ($distributionHandover) {
                return redirect()->route('distribution.requests.handovers.edit',$distributionHandover->id)
                    ->withSuccessMessage('Distribution handover is successfully created from distribution request.');
            }
            return redirect()->back()->withInput()
                ->withWarningMessage('Distribution handover can not be created.');
        }
    }

    /**
     * Show the specified distribution handover.
     *
     * @param $distributionHandoverId
     * @return mixed
     */
    public function show($distributionHandoverId)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($distributionHandoverId);

        return view('DistributionRequest::Handover.show')
            ->withDistributionHandover($distributionHandover)
            ->withDistributionRequest($distributionHandover->distributionRequest);
    }

    /**
     * Show the form for editing the specified distribution handover.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->find($id);
        $this->authorize('update', $distributionHandover);

        $approvers = $this->users->permissionBasedUsers('approve-distribution-handover');
        $receivers = $this->employees->getActiveEmployees();
        $localLevels = $this->localLevels->select(['*'])
            ->where('district_id', $distributionHandover->district_id)
            ->orderBy('local_level_name', 'asc')->get();

        return view('DistributionRequest::Handover.edit')
            ->withApprovers($approvers)
            ->withAuthUser($authUser)
            ->withDistributionHandover($distributionHandover)
            ->withReceivers($receivers)
            ->withLocalLevels($localLevels);
    }

    /**
     * Update the specified distribution handover in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $distributionHandover = $this->distributionHandovers->find($id);
        $this->authorize('update', $distributionHandover);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $distributionHandover = $this->distributionHandovers->update($id, $inputs);

        if ($distributionHandover) {
            $message = 'Distribution handover is successfully updated.';
            if ($distributionHandover->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Distribution handover is successfully submitted.';
                $distributionHandover->approver->notify(new DistributionHandoverSubmitted($distributionHandover));
            }
            return redirect()->route('distribution.requests.handovers.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Distribution handover can not be updated.');
    }

    /**
     * Remove the specified distribution handover from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $distributionHandover = $this->distributionHandovers->find($id);
        $this->authorize('delete', $distributionHandover);
        $flag = $this->distributionHandovers->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Distribution handover is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Distribution handover can not deleted.',
        ], 422);
    }

    /**
     * Show the specified distribution handover in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printHandover($id)
    {
        $authUser = auth()->user();
        $distributionHandover = $this->distributionHandovers->with(['distributionHandoverItems', 'logs', 'approvedLog'])
            ->find($id);

        return view('DistributionRequest::Handover.print')
            ->withDistributionHandover($distributionHandover);
    }
}
