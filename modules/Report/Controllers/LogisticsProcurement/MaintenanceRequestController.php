<?php

namespace Modules\Report\Controllers\LogisticsProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\LogisticsProcurement\MaintenanceRequestExport;
use Yajra\DataTables\DataTables;

class MaintenanceRequestController extends Controller
{
    public function __construct(
        ItemRepository $items,
        OfficeRepository $offices
    ) {
        $this->items = $items;
        $this->offices = $offices;
    }

    public function index(Request $request)
    {
        $data = MaintenanceRequest::query();
        $data->where('status_id', config('constant.APPROVED_STATUS'));

        if ($request->ajax()) {
            if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000);
                $end_date = date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000);
                if ($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                }
            }

            if ($request->has('rm_number') && $request->rm_number) {
                $rmNumber = $request->rm_number;
                $data->where(DB::raw('CONCAT(prefix, maintenance_number)'), 'LIKE', '%' . $rmNumber . '%')
                    ->orWhere(DB::raw('CONCAT_WS("-", prefix, maintenance_number)'), 'LIKE', '%' . $rmNumber . '%');
            }

            if ($request->has('office') && $request->office) {
                $officeId = $request->office;
                $data->whereHas('requester', function ($q) use ($officeId) {
                    $q->whereHas('employee', function ($q) use ($officeId) {
                        $q->whereHas('latestTenure', function ($q) use ($officeId) {
                            $q->where('office_id', $officeId);
                        });
                    });
                });
            }

            if ($request->has('item') && $request->item) {
                $itemId = $request->item;
                $data->where('item_id', $itemId);
            }

            $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('rm_number', function ($row) {
                    return $row->getMaintenanceRequestNumber();
                })
                ->addColumn('office', function ($row) {
                    return $row->requester->getOfficeName();
                })
                ->addColumn('requested_date', function ($row) {
                    return $row->created_at->format('M d, Y');
                })
                ->addColumn('requested_by', function ($row) {
                    return $row->getRequesterName();
                })
                ->addColumn('item_equipment_to_repair', function ($row) {
                    return $row->getItem();
                })
                ->addColumn('assets_code', function ($row) {
                    return $row->item->item_code;
                })
                ->addColumn('problem_service_for', function ($row) {
                    return $row->problem;
                })
                ->addColumn('quantity', function ($row) {
                    return '';
                })
                ->addColumn('total_tentative_cost', function ($row) {
                    return $row->estimated_cost;
                })
                ->addColumn('project', function ($row) {
                    return '';
                })
                ->addColumn('activity_code', function ($row) {
                    return $row->getActivityCode();
                })
                ->addColumn('account_code', function ($row) {
                    return $row->getAccountCode();
                })
                ->addColumn('donor_code', function ($row) {
                    return $row->getDonorCode();
                })
                ->addColumn('remarks', function ($row) {
                    return $row->remarks;
                })
                ->make(true);
        }

        $array = [
            'items' => $this->items->getItems(),
            'offices' => $this->offices->getOffices(),
        ];

        return view('Report::LogisticsProcurement.MaintenanceRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000) : null;
        $rmNumber = $request->rm_number ? $request->rm_number : null;
        $office = $request->office ? $request->office : null;
        $item = $request->item ? $request->item : null;

        return new MaintenanceRequestExport($start_date, $end_date, $rmNumber, $office, $item);
    }
}
