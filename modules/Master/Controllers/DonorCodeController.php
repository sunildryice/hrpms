<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Requests\DonorCode\StoreRequest;
use Modules\Master\Requests\DonorCode\UpdateRequest;

use DataTables;

class DonorCodeController extends Controller
{
    /**
     * The donor code repository instance.
     *
     * @var DonorCodeRepository
     */
    protected $donorCodes;

    /**
     * Create a new controller instance.
     *
     * @param DonorCodeRepository $donorCodes
     * @return void
     */
    public function __construct(
        DonorCodeRepository $donorCodes,
        AttendanceRepository $attendances
    )
    {
        $this->attendances = $attendances;
        $this->donorCodes = $donorCodes;
    }

    /**
     * Display a listing of the donor code.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->donorCodes->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-donor-modal-form" href="';
                    $btn .= route('master.donor.codes.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.donor.codes.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('attendance_enable', function ($row) {
                    return $row->getAttendanceEnable();
                }) ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::DonorCode.index')
            ->withDonorCodes($this->donorCodes->all());
    }

    /**
     * Show the form for creating a new donor code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::DonorCode.create');
    }

    /**
     * Store a newly created donor code in storage.
     *
     * @param \Modules\Master\Requests\DonorCode\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['attendance_enable_at'] = $request->attendance_enable ? date('Y-m-d H:i:s') : NULL;
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $donorCode = $this->donorCodes->create($inputs);
        if ($donorCode) {
            return response()->json(['status' => 'ok',
                'donor code' => $donorCode,
                'message' => 'Donor code is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Donor code can not be added.'], 422);
    }

    /**
     * Display the specified donor code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $donorCode = $this->donorCodes->find($id);
        return response()->json(['status' => 'ok', 'donorCode' => $donorCode], 200);
    }

    /**
     * Show the form for editing the specified donor code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $donorCode = $this->donorCodes->find($id);
        return view('Master::DonorCode.edit')
            ->withDonorCode($donorCode);
    }

    /**
     * Update the specified donor code in storage.
     *
     * @param \Modules\Master\Requests\DonorCode\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $donorCode = $this->donorCodes->find($id);
        $attendanceEnable = $donorCode->attendance_enable_at;
        $inputs['attendance_enable_at'] = $request->attendance_enable ? date('Y-m-d H:i:s') : NULL;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : NULL;
        $donorCode = $this->donorCodes->update($id, $inputs);
        if ($donorCode) {
            return response()->json(['status' => 'ok',
                'donor code' => $donorCode,
                'message' => 'Donor code is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Donor code can not be updated.'], 422);
    }

    /**
     * Remove the specified donor code from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->donorCodes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Donor code is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Donor code can not deleted.',
        ], 422);
    }
}
