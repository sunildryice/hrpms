<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\FamilyRelationRepository;
use Modules\Master\Requests\FamilyRelation\StoreRequest;
use Modules\Master\Requests\FamilyRelation\UpdateRequest;

use DataTables;

class FamilyRelationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FamilyRelationRepository $familyRelations
     * @return void
     */
    public function __construct(
        FamilyRelationRepository $familyRelations
    )
    {
        $this->familyRelations = $familyRelations;
    }

    /**
     * Display a listing of the family type.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
//        if ($request->ajax()) {
//            $data = $this->familyRelations->select(['*'])->orderBy('position');
//            return DataTables::of($data)
//                ->addIndexColumn()
//                ->addColumn('action', function ($row) {
//                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form" href="';
//                    $btn .= route('master.family.relations.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
//                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
//                    $btn .= 'data-href="' . route('master.family.relations.destroy', $row->id) . '">';
//                    $btn .= '<i class="bi-trash"></i></a>';
//                    return $btn;
//                })
//                ->addColumn('created_by', function ($row) {
//                    return $row->getCreatedBy();
//                })
//                ->addColumn('updated_at', function ($row) {
//                    return $row->getUpdatedAt();
//                })
//                ->rawColumns(['action'])
//                ->make(true);
//        }
        $familyRelations = $this->familyRelations->select(['*'])->orderBy('position')->get();
        return view('Master::FamilyRelation.index')
            ->withFamilyRelations($familyRelations);
    }

    /**
     * Show the form for creating a new family type.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::FamilyRelation.create');
    }

    /**
     * Store a newly created family type in storage.
     *
     * @param \Modules\Master\Requests\FamilyRelation\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $familyRelation = $this->familyRelations->create($inputs);
        if ($familyRelation) {
            return response()->json(['status' => 'ok',
                'familyRelation' => $familyRelation,
                'message' => 'Family Relation is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Family Relation can not be added.'], 422);
    }

    /**
     * Display the specified family type.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $familyRelation = $this->familyRelations->find($id);
        return response()->json(['status' => 'ok', 'familyRelation' => $familyRelation], 200);
    }

    /**
     * Show the form for editing the specified family type.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $familyRelation = $this->familyRelations->find($id);
        return view('Master::FamilyRelation.edit')
            ->withFamilyRelation($familyRelation);
    }

    /**
     * Update the specified family type in storage.
     *
     * @param \Modules\Master\Requests\FamilyRelation\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $familyRelation = $this->familyRelations->update($id, $inputs);
        if ($familyRelation) {
            return response()->json(['status' => 'ok',
                'familyRelation' => $familyRelation,
                'message' => 'Family Relation is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Family Relation can not be updated.'], 422);
    }

    /**
     * Remove the specified family type from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->familyRelations->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Family Relation is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Family Relation can not deleted.',
        ], 422);
    }

    /**
     * sort orders of the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sortOrder(Request $request)
    {
        $relations = explode('&', str_replace('row[]=', '', $request->relations));
        $position = 1;
        foreach ($relations as $relationId) {
            $this->familyRelations->update($relationId, ['position' => $position]);
            $position++;
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Family relations are sorted successfully.'
        ], 200);
    }

}
