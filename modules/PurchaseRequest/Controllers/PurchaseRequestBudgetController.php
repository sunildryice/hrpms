<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmitted;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PurchaseRequest\Requests\StoreRequest;
use Modules\PurchaseRequest\Requests\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\OfficeRepository;

class PurchaseRequestBudgetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseRequestRepository $purchaseRequests
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository          $districts,
        EmployeeRepository          $employees,
        FiscalYearRepository        $fiscalYears,
        OfficeRepository            $offices,
        PurchaseRequestRepository   $purchaseRequests,
        UserRepository              $users
    )
    {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->offices = $offices;
        $this->purchaseRequests = $purchaseRequests;
        $this->users = $users;
        $this->destinationPath = 'purchaserequest';
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->purchaseRequests->with(['fiscalYear', 'status', 'logs'])->select(['*'])
                ->whereRequesterId( $authUser->id)
                ->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('required_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('required_date', function ($row){
                    return $row->getRequiredDate();
                })->addColumn('request_date', function ($row){
                    return $row->getRequestDate();
                })->addColumn('purchase_number', function ($row){
                    return $row->getPurchaseRequestNumber();
                })->addColumn('estimated_amount', function ($row){
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('purchase.requests.show', $row->id) . '" rel="tooltip" title="View Purchase Request"><i class="bi bi-eye"></i></a>';
                    if($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('purchase.requests.edit', $row->id) . '" rel="tooltip" title="Edit Purchase Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('purchase.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if($authUser->can('print', $row)){
                        $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" rel="toottip" title="Print Purchase Request" ';
                        $btn .= 'href="' . route('purchase.requests.print', $row->id) . '">';
                        $btn .= '<i class="bi-printer"></i></a>';
                    }
                    if($authUser->can('replicate', $row)){
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-outline-primary btn-sm replicate-record" rel="tooltip" title="Replicate Purchase Request"';
                        $btn .= 'data-href="' . route('purchase.requests.replicate.store', $row->id) . '"';
                        $btn .= ' data-purchase-number='.$row->getPurchaseRequestNumber() .'>';
                        $btn .= '<i class="bi-clipboard"></i></a>';
                    }

                    if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-purchase-request"';
                        $btn .= 'data-href = "' . route('purchase.requests.amend.store', $row->id) . '" title="Amend Purchase Request">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseRequest::index');
    }

    /**
     * Show the form for creating a new purchase request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        return view('PurchaseRequest::create')
            ->withDistricts($this->districts->getEnabledDistricts());
    }

    /**
     * Store a newly created purchase request in storage.
     *
     * @param \Modules\PurchaseRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseRequest = $this->purchaseRequests->create($inputs);

        if ($purchaseRequest) {
            return redirect()->route('purchase.requests.edit', $purchaseRequest->id)
                ->withSuccessMessage('Purchase Request successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase Request can not be added.');
    }

    /**
     * Show the specified purchase request.
     *
     * @param $purchaseRequestId
     * @return mixed
     */
    public function show($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);

        return view('PurchaseRequest::show')
            ->withPurchaseRequest($purchaseRequest);
    }

    /**
     * Show the form for editing the specified purchase request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('update', $purchaseRequest);

        $districts = $this->districts->getEnabledDistricts();
        return view('PurchaseRequest::edit')
            ->withAuthUser(auth()->user())
            ->withDistricts($districts)
            ->withPurchaseRequest($purchaseRequest);
    }

    /**
     * Update the specified purchase request in storage.
     *
     * @param \Modules\PurchaseRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('update', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $purchaseRequest = $this->purchaseRequests->update($id, $inputs);
        if ($purchaseRequest) {
            $message = 'Purchase request is successfully updated.';
            return redirect()->route('purchase.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase Request can not be updated.');
    }

    /**
     * Remove the specified purchase request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('delete', $purchaseRequest);
        $flag = $this->purchaseRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase request can not deleted.',
        ], 422);
    }

    /**
     * Amend the specified purchase request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('amend', $purchaseRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->purchaseRequests->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase request is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase request can not amended.',
        ], 422);
    }

    /**
     * Replicate the specified purchase request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function replicate($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('replicate', $purchaseRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->purchaseRequests->replicate($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase request is successfully replicated.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase request can not replicated.',
        ], 422);
    }

    /**
     * Show the specified purchase order in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printRequest($id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('print', $purchaseRequest);

        return view('PurchaseRequest::print')
            ->withPurchaseRequest($purchaseRequest);
    }
}
