<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ConstructionTrack\Models\ConstructionInstallment;
use Modules\ConstructionTrack\Notifications\InstallmentApproved;
use Modules\ConstructionTrack\Notifications\InstallmentReturned;
use Modules\ConstructionTrack\Repositories\ConstructionInstallmentRepository;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\ConstructionTrack\Requests\ConstructionInstallmentApprove\StoreRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ConstructionInstallmentApproveController extends Controller{
    public function __construct(
        ConstructionInstallmentRepository   $installments,
        ConstructionRepository              $constructions,
        DistrictRepository                  $districts,
        EmployeeRepository                  $employees,
        LocalLevelRepository                $localLevels,
        ProvinceRepository                  $provinces,
        UserRepository                      $users
    )
    {
        $this->installments     = $installments;
        $this->constructions    = $constructions;
        $this->districts        = $districts;
        $this->employees        = $employees;
        $this->localLevels      = $localLevels;
        $this->provinces        = $provinces;
        $this->users            = $users;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->installments->where('approver_id', '=', $authUser->id)
                                        ->where('status_id', '=', config('constant.VERIFIED_STATUS'))
                                        ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('health_facility_name', function ($row) {
                return $row->construction->health_facility_name;
            })
            ->addColumn('cluster', function ($row) {
                return $row->construction->getOfficeName();
            })
            ->addColumn('district', function ($row) {
                return $row->construction->getDistrictName();
            })
            ->addColumn('local_level', function ($row) {
                return $row->construction->getLocalName();
            })
            ->addColumn('installment_number', function ($row) {
                return $row->installment_number;
            })
            ->addColumn('installment_amount', function ($row) {
                return $row->amount;
            })
            ->addColumn('advance_release_date', function ($row) {
                return $row->getAdvanceReleaseDate();
            })
            ->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('construction.installment.approve.create', $row->id).'" rel="tooltip" title="Approve Construction Installment"><i class="bi bi-eye"></i></a>';    
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
        }

        return view('ConstructionTrack::ConstructionInstallmentApprove.index');
    }

    public function create(Request $request, $installmentId)
    {
        $installment = $this->installments->find($installmentId);

        $this->authorize('approve', $installment);
        
        $array = [
            'authUser'      => auth()->user(),
            'approvers'     => $this->users->permissionBasedUsers('approve-installment'),
            'construction'  => $this->constructions->find($installment->construction_id),
            'districts'     => $this->districts->get(),
            'employees'     => $this->employees->getActiveEmployees(),
            'installment'   => $installment,
            'localLevels'   => $this->localLevels->orderBy('local_level_name', 'asc')->get(),
            'provinces'     => $this->provinces->get()
        ];

        return view('ConstructionTrack::ConstructionInstallmentApprove.create', $array);
    }

    public function store(StoreRequest $request, $installmentId)
    {
        $installment = $this->installments->find($installmentId);
        $this->authorize('approve', $installment);

        $inputs = $request->validated();

        $inputs['user_id']          = auth()->user()->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['status_id']        = $request->status_id;
        $inputs['log_remarks']      = $request->remarks;

        $installment = $this->installments->approve($installmentId, $inputs);

        if ($installment) {
            $message = '';
            if ($installment->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Construction installment is successfully returned.';
                $installment->requester->notify(new InstallmentReturned($installment));
            } else {
                $message = 'Construction installment is successfully approved.';
                $installment->requester->notify(new InstallmentApproved($installment));
            }
            return redirect()->route('construction.installment.review.index')->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()->withWarningMessage('Construction installment can not be approved.');
    }
}