<?php

namespace Modules\Supplier\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Supplier\Repositories\SupplierRepository;

class SupplierController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  SupplierRepository $suppliers
     * @return void
     */
    public function __construct(
        SupplierRepository $suppliers
    )
    {
        $this->suppliers = $suppliers;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'suppliers'=>$this->suppliers->get()
        ], 200);
    }

    /**
     * Display the specified province.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $supplier = $this->suppliers->find($id);
        return response()->json([
            'purchaseOrders'=>$supplier->purchaseOrders()->select(['id','prefix','order_number','status_id'])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->orderBy('order_number', 'desc')->get(),
            'supplier'=>$supplier
        ], 200);
    }
}
