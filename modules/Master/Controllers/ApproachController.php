<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\ApproachRepository;
use Modules\Master\Requests\Approach\StoreRequest;
use Modules\Master\Requests\Approach\UpdateRequest;
use DataTables;

class ApproachController extends Controller
{
    protected $approaches;

    public function __construct(ApproachRepository $approaches)
    {
        $this->approaches = $approaches;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->approaches->select([
                'id', 'title', 'created_by', 'updated_at'
            ])->orderBy('title');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-approach-modal-form" href="';
                    $btn .= route('master.approaches.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.approaches.destroy', $row->id) . '">';
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

        return view('Master::Approach.index')
            ->withApproachs($this->approaches->all());
    }

    public function create()
    {
        return view('Master::Approach.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();

        $approach = $this->approaches->create($inputs);

        if ($approach) {
            return response()->json(['status' => 'ok',
                'sector' => $approach,
                'message' => 'Approach is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Approach can not be added.'], 422);
    }

    public function show($id)
    {
        $approach = $this->approaches->find($id);
        return response()->json(['status' => 'ok', 'sector' => $approach], 200);
    }

    public function edit($id)
    {
        $approach = $this->approaches->find($id);
        return view('Master::Approach.edit')
            ->withApproach($approach);
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $approach = $this->approaches->update($id, $inputs);

        if ($approach) {
            return response()->json(['status' => 'ok',
                'sector' => $approach,
                'message' => 'Approach is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Approach can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->approaches->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Approach is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Approach can not deleted.',
        ], 422);
    }
}
