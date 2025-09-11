<?php

namespace Modules\ConstructionTrack\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ConstructionTrack\Requests\ConstructionInstallment\StoreRequest;
use Modules\ConstructionTrack\Requests\ConstructionInstallment\UpdateRequest;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\ConstructionTrack\Repositories\ConstructionInstallmentRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\ConstructionTrack\Notifications\InstallmentSubmitted;
use Modules\ConstructionTrack\Requests\ConstructionInstallmentSubmit\StoreRequest as InstallmentSubmitStoreRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Master\Repositories\TransactionTypeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ConstructionInstallmentController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ConstructionRepository $constructions
     * @param ConstructionInstallmentRepository $installments
     * @param FiscalYearRepository $installments
     */
    public function __construct(
        ConstructionRepository              $constructions,
        ConstructionInstallmentRepository   $installments,
        DistrictRepository                  $districts,
        EmployeeRepository                  $employees,
        FiscalYearRepository                $fiscalYears,
        LocalLevelRepository                $localLevels,
        ProvinceRepository                  $provinces,
        TransactionTypeRepository           $transactionTypes,
        UserRepository                      $users
    )
    {

        $this->constructions    = $constructions;
        $this->installments     = $installments;
        $this->districts        = $districts;
        $this->employees        = $employees;
        $this->fiscalYears      = $fiscalYears;
        $this->localLevels      = $localLevels;
        $this->provinces        = $provinces;
        $this->transactionTypes = $transactionTypes;
        $this->users            = $users;
    }

    /**
     * Display a listing of the construction installment
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $constructionId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $lastData = $this->installments->orderby('id','DESC')->where('construction_id','=',$constructionId)->first(); 
            $data = $this->installments->orderBy('id','asc')->whereConstructionId($constructionId)->get();

            $datatable = DataTables::of($data)
            ->addColumn('amount', function ($row) {
                return $row->amount;
            })
            ->addColumn('advance_release_date', function ($row) {
                return $row->advance_release_date->format('d M, Y');
            })
            ->addColumn('transaction_type', function ($row) {
                return $row->transactionType->title;
            })
            ->addColumn('remarks', function ($row) {
                return $row->remarks;
            })
            ->addColumn('action', function ($row) use ($authUser, $lastData) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-settlement-modal-form" href="';
                // if($row->id == $lastData->id){
                    if ($authUser->can('edit', $row)) {
                        $btn .= route('construction.installment.edit', [$row->construction_id, $row->id]) . '" rel="tooltip" title="Edit Installment"><i class="bi-pencil-square"></i></a>';
                    } 
                    if ($authUser->can('submit', $row)) {
                        $btn .= '&emsp;<a href = "'.route('construction.installment.submit.create', $row->id).'" class="btn btn-outline-primary btn-sm" rel="tooltip" title="Submit Installment">';
                        $btn .= '<i class="bi bi-box-arrow-right"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-outline-danger btn-sm delete-record" rel="tooltip" title="Delete Installment" ';
                        $btn .= 'data-href="' . route('construction.installment.destroy', [$row->construction_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                // }
                return $btn;
            })
            ->with('fund_transferred_total', function() use($data) {
                return $data->where('transaction_type_id', '=', config('constant.FUND_TRANSFERRED'))->sum('amount');
            })
            ->with('expense_settled_total', function() use($data) {
                return $data->where('transaction_type_id', '=', config('constant.EXPENSE_SETTLED'))->sum('amount');
            });
           

            return $datatable->rawColumns(['action'])
            ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new construction request detail by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $transactionTypes = $this->transactionTypes->get();
        return view('ConstructionTrack::ConstructionInstallments.create')
            ->withConstruction($construction)
            ->withTransactionTypes($transactionTypes);
    }

    /**
     * Store a newly created construction Installment in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        // $this->authorize('create-advance-request');
        $inputs = $request->validated();
        $inputs['construction_id'] = $construction->id;
        $inputs['created_by'] = $authUser->id;
        $inputs['installment_number'] = $this->installments->getInstallmentNumber($construction->id);
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first(); 
        $inputs['fiscal_year_id'] = $fiscalYear->id;    
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null; 
        $installments = $this->installments->create($inputs);

        if ($installments) {
            return response()->json(['status' => 'ok',
                'installments' => $installments,
                'message' => 'Construction Installment is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Construction Installment can not be added.'], 422);

    }


    /**
     * Show the form for editing the specified Construction Installments.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($prId);
        $installments = $this->installments->find($id);
        $transactionTypes = $this->transactionTypes->get();
        // $this->authorize('update', $constructions);

        return view('ConstructionTrack::ConstructionInstallments.edit')
            ->withConstructionInstallments($installments)
            ->withConstruction($construction)
            ->withTransactionTypes($transactionTypes);
    }


    /**
     * Update the specified Construction Installments in storage.
     *
     * @param \Modules\ConstructionTrack\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $authUser = auth()->user();
        $installments = $this->installments->find($id);
        // $this->authorize('update', $advanceRequestsDetail->advanceRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first(); 
        $inputs['fiscal_year_id'] = $fiscalYear->id;  
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;   
        $installments = $this->installments->update($id, $inputs);
        if ($installments) {
            return response()->json(['status' => 'ok',
                'installments' => $installments,
                'message' => 'Construction Installments is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Construction Installments can not be updated.'], 422);
    }


    /**
     * Remove the specified Construction Installments from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        $installments = $this->installments->find($id);
        // $this->authorize('delete', $installments->advanceRequest);
        $flag = $this->installments->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Construction Installments is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Construction Installments can not deleted.',
        ], 422);
    }

    public function totalAmount(Request $request)
    {
        $construction = $this->constructions->find($request->constructionId);
        $amount = $construction->getTotalInstallmentAmount();
        return response()->json(['sum' => $amount], 200);
    }

    public function createSubmit($installmentId)
    {
        $installment = $this->installments->find($installmentId);

        $this->authorize('submit', $installment);
        
        $array = [
            'authUser'      => auth()->user(),
            'reviewers'     => $this->users->permissionBasedUsers('verify-installment'),
            'construction'  => $this->constructions->find($installment->construction_id),
            'districts'     => $this->districts->get(),
            'employees'     => $this->employees->getActiveEmployees(),
            'installment'   => $installment,
            'localLevels'   => $this->localLevels->orderBy('local_level_name', 'asc')->get(),
            'provinces'     => $this->provinces->get()
        ];

        return view('ConstructionTrack::ConstructionInstallmentSubmit.create', $array);
    }

    public function submit(InstallmentSubmitStoreRequest $request, $installmentId)
    {
        $installment = $this->installments->find($installmentId);
        $this->authorize('submit', $installment);

        $inputs = $request->validated();

        $installment = $this->installments->submit($installmentId, $inputs);

        if ($installment) {
            $message = 'Construction installment is successfully submitted.';
            $installment->reviewer->notify(new InstallmentSubmitted($installment));
            return redirect()->route('construction.show', $installment->construction_id)->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()->withWarningMessage('Construction installment can not be submitted.');
    }


}
