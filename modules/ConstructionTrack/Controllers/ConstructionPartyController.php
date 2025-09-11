<?php

namespace Modules\ConstructionTrack\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
// use Modules\AdvanceRequest\Notifications\AdvanceRequestSubmitted;
use Illuminate\Support\Facades\Storage;
use Modules\ConstructionTrack\Repositories\ConstructionPartyRepository;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\ConstructionTrack\Requests\ConstructionParty\StoreRequest;
// use Modules\Master\Repositories\AccountCodeRepository;
// use Modules\Master\Repositories\ActivityCodeRepository;
// use Modules\Master\Repositories\DonorCodeRepository;
use Modules\ConstructionTrack\Requests\ConstructionParty\UpdateRequest;

class ConstructionPartyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ConstructionRepository $constructions,
        protected ConstructionPartyRepository $constructionParties,
    ) {}

    /**
     * Display a listing of the construction parties
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $constructionId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $construction = $this->constructions->find($constructionId);
            $data = $this->constructionParties->select([
                'id', 'construction_id', 'party_name', 'contribution_amount', 'contribution_percentage', 'deletable'])
                ->whereConstructionId($constructionId)->orderBy('created_at', 'asc');
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            // if ($authUser->can('update', $construction)) {
            $datatable->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-contribution-modal-form" href="';
                $btn .= route('construction.parties.edit', [$row->id]).'"><i class="bi-pencil-square"></i></a>';
                if ($authUser->can('deletable', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('construction.parties.destroy', [$row->id]).'">';
                    $btn .= '<i class="bi-trash"></i></a>';
                }

                return $btn;
            });

            // }
            return $datatable->rawColumns(['action'])
                ->make(true);
        }

        return true;
    }

    /**
     * Show the form for creating a new construction request detail by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);

        return view('ConstructionTrack::ConstructionParties.create', [
            'construction' => ($construction),
        ]);
    }

    /**
     * Store a newly created construction party in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $construction = $this->constructions->find($id);
        $inputs = $request->validated();
        $inputs['construction_id'] = $construction->id;
        $constructionParty = $this->constructionParties->create($inputs);

        if ($constructionParty) {
            $this->constructionParties->updateContributionPercentage($construction->id);

            $construction = $this->constructions->find($construction->id);

            return response()->json([
                'status' => 'ok',
                'construction' => $construction,
                'constructionParty' => $constructionParty,
                'message' => 'Construction Party is successfully added.'], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Construction Party can not be added.',
        ], 422);

    }

    /**
     * Show the form for editing the specified advance request detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $constructionParty = $this->constructionParties->find($id);
        // $this->authorize('update', $constructions);

        return view('ConstructionTrack::ConstructionParties.edit', [
            'constructionParty' => ($constructionParty),
        ]);
    }

    /**
     * Update the specified Construction Party in storage.
     *
     * @param  \Modules\ConstructionTrack\Requests\UpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $constructionParty = $this->constructionParties->update($id, $inputs);
        if ($constructionParty) {
            $this->constructionParties->updateContributionPercentage($constructionParty->construction_id);

            $construction = $this->constructions->find($constructionParty->construction_id);

            return response()->json([
                'status' => 'ok',
                'construction' => $construction,
                'constructionParty' => $constructionParty,
                'message' => 'Construction Party is successfully updated.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Construction Party can not be updated.',
        ], 422);
    }

    /**
     * Remove the specified Advance Request Detail from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $constructionParty = $this->constructionParties->find($id);
        $flag = $this->constructionParties->destroy($id);
        if ($flag) {
            $this->constructionParties->updateContributionPercentage($constructionParty->construction_id);

            $construction = $this->constructions->find($constructionParty->construction_id);

            return response()->json([
                'type' => 'success',
                'construction' => $construction,
                'message' => 'Construction Party is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Construction Party can not deleted.',
        ], 422);
    }
}
