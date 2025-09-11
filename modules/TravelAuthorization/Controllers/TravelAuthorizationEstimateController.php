<?php

namespace Modules\TravelAuthorization\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;

use Modules\TravelAuthorization\Requests\Estimate\StoreRequest;
use Modules\TravelAuthorization\Requests\Estimate\UpdateRequest;

use DataTables;
use Modules\TravelAuthorization\Models\TravelAuthorization;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationEstimateRepository;

class TravelAuthorizationEstimateController extends Controller
{
    protected $destinationPath;

    public function __construct(
        protected AccountCodeRepository            $accountCodes,
        protected ActivityCodeRepository           $activityCodes,
        protected DonorCodeRepository              $donorCodes,
        protected EmployeeRepository      $employees,
        protected TravelAuthorization $travel,
        protected TravelAuthorizationEstimateRepository $travelEstimate,
        protected RoleRepository          $roles,
        protected UserRepository          $user
    )
    {
        $this->destinationPath  = 'travelAuthorization';
    }

    public function index(Request $request, $taId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travel = $this->travel->find($taId);
            $data = $this->travelEstimate->select(['*'])
                ->where('travel_authorization_id',$taId);

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('activity_code', function ($row) {
                    return $row->getActivityCode();
                })->addColumn('donor_code', function ($row) {
                    return $row->getDonorCode();
                })->addColumn('account_code', function ($row) {
                    return $row->getAccountCode();
                })
                ->addColumn('total_amount', function ($row) {
                    return round($row->quantity * $row->unit_price * $row->days, 2);
                });
            if ($authUser->can('update', $travel)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $travel) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-estimation-modal-form" href="';
                    $btn .= route('ta.requests.estimate.edit', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Edit Travel Cost Estimation"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('ta.requests.estimate.destroy', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Delete Travel Cost Estimation">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

   public function create($taId)
    {
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        $prevEstimate = $travel->latestEstimate;
        return view('TravelAuthorization::Estimate.create')
            ->withActivityCodes($this->activityCodes->getActiveActivityCodes())
            ->withDonorCodes($this->donorCodes->getActiveDonorCodes())
            ->withPrevEstimate($prevEstimate)
            ->withTravel($travel);
    }

   public function store(StoreRequest $request, $taId)
    {
        $inputs = $request->validated();
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        $inputs['travel_authorization_id'] = $travel->id;
        $inputs['total_price'] = $inputs['quantity'] * $inputs['unit_price'] * $inputs['days'];
        $inputs['created_by'] = auth()->id();
        $travelEstimate = $this->travelEstimate->create($inputs);
        if($travelEstimate){
            $estimate = $this->travelEstimate->find($travelEstimate->id);
            return response()->json(['status' => 'ok',
                'travelEstimate' => $estimate,
                'estimatesCount'=>$travelEstimate->travelAuthorization->estimates()->count(),
                'message' => 'Travel Estimate is successfully updated.'], 200);
        }
        return response()->json(['status'=>'error',
            'message'=>'Travel Estimate can not be added.'], 422);
    }

   public function edit($taId, $id)
    {
        $travelEstimate = $this->travelEstimate->find($id);
        $this->authorize('update', $travelEstimate->travelAuthorization);
        return view('TravelAuthorization::Estimate.edit')
            ->withActivityCodes($this->activityCodes->getActiveActivityCodes())
            ->withDonorCodes($this->donorCodes->getActiveDonorCodes())
            ->withAccountCodes($travelEstimate->activityCode->accountCodes)
            ->withEstimate($travelEstimate);
    }


    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $travelEstimate = $this->travelEstimate->find($id);
        $this->authorize('update', $travelEstimate->travelAuthorization);
        $inputs['total_price'] = $inputs['quantity'] * $inputs['unit_price'] * $inputs['days'];
        $inputs['created_by'] = auth()->id();
        $travelEstimate = $this->travelEstimate->update($id, $inputs);
        if($travelEstimate){
            $estimate = $this->travelEstimate->find($travelEstimate->id);
            return response()->json(['status' => 'ok',
                'travelEstimate' => $estimate,
                'estimatesCount'=>$travelEstimate->travelAuthorization->estimates()->count(),
                'message' => 'Travel Estimate is successfully updated.'], 200);
        }
        return response()->json(['status'=>'error',
            'message'=>'Travel Estimate can not be added.'], 422);
    }

   public function destroy($travelReqyestId, $id)
    {
        $travelEstimate = $this->travelEstimate->find($id);
        $this->authorize('delete', $travelEstimate->travelAuthorization);
        $flag = $this->travelEstimate->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'estimatesCount'=>$travelEstimate->travelAuthorization->estimates()->count(),
                'message' => 'Travel estimate is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel estimate can not deleted.',
        ], 422);
    }
}
