<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;
use Modules\Report\Exports\LogisticsProcurement\PurchaseRequestExport;

use Yajra\DataTables\DataTables;

class PurchaseRequestController extends Controller
{
    public function __construct(
        EmployeeRepository  $employees,
        ItemRepository      $items,
        OfficeRepository    $offices
    )
    {
        $this->employees    = $employees; 
        $this->items        = $items;
        $this->offices      = $offices;       
    }

    public function index(Request $request)
    {
        $data = PurchaseRequestItem::query();

        $data->with('purchaseRequest')
            ->whereHas('purchaseRequest', function($q) {
                $q->whereNot('approver_id', null);
            });
        
        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereHas('purchaseRequest', function($q) use($start_date,$end_date) {
                        $q->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                    });
                }
            }

            if($request->has('pr_number') && $request->pr_number) {
                $prNumber = $request->pr_number;
                $data->whereHas('purchaseRequest', function($q) use($prNumber) {
                    $q->where(DB::raw('CONCAT(prefix, purchase_number)'), 'LIKE', "%$prNumber%");
                    $q->orWhere(DB::raw('CONCAT_WS("-", prefix, purchase_number)'), 'LIKE', "%{$prNumber}%");
                });
            }

            if($request->has('office') && $request->office) {
                $office_id = $request->office;
                $data->whereHas('purchaseRequest', function($q) use($office_id) {
                    $q->where('office_id', $office_id);
                });
            }

            if($request->has('requester') && $request->requester) {
                $requesterUserId = $request->requester;
                $data->whereHas('purchaseRequest', function($q) use($requesterUserId) {
                    $q->where('requester_id', $requesterUserId);
                });
            }

            if($request->has('particulars') && $request->particulars) {
                $itemId = $request->particulars;
                $data->where('item_id', $itemId);
            }

            $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('purchase_request_number', function ($row) {
                    return $row->purchaseRequest->getPurchaseRequestNumber();
            })
            ->addColumn('office', function ($row){
                return $row->purchaseRequest->office->office_name;
            })
            ->addColumn('requested_date', function ($row){
                return $row->purchaseRequest->request_date->format('Y-m-d');
            })
            ->addColumn('requested_by', function ($row){
                return $row->purchaseRequest->requester->full_name;
            })
            ->addColumn('required_date', function ($row){
                return $row->purchaseRequest->required_date->format('Y-m-d');
            })
            ->addColumn('particulars', function ($row){
                return $row->item->title;
            })
            ->addColumn('quantity', function ($row){
                return $row->quantity;
            })
            ->addColumn('tentative_cost', function ($row){
                return $row->total_price;
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
            ->addColumn('remarks', function ($row){
                return $row->remarks;
            })
            
            ->make(true);
        }

        $array = [
            'offices'    => $this->offices->getActiveOffices(),
            'employees'  => $this->employees->getActiveEmployees(),
            'items'      => $this->items->getActiveItems()
        ];

        return view('Report::LogisticsProcurement.PurchaseRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date         = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date           = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $pr_number          = $request->pr_number ? $request->pr_number : null;
        $office             = $request->office ? $request->office : null;
        $requester_user_id  = $request->requester ? $request->requester : null;
        $item_id            = $request->particulars ? $request->particulars : null;

        return new PurchaseRequestExport($start_date, $end_date, $office, $pr_number, $requester_user_id, $item_id);
    }
}
