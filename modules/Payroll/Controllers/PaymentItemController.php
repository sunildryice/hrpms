<?php

namespace Modules\Payroll\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Payroll\Repositories\PaymentItemRepository;

use DataTables;

class PaymentItemController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param PaymentItemRepository $paymentItems
     * @return void
     */
    public function __construct(
        PaymentItemRepository $paymentItems
    )
    {
        $this->paymentItems = $paymentItems;
    }

    /**
     * Display a listing of the payment item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->paymentItems->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Payroll::PaymentItem.index');
    }
}
