<?php

namespace Modules\PaymentSheet\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Http\Controllers\Controller;

use Modules\PaymentSheet\Requests\Payment\StoreRequest;
use Modules\PaymentSheet\Repositories\PaymentSheetRepository;
use Modules\PaymentSheet\Notifications\PaymentSheetPaid;

class PaymentController extends Controller
{
  
    private $paymentSheet;
    
    public function __construct(
        PaymentSheetRepository     $paymentSheet,
   
    )
    {
        $this->paymentSheet = $paymentSheet;
       
    }

    public function index(Request $request){
        $authUser = auth()->user();
        if($request->ajax()){
            $data = $this->paymentSheet->getPaid();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier', function ($row){
                    return $row->getSupplierName();
                })->addColumn('vat_pan_number', function ($row){
                    return $row->getSupplierVatPanNumber();
                })->addColumn('payment_sheet_number', function ($row){
                    return $row->getPaymentSheetNumber();
                })->addColumn('prepared_by', function ($row){
                    return $row->requester->getFullName();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('paid.payment.sheets.show', $row->id) . '" rel="tooltip" title="View  Payment Sheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.payment.sheets.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('PaymentSheet::Paid.index');
    }

    public function show($Id)
    {
        $paymentSheet = $this->paymentSheet->find($Id);
        $this->authorize('viewApproved', $paymentSheet);
        return view('PaymentSheet::Paid.show')
            ->withPaymentSheet($paymentSheet);
    }

    public function create($id){
        $authUser = auth()->user();
        $paymentSheet = $this->paymentSheet->find($id);
        $this->authorize('pay', $paymentSheet);
        return view('PaymentSheet::Payment.create')
                ->withPaymentSheet($paymentSheet);
    }

    public function store(StoreRequest $request, $id){
        $paymentSheet = $this->paymentSheet->find($id);
        $this->authorize('pay', $paymentSheet);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $paymentSheet = $this->paymentSheet->pay($id,$inputs);
        if($paymentSheet){
            $paymentSheet->requester->notify(new PaymentSheetPaid($paymentSheet));
            return response()->json([
                'status' => 'ok',
                'message' => 'Payment is successfully made.'
            ],200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Payment can not be made.'
        ],422);
    }

    }

  

  

    
