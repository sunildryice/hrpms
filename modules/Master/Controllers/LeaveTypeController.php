<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;

use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Requests\LeaveType\StoreRequest;
use Modules\Master\Requests\LeaveType\UpdateRequest;

class LeaveTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param LeaveTypeRepository $leaveTypes
     * @return void
     */
    public function __construct(
        FiscalYearRepository $fiscalYears,
        LeaveTypeRepository  $leaveTypes
    )
    {
        $this->fiscalYears = $fiscalYears;
        $this->leaveTypes = $leaveTypes;
    }

    /**
     * Display a listing of the leaveType.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->leaveTypes->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-leave-type-modal-form" href="';
                    $btn .= route('master.leave.types.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.leave.types.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('number_of_days', function ($row) {
                    return $row->number_of_days . '/' . $row->getLeaveFrequency();
                })->addColumn('paid_status', function ($row) {
                    return $row->getPaidStatus();
                })->addColumn('weekend_status', function ($row) {
                    return $row->getIncludeWeekendsStatus();
                })->addColumn('applicable_to_all_status', function ($row) {
                    return $row->getApplicableToAllStatus();
                })->addColumn('encashment_status', function ($row) {
                    return $row->getEncashmentStatus();
                })->addColumn('female_status', function ($row) {
                    return $row->getFemaleStatus();
                })->addColumn('male_status', function ($row) {
                    return $row->getMaleStatus();
                })->addColumn('status', function ($row) {
                    return $row->getActiveStatus();
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::LeaveType.index')
            ->withLeaveTypes($this->leaveTypes->all());
    }

    /**
     * Show the form for creating a new leaveType.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        return view('Master::LeaveType.create')
            ->withFiscalYears($this->fiscalYears->get());
    }

    /**
     * Store a newly created leaveType in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['default'] = $request->default ? 1 : 0;
        $inputs['male'] = $request->male ? 1 : 0;
        $inputs['include_weekends'] = $request->include_weekends ? 1 : 0;
        $inputs['female'] = $request->female ? 1 : 0;
        $inputs['applicable_to_all'] = $request->applicable_to_all ? 1 : 0;
        $inputs['encashment'] = $request->encashment ? 1 : 0;
        $inputs['number_of_days'] = $request->number_of_days ?: 0;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : NULL;
        $inputs['created_by'] = auth()->id();
        $leaveType = $this->leaveTypes->create($inputs);
        if ($leaveType) {
            return response()->json(['status' => 'ok',
                'leaveType' => $leaveType,
                'message' => 'Leave type is added successfully.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Leave type can not be added.'], 422);
    }

    /**
     * Display the specified leaveType.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leaveType = $this->leaveTypes->find($id);
        return response()->json(['status' => 'ok', 'leaveType' => $leaveType], 200);
    }

    /**
     * Show the form for editing the specified leaveType.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $leaveType = $this->leaveTypes->find($id);
        return view('Master::LeaveType.edit')
            ->withLeaveType($leaveType)
            ->withFiscalYears($this->fiscalYears->get());
    }

    /**
     * Update the specified leaveType in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['default'] = $request->default ? 1 : 0;
        $inputs['male'] = $request->male ? 1 : 0;
        $inputs['include_weekends'] = $request->include_weekends ? 1 : 0;
        $inputs['female'] = $request->female ? 1 : 0;
        $inputs['applicable_to_all'] = $request->applicable_to_all ? 1 : 0;
        $inputs['encashment'] = $request->encashment ? 1 : 0;
        $inputs['number_of_days'] = $request->number_of_days ?: 0;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : NULL;
        $inputs['updated_by'] = auth()->id();
        $leaveType = $this->leaveTypes->update($id, $inputs);

        if ($leaveType) {
            return response()->json(['status' => 'ok',
                'leaveType' => $leaveType,
                'message' => 'Leave type is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Leave type can not be updated.'], 422);
    }

    /**
     * Remove the specified leaveType from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $flag = $this->leaveTypes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Leave type is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Leave type can not deleted.',
        ], 422);
    }
}
