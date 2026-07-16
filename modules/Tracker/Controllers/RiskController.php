<?php

namespace Modules\Tracker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use Modules\Tracker\Models\RiskStatus;
use Modules\Tracker\Models\RiskType;
use Modules\Tracker\Models\RiskProbability;
use Modules\Tracker\Models\RiskImpact;
use Modules\Tracker\Models\RiskRating;
use Modules\Tracker\Models\RiskResponseType;
use Modules\Tracker\Repositories\RiskRepository;
use Modules\Tracker\Requests\Risk\StoreRequest;
use Modules\Tracker\Requests\Risk\UpdateRequest;
use Yajra\DataTables\DataTables;

class RiskController extends Controller
{
    public function __construct(
        RiskRepository $risks,
    )
    {
        $this->risks = $risks;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-risk');

        if ($request->ajax()) {
            $data = $this->risks->with([
                'projectDetail', 'riskStatus', 'riskType', 'riskProbability',
                'riskImpact', 'riskRating', 'riskResponseType',
            ])->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('risk_name', function ($row) {
                    return $row->risk_name;
                })
                ->addColumn('date_added', function ($row) {
                    return $row->getDateAdded();
                })
                ->addColumn('project_title', function ($row) {
                    return $row->getProjectTitle();
                })
                ->addColumn('risk_status', function ($row) {
                    return $row->getRiskStatusTitle();
                })
                ->addColumn('risk_type', function ($row) {
                    return $row->getRiskTypeTitle();
                })
                ->addColumn('risk_rating', function ($row) {
                    return $row->getRiskRatingTitle();
                })
                ->addColumn('risk_owner_names', function ($row) {
                    return $row->getRiskOwnerNames();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('risk.show', $row->id) . '" rel="tooltip" title="View Risk"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-risk')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('risk.edit', $row->id) . '" rel="tooltip" title="Edit Risk"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-risk')) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete Risk" ';
                        $btn .= 'data-href="' . route('risk.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Tracker::Risk.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-risk');

        $projects           = Project::whereNotNull('activated_at')->get();
        $riskStatuses       = RiskStatus::all();
        $riskTypes          = RiskType::all();
        $riskProbabilities  = RiskProbability::all();
        $riskImpacts        = RiskImpact::all();
        $riskRatings        = RiskRating::all();
        $riskResponseTypes  = RiskResponseType::all();

        return view('Tracker::Risk.create', compact(
            'projects', 'riskStatuses', 'riskTypes', 'riskProbabilities',
            'riskImpacts', 'riskRatings', 'riskResponseTypes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-risk');

        $inputs = $request->validated();
        $inputs['created_by'] = auth()->user()->id;

        $risk = $this->risks->create($inputs);

        if ($risk) {
            if ($request->has('risk_histories')) {
                foreach ($request->input('risk_histories') as $history) {
                    if (!empty($history['description_of_risk']) || !empty($history['mitigating_action']) || !empty($history['whats_changed_this_period']) || !empty($history['remarks'])) {
                        $risk->riskHistories()->create([
                            'updated_date'           => $history['updated_date'] ?? null,
                            'risk_status_id'         => $history['risk_status_id'] ?? null,
                            'description_of_risk'    => $history['description_of_risk'] ?? null,
                            'mitigating_action'      => $history['mitigating_action'] ?? null,
                            'whats_changed_this_period' => $history['whats_changed_this_period'] ?? null,
                            'remarks'                => $history['remarks'] ?? null,
                            'created_by'             => auth()->user()->id,
                        ]);
                    }
                }
            }

            return redirect()->route('risk.index')->withSuccessMessage('Risk created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Risk could not be created.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $risk = $this->risks->find($id);
        $risk->load('riskHistories.riskStatus');
        return view('Tracker::Risk.show', compact('risk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-risk');

        $risk               = $this->risks->find($id);
        $risk->load('riskHistories');
        $projects           = Project::whereNotNull('activated_at')->get();
        $riskStatuses       = RiskStatus::all();
        $riskTypes          = RiskType::all();
        $riskProbabilities  = RiskProbability::all();
        $riskImpacts        = RiskImpact::all();
        $riskRatings        = RiskRating::all();
        $riskResponseTypes  = RiskResponseType::all();

        return view('Tracker::Risk.edit', compact(
            'risk', 'projects', 'riskStatuses', 'riskTypes', 'riskProbabilities',
            'riskImpacts', 'riskRatings', 'riskResponseTypes'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->authorize('manage-risk');

        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->user()->id;

        $risk = $this->risks->update($id, $inputs);

        if ($risk) {
            if ($request->has('deleted_risk_histories')) {
                \Modules\Tracker\Models\RiskHistory::whereIn('id', $request->input('deleted_risk_histories'))->delete();
            }

            if ($request->has('risk_histories')) {
                foreach ($request->input('risk_histories') as $history) {
                    if (isset($history['id'])) {
                        $existing = \Modules\Tracker\Models\RiskHistory::find($history['id']);
                        if ($existing) {
                            $existing->update([
                                'updated_date'           => $history['updated_date'] ?? null,
                                'risk_status_id'         => $history['risk_status_id'] ?? null,
                                'description_of_risk'    => $history['description_of_risk'] ?? null,
                                'mitigating_action'      => $history['mitigating_action'] ?? null,
                                'whats_changed_this_period' => $history['whats_changed_this_period'] ?? null,
                                'remarks'                => $history['remarks'] ?? null,
                                'updated_by'             => auth()->user()->id,
                            ]);
                        }
                    } else {
                        if (!empty($history['description_of_risk']) || !empty($history['mitigating_action']) || !empty($history['whats_changed_this_period']) || !empty($history['remarks'])) {
                            $risk->riskHistories()->create([
                                'updated_date'           => $history['updated_date'] ?? null,
                                'risk_status_id'         => $history['risk_status_id'] ?? null,
                                'description_of_risk'    => $history['description_of_risk'] ?? null,
                                'mitigating_action'      => $history['mitigating_action'] ?? null,
                                'whats_changed_this_period' => $history['whats_changed_this_period'] ?? null,
                                'remarks'                => $history['remarks'] ?? null,
                                'created_by'             => auth()->user()->id,
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('risk.index')->withSuccessMessage('Risk updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Risk could not be updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage-risk');

        $risk = $this->risks->destroy($id);

        if ($risk) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Risk deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Risk could not be deleted.'
            ], 422);
        }
    }
}
