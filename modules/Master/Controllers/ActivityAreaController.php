<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ActivityAreaRepository;
use Modules\Master\Requests\ActivityArea\StoreRequest;
use Modules\Master\Requests\ActivityArea\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class ActivityAreaController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ActivityAreaRepository $activityAreas
     * @return void
     */
    public function __construct(
        protected ActivityAreaRepository $activityAreas
    ) {}

    /**
     * Display a listing of the activity code.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->activityAreas->select([
                'id',
                'title',
                'activated_at',
                'created_by',
                'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-area-modal-form" href="';
                    $btn .= route('master.activity.areas.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.activity.areas.destroy', $row->id) . '">';
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
        return view('Master::ActivityArea.index');
    }

    /**
     * Show the form for creating a new activity code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ActivityArea.create');
    }

    /**
     * Store a newly created activity code in storage.
     *
     * @param \Modules\Master\Requests\ActivityArea\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $activityArea = $this->activityAreas->create($inputs);
        if ($activityArea) {
            return response()->json([
                'status' => 'ok',
                'activity code' => $activityArea,
                'message' => 'Activity area is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Activity area can not be added.'
        ], 422);
    }

    /**
     * Display the specified activity code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activityArea = $this->activityAreas->find($id);
        return response()->json(['status' => 'ok', 'activity code' => $activityArea], 200);
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
        $activityArea = $this->activityAreas->find($id);
        return view('Master::ActivityArea.edit')
            ->withActivityArea($activityArea);
    }

    /**
     * Update the specified activity code in storage.
     *
     * @param \Modules\Master\Requests\ActivityArea\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $activityArea = $this->activityAreas->update($id, $inputs);
        if ($activityArea) {
            return response()->json([
                'status' => 'ok',
                'activityArea' => $activityArea,
                'message' => 'Activity area is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Activity area can not be updated.'
        ], 422);
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
        $flag = $this->activityAreas->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Activity area is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Activity area can not deleted.',
        ], 422);
    }
}
