<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\PaymentDetailRepository;
use Modules\Employee\Repositories\PaymentMasterRepository;
use Modules\Employee\Requests\PaymentDetail\StoreRequest;
use Modules\Employee\Requests\PaymentDetail\UpdateRequest;

use DataTables;
use Modules\Payroll\Repositories\PaymentItemRepository;

class PaymentDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  PaymentItemRepository $paymentItems
     * @param  PaymentDetailRepository $paymentDetails
     * @param  PaymentMasterRepository $paymentMasters
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        PaymentItemRepository $paymentItems,
        PaymentDetailRepository $paymentDetails,
        PaymentMasterRepository $paymentMasters,
    )
    {
        $this->employees   = $employees;
        $this->paymentItems   = $paymentItems;
        $this->paymentDetails   = $paymentDetails;
        $this->paymentMasters   = $paymentMasters;
        $this->slugs = ['provident-fund', 'gratuity', 'medical-insurance', 'ssf-deduction'];
    }

    /**
     * Display a listing of the payment details.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $id)
    {
        $paymentMaster = $this->paymentMasters->find($id);
        if ($request->ajax()) {
            $data = $this->paymentDetails->select(['*'])
                ->with(['paymentItem'])
                ->where('payment_master_id', $id);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if(!in_array($row->paymentItem->slug, $this->slugs)) {
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-detail-modal-form" href="';
                        $btn .= route('employees.payments.masters.details.edit', [$row->payment_master_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('employees.payments.masters.details.destroy', [$row->payment_master_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('payment_item', function ($row) {
                    return $row->getPaymentItem();
                })->addColumn('type', function ($row) {
                    return $row->paymentItem->type;
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Employee::PaymentDetail.index')
            ->withPaymentMaster($paymentMaster);
    }

    /**
     * show the form to create new payment detail
     *
     * @param $paymentMasterId
     * @return mixed
     */
    public function create($paymentMasterId)
    {
        $paymentMaster = $this->paymentMasters->find($paymentMasterId);
        $paymentItems = $this->paymentItems->select(['*'])
            ->whereNotIn('slug', $this->slugs)
            ->where('frequency', '<>', 0)
            ->get();
        return view('Employee::PaymentDetail.create')
            ->withPaymentItems($paymentItems)
            ->withPaymentMaster($paymentMaster);
    }

    /**
     * Store a newly created employee payment detail in storage.
     *
     * @param StoreRequest $request
     * @param $paymentMasterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $paymentMasterId)
    {
        $paymentMaster = $this->paymentMasters->find($paymentMasterId);
        $inputs = $request->validated();
        $inputs['payment_master_id'] = $paymentMaster->id;
        $inputs['created_by'] = auth()->id();
        $inputs['updated_by'] = NULL;
        $paymentDetail = $this->paymentDetails->create($inputs);
        if ($paymentDetail) {
            return response()->json(['status' => 'ok',
                'payment_detail' => $paymentDetail,
                'message' => 'Payment detail is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment detail can not be added.'], 422);
    }

    /**
     * Show the form to edit payment detail
     *
     * @param $paymentMasterId
     * @param $id
     * @return mixed
     */
    public function edit($paymentMasterId, $id)
    {
        $paymentMaster = $this->paymentMasters->find($paymentMasterId);
        $paymentDetail = $this->paymentDetails->find($id);
        $paymentItems = $this->paymentItems->select(['*'])
            ->whereNotIn('slug', $this->slugs)
            ->where('frequency', '<>', 0)->get();

        return view('Employee::PaymentDetail.edit')
            ->withPaymentDetail($paymentDetail)
            ->withPaymentItems($paymentItems);
    }

    /**
     * Update the specified payment detail in storage.
     *
     * @param UpdateRequest $request
     * @param $paymentMasterId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $paymentMasterId, $id)
    {
        $paymentDetail = $this->paymentDetails->find($id);
        $inputs = $request->validated();
        $inputs['created_by'] = $paymentDetail->created_by;
        $inputs['updated_by'] = auth()->id();
        $paymentDetail = $this->paymentDetails->update($id, $inputs);
        if ($paymentDetail) {
            return response()->json(['status' => 'ok',
                'payment_detail' => $paymentDetail,
                'message' => 'Payment detail is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment detail can not be updated.'], 422);
    }

    /**
     * Remove the specified payment detail from storage.
     *
     * @param $paymentMasterId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($paymentMasterId, $id)
    {
        $paymentDetail = $this->paymentDetails->find($id);
        $flag = $this->paymentDetails->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Payment detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Payment detail can not deleted.',
        ], 422);
    }
}
