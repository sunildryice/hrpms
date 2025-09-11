<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Requests\ActivityCode\StoreRequest;
use Modules\Master\Requests\ActivityCode\UpdateRequest;

use DataTables;

class ActivityCodeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @return void
     */
    public function __construct(
        AccountCodeRepository $accountCodes,
        ActivityCodeRepository $activityCodes
    )
    {
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
    }

    /**
     * Display a listing of the activity code.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->activityCodes->select([
                'id', 'title', 'description', 'activated_at', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-activity-modal-form" href="';
                    $btn .= route('master.activity.codes.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.activity.codes.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::ActivityCode.index');
    }

    /**
     * Show the form for creating a new activity code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $accountCodes = $this->accountCodes->select(['id', 'title'])->whereNotNull('activated_at')->get();
        return view('Master::ActivityCode.create')
            ->withAccountCodes($accountCodes);
    }

    /**
     * Store a newly created activity code in storage.
     *
     * @param \Modules\Master\Requests\ActivityCode\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inputs['account_codes'] = $request->account_codes ?: [];
        $activityCode = $this->activityCodes->create($inputs);
        if ($activityCode) {
            $message = 'Activity code is successfully added.';
            if($request->isJson()){
                return response()->json(['status' => 'ok',
                    'activityCode' => $activityCode,
                    'message' => $message], 200);
            }
            return redirect()->route('master.activity.codes.index')
                ->withSuccessMessage($message);
        }
        return response()->json(['status' => 'error',
            'message' => 'Activity code can not be added.'], 422);
    }

    /**
     * Display the specified activity code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activityCode = $this->activityCodes->find($id);
        return response()->json(['status' => 'ok', 'activity code' => $activityCode], 200);
    }

    /**
     * Show the form for editing the specified activity code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $activityCode = $this->activityCodes->find($id);
        $accountCodes = $this->accountCodes->select(['id', 'title'])->whereNotNull('activated_at')->get();
        return view('Master::ActivityCode.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCode($activityCode);
    }

    /**
     * Update the specified activity code in storage.
     *
     * @param \Modules\Master\Requests\ActivityCode\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['account_codes'] = $request->account_codes ?: [];
        $activityCode = $this->activityCodes->update($id, $inputs);
        if ($activityCode) {
            $message = 'Activity code is successfully updated.';
            if($request->isJson()){
                return response()->json(['status' => 'ok',
                    'activityCode' => $activityCode,
                    'message' => $message], 200);
            }
            return redirect()->route('master.activity.codes.index')
                ->withSuccessMessage($message);
        }
        return response()->json(['status' => 'error',
            'message' => 'Activity code can not be updated.'], 422);
    }

    /**
     * Remove the specified activity code from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->activityCodes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Activity code is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Activity code can not deleted.',
        ], 422);
    }
}
