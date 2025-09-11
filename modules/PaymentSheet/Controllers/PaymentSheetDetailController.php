<?php

namespace Modules\PaymentSheet\Controllers;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\PaymentSheet\Repositories\PaymentBillRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetDetailRepository;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;

use Modules\PaymentSheet\Requests\Detail\StoreRequest;
use Modules\PaymentSheet\Requests\Detail\UpdateRequest;

use DataTables;
use Illuminate\Support\Facades\DB;
use Modules\PaymentSheet\Models\PaymentBill;

class PaymentSheetDetailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param OfficeRepository $offices
     * @param PaymentBillRepository $paymentBills
     * @param PaymentSheetRepository $paymentSheets
     * @param PaymentSheetDetailRepository $paymentSheetDetails
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected DonorCodeRepository $donorCodes,
        protected OfficeRepository $offices,
        protected PaymentBillRepository       $paymentBills,
        protected PaymentSheetRepository       $paymentSheets,
        protected PaymentSheetDetailRepository $paymentSheetDetails
    )
    {
    }

    /**
     * Display a listing of the payment sheet details
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $paymentSheetId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $paymentSheet = $this->paymentSheets->find($paymentSheetId);
            $data = $this->paymentSheetDetails->select([
                'id', 'payment_sheet_id','payment_bill_id', 'activity_code_id', 'account_code_id', 'donor_code_id',
                'total_amount', 'vat_amount', 'tds_amount', 'net_amount','charged_office_id', 'description'
            ])->wherePaymentSheetId($paymentSheetId);
            return  DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bill_number', function ($row){
                    return $row->getBillNumber();
                })->addColumn('activity', function ($row){
                    return $row->activityCode->getActivityCode();
                })->addColumn('account', function ($row){
                    return $row->accountCode->getAccountCode();
                })->addColumn('donor', function ($row){
                    return $row->getDonorCode();
                })->addColumn('charged_office', function ($row){
                    return $row->getChargedOffice();
                })->addColumn('description', function ($row){
                    return $row->getDescription();
                })
                ->addColumn('total_amount', function ($row){
                    return $row->total_amount;
                })
                ->addColumn('vat_amount', function ($row){
                    return $row->vat_amount;
                })
                ->addColumn('tds_amount', function ($row){
                    return $row->tds_amount;
                })
                ->addColumn('net_amount', function ($row){
                    return $row->net_amount;
                })
                ->withQuery('sum_total_amount', function ($filteredQuery) {
                    return $filteredQuery->sum('total_amount');
                })
                ->withQuery('sum_vat_amount', function ($filteredQuery) {
                    return $filteredQuery->sum('vat_amount');
                })
                ->withQuery('sum_tds_amount', function ($filteredQuery) {
                    return $filteredQuery->sum('tds_amount');
                })
                ->withQuery('sum_net_amount', function ($filteredQuery) {
                    return $filteredQuery->sum('net_amount');
                })
                ->addColumn('action', function ($row) use ($authUser, $paymentSheet) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-detail-modal-form" href="';
                    $btn .= route('payment.sheets.details.edit', [$row->payment_sheet_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('payment.sheets.details.destroy', [$row->payment_sheet_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new payment sheet detail.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($id);

        $paymentBills = $this->paymentBills->select(['*'])
            ->where(function ($q) use ($paymentSheet, $authUser) {
                $q->where('supplier_id', '=', $paymentSheet->supplier_id)
                    ->where('office_id', '=', $authUser->employee->office_id)
                    ->whereRaw('settled_amount < bill_amount');
            })->get();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $offices = $this->offices->getActiveOffices();
        $tdsPercentages = Helper::tdsPercentages();

        return view('PaymentSheet::Detail.create')
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withOffices($offices)
            ->withPaymentBills($paymentBills)
            ->withPaymentSheet($paymentSheet)
            ->withTdsPercentages($tdsPercentages);
    }

    /**
     * Store a newly created payment sheet detail in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheets->find($id);
        $inputs = $request->validated();
        $inputs['payment_sheet_id'] = $paymentSheet->id;
        $inputs['tds_flag'] = isset($request->tds_applicable);
        $paymentSheetDetail = $this->paymentSheetDetails->create($inputs);

        if ($paymentSheetDetail) {
            return response()->json(['status' => 'ok',
                'paymentSheet' => $paymentSheet,
                'paymentSheetDetail' => $paymentSheetDetail,
                'paymentDetailCount' => $paymentSheet->paymentSheetDetails->count(),
                'message' => 'Payment sheet detail is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment sheet detail can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified payment sheet detail.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($tbId, $id)
    {
        $paymentSheet = $this->paymentSheets->find($tbId);
        $paymentSheetDetail = $this->paymentSheetDetails->find($id);
        $this->authorize('update', $paymentSheet);
        $authUser = auth()->user();
        // $paymentBills = $this->paymentBills->select(['*'])
        //     ->where(function ($q) use($paymentSheet, $authUser){
        //         $q->where('supplier_id', $paymentSheet->supplier_id)
        //             ->where('paid_percentage', '<', 100)
        //             ->whereOfficeId($authUser->employee->office_id);
        //     })->orWhere('id', $paymentSheetDetail->payment_bill_id)
        //     ->orderBy('bill_date')->get();


        $paidAmount = $this->paymentSheetDetails->select('net_amount')
            ->wherePaymentSheetId($paymentSheetDetail->payment_sheet_id)
            ->wherePaymentBillId($paymentSheetDetail->payment_bill_id)
            ->where('id', '!=', $paymentSheetDetail->id)
            ->sum('net_amount');
        $paymentBill = $this->paymentBills->select(['*'])->find($paymentSheetDetail->payment_bill_id);
        $leftAmount = $paymentSheetDetail->paymentBill->bill_amount - $paidAmount;

        $paymentBills = $this->paymentBills->select(['*'])
            ->where(function ($q) use($paymentSheet, $authUser, $paidAmount){
                $q->where('supplier_id', $paymentSheet->supplier_id)
                    ->where('total_amount', '>', $paidAmount)
                    ->whereOfficeId($authUser->employee->office_id);
            })->orWhere('id', $paymentSheetDetail->payment_bill_id)
            ->orderBy('bill_date')->get();

        $accountCodes = $paymentSheetDetail->activityCode ? $paymentSheetDetail->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $offices = $this->offices->getActiveOffices();

        // $bill_percentage = $this->paymentSheetDetails->select('percentage')
        //                         ->wherePaymentSheetId($paymentSheetDetail->payment_sheet_id)
        //                         ->wherePaymentBillId($paymentSheetDetail->payment_bill_id)
        //                         ->sum('percentage');

        // $leftPercentage = 100-$bill_percentage+$paymentSheetDetail->percentage;

        $tdsPercentages = Helper::tdsPercentages();

        return view('PaymentSheet::Detail.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            // ->withLeftPercentage($leftPercentage)
            ->withLeftAmount($leftAmount)
            ->withOffices($offices)
            ->withPaymentBill($paymentBill)
            ->withBillAmount($paymentBill->bill_amount)
            ->withPaymentBills($paymentBills)
            ->withPaymentSheet($paymentSheet)
            ->withPaymentSheetDetail($paymentSheetDetail)
            ->withTdsPercentages($tdsPercentages);
    }

    /**
     * Update the specified payment sheet detail in storage.
     *
     * @param UpdateRequest $request
     * @param $tbId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $tbId, $id)
    {
        $paymentSheet = $this->paymentSheets->find($tbId);
        $paymentSheetDetail = $this->paymentSheetDetails->find($id);
        $this->authorize('update', $paymentSheetDetail->paymentSheet);
        $inputs = $request->validated();
        $inputs['tds_flag'] = isset($request->tds_applicable);
        $paymentSheetDetail = $this->paymentSheetDetails->update($id, $inputs);
        if ($paymentSheetDetail) {
            return response()->json(['status' => 'ok',
                'paymentSheet' => $paymentSheetDetail->paymentSheet,
                'paymentSheetDetail' => $paymentSheetDetail,
                'paymentDetailCount' => $paymentSheet->paymentSheetDetails->count(),
                'message' => 'Payment sheet detail is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Payment sheet detail can not be updated.'], 422);
    }

    /**
     * Remove the specified payment sheet detail from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($tbId, $id)
    {
        $paymentSheetDetail = $this->paymentSheetDetails->find($id);
        $this->authorize('delete', $paymentSheetDetail);
        $flag = $this->paymentSheetDetails->destroy($id);
        if ($flag) {
            $paymentSheet = $this->paymentSheets->find($tbId);
            return response()->json([
                'type' => 'success',
                'paymentSheet' => $paymentSheet,
                'paymentDetailCount' => $paymentSheet->paymentSheetDetails->count(),
                'message' => 'Payment sheet detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'paymentSheet' => $paymentSheetDetail->paymentSheet,
            'message' => 'Payment sheet detail can not deleted.',
        ], 422);
    }
}
