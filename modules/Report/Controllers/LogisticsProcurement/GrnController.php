<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Grn\Models\GrnItem;
use Modules\Master\Models\Office;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\LogisticsProcurement\GrnExport;
use Modules\Supplier\Repositories\SupplierRepository;
use Yajra\DataTables\DataTables;

class GrnController extends Controller
{
    public function __construct(
        ItemRepository      $items,
        OfficeRepository    $offices,
        SupplierRepository  $suppliers,
    )
    {
        $this->items        = $items;
        $this->offices      = $offices;
        $this->suppliers    = $suppliers;
    }
    public function index(Request $request)
    {
        $data = GrnItem::query();

        $data->with('grn')
            ->whereHas('grn', function($q) {
                $q->whereNot('approver_id', null);
            });

        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereHas('grn', function($q) use($start_date,$end_date) {
                        $q->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                    });
                }
            }

            if($request->has('office') && $request->office) {
                $office_id = $request->office;
                $data->whereHas('grn', function($q) use($office_id) {
                    $q->where('office_id', $office_id);
                });
            }

            if ($request->has('grn_number') && $request->grn_number) {
                $grnNumber = $request->grn_number;
                $data->whereHas('grn', function($q) use($grnNumber) {
                    $q->where(DB::raw('CONCAT(prefix, grn_number)'), 'LIKE', "%$grnNumber%");
                    $q->orWhere(DB::raw('CONCAT_WS("-", prefix, grn_number)'), 'LIKE', "%{$grnNumber}%");
                });
            }

            if($request->has('po_number') && $request->po_number) {
                $poNumber = $request->po_number;
                $data->whereHas('grn', function($q) use($poNumber) {
                    $q->whereHas('purchaseOrder', function($q) use($poNumber) {
                        $q->where(DB::raw('CONCAT(prefix, order_number)'), 'LIKE', "%$poNumber%");
                        $q->orWhere(DB::raw('CONCAT_WS("-", prefix, order_number)'), 'LIKE', "%{$poNumber}%");
                    });
                });
            }

            if ($request->has('vendor') && $request->vendor) {
                $supplierId = $request->vendor;
                $data->whereHas('grn', function($q) use($supplierId) {
                    $q->where('supplier_id', $supplierId);
                });
            }

            if ($request->has('item') && $request->item) {
                $itemId = $request->item;
                $data->where('item_id', $itemId);
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('grn_number', function ($row){
                return $row->getGrnNumber();
            })
            ->addColumn('office', function ($row){
                return $row->grn->office->office_name;
            })
            ->addColumn('purchase_order_number', function ($row){
                return $row->getPONo();
            })
            ->addColumn('purchase_request_number', function ($row){
                return $row->getPRNo();
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
                return $row->donorCode->getDonorCodeWithDescription();
            })
            ->addColumn('vendor_name', function ($row){
                return $row->grn->supplier->supplier_name;
            })
            ->addColumn('address', function ($row){
                return $row->grn->supplier->address1;
            })
            ->addColumn('item', function ($row){
                return $row->item->title;
            })
            ->addColumn('description', function ($row){
                return $row->item->category->description;
            })
            ->addColumn('inventory_type', function ($row){
                return $row->item->category->inventoryType->title;
            })
            ->addColumn('item_category', function ($row){
                return $row->item->category->title;
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
            ->addColumn('receiver', function ($row){
                return '';
            })
            ->addColumn('received_date', function ($row){
                return $row->grn->received_date->format('Y-m-d');
            })
            ->make(true);
        }

        $array = [
            'items'     => $this->items->getActiveItems(),
            'offices' => $this->offices->getActiveOffices(),
            'suppliers' => $this->suppliers->getActiveSuppliers()
        ];

        return view('Report::LogisticsProcurement.Grn.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $office = $request->office ? $request->office : null;
        $grnNumber = $request->grn_number ? $request->grn_number : null;
        $poNumber = $request->po_number ? $request->po_number : null;
        $vendor = $request->vendor ? $request->vendor : null;
        $item = $request->item ? $request->item : null;

        return new GrnExport($start_date, $end_date, $office, $grnNumber, $poNumber, $vendor, $item);
    }
}
