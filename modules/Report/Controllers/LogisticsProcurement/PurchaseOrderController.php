<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;
use Modules\Report\Exports\LogisticsProcurement\PurchaseOrderExport;
use Modules\Supplier\Repositories\SupplierRepository;
use Yajra\DataTables\DataTables;

class PurchaseOrderController extends Controller
{
    public function __construct(
        OfficeRepository $offices,
        SupplierRepository $suppliers,
        ItemRepository $items
    )
    {
        $this->offices = $offices;
        $this->suppliers = $suppliers;
        $this->items = $items;
    }

    public function index(Request $request)
    {
        $data = PurchaseOrderItem::query();

        $data->with('purchaseOrder')
            ->whereHas('purchaseOrder', function($q) {
                $q->whereNot('approver_id', null);
            });
        
        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereHas('purchaseOrder', function($q) use($start_date,$end_date) {
                        $q->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                    });
                }
            }

            if($request->has('po_number') && $request->po_number) {
                $poNumber = $request->po_number;
                $data->whereHas('purchaseOrder', function($q) use($poNumber) {
                    $q->where(DB::raw('CONCAT(prefix, order_number)'), 'LIKE', "%$poNumber%");
                    $q->orWhere(DB::raw('CONCAT_WS("-", prefix, order_number)'), 'LIKE', "%{$poNumber}%");
                });
            }

            if($request->has('office') && $request->office) {
                $office_id = $request->office;
                $data->whereHas('purchaseOrder', function($q) use($office_id) {
                    $q->where('office_id', $office_id);
                });
            }

            if($request->has('vendor') && $request->vendor) {
                $supplierId = $request->vendor;
                $data->whereHas('purchaseOrder', function($q) use($supplierId) {
                    $q->where('supplier_id', $supplierId);
                });
            }

            if($request->has('item') && $request->item) {
                $itemId = $request->item;
                $data->where('item_id', $itemId);
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('purchase_order_number', function ($row){
                return $row->purchaseOrder->prefix.'-'.$row->purchaseOrder->order_number;
            })
            ->addColumn('purchase_order_date', function ($row){
                return $row->purchaseOrder->order_date->format('Y-m-d');
            })
            ->addColumn('office', function ($row){
                return $row->purchaseOrder->office->office_name;
            })
            ->addColumn('vendor_name', function ($row){
                return $row->purchaseOrder->supplier->supplier_name;
            })
            ->addColumn('items', function ($row){
                return $row->item->title;
            })
            ->addColumn('unit', function ($row){
                return $row->unit->title;
            })
            ->addColumn('quantity', function ($row){
                return $row->quantity;
            })
            ->addColumn('rate', function ($row){
                return $row->unit_price;
            })
            ->addColumn('amount', function ($row){
                return $row->total_price;
            })
            ->addColumn('vat', function ($row){
                return $row->vat_amount;
            })
            ->addColumn('total_amount', function ($row){
                return $row->total_amount;
            })
            ->addColumn('project', function ($row){
                return '';
            })
            ->addColumn('activity_code', function ($row){
                return $row->activityCode->title;
            })
            ->addColumn('account_code', function ($row){
                return $row->accountCode->title;
            })
            ->addColumn('donor_code', function ($row){
                return $row->donorCode->title;
            })
            ->addColumn('delivery_date', function ($row){
                return $row->delivery_date;
            })
            ->addColumn('purchase_request_number', function ($row){
                return $row->purchaseRequestItem->purchaseRequest->prefix.'-'.$row->purchaseRequestItem->purchaseRequest->purchase_number;
            })
            ->addColumn('bill_number', function ($row){
                return '';
            })
            ->addColumn('payment_status', function ($row){
                return $row->purchaseOrder->status->title;
            })
            ->addColumn('requested_final_remarks', function ($row){
                return $row->remarks;
            })
            ->make(true);
        }

        $array = [
            'offices'    => $this->offices->getActiveOffices(),
            'suppliers'  => $this->suppliers->getActiveSuppliers(),
            'items'      => $this->items->getActiveItems()
        ];

        return view('Report::LogisticsProcurement.PurchaseOrder.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date   = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $po_number  = $request->po_number ? $request->po_number : null;
        $office     = $request->office ? $request->office : null;
        $vendor     = $request->vendor ? $request->vendor : null;
        $item       = $request->item ? $request->item : null;

        return new PurchaseOrderExport($start_date, $end_date, $po_number, $office, $vendor, $item);
    }
}
