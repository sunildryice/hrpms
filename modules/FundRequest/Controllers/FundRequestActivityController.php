<?php

namespace Modules\FundRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\FundRequest\Repositories\FundRequestActivityRepository;
use Modules\FundRequest\Repositories\FundRequestRepository;
use Modules\FundRequest\Requests\Activity\StoreRequest;
use Modules\FundRequest\Requests\Activity\UpdateRequest;
use Modules\Master\Repositories\ActivityCodeRepository;

class FundRequestActivityController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected FundRequestRepository $fundRequests,
        protected FundRequestActivityRepository $fundRequestActivities
    ) {
    }

    /**
     * Display a listing of the fund request items
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $fundRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $fundRequest = $this->fundRequests->find($fundRequestId);
            $data = $this->fundRequestActivities->select(['*'])
                ->with(['activityCode'])
                ->whereFundRequestId($fundRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            //            if ($authUser->can('update', $fundRequest)) {
            $datatable->addColumn('action', function ($row) use ($authUser, $fundRequest) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-activity-modal-form" href="';
                $btn .= route('fund.requests.activities.edit', [$row->fund_request_id, $row->id]).'" rel="tooltip" title="Edit Fund Request Activity""><i class="bi-pencil-square"></i></a>';
                if ($authUser->can('delete', $fundRequest)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('fund.requests.activities.destroy', [$row->fund_request_id, $row->id]).'" rel="tooltip" title="Delete Fund Request Activity">';
                    $btn .= '<i class="bi-trash"></i></a>';
                }

                return $btn;
            });

            //            }
            return $datatable->addColumn('activity', function ($row) {
                return $row->activityCode->getActivityCode();
            })->rawColumns(['action'])
                ->make(true);
        }

        return true;
    }

    /**
     * Show the form for creating a new fund request item.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $fundRequest = $this->fundRequests->find($id);

        return view('FundRequest::Activity.create')
            ->withActivityCodes($activityCodes)
            ->withFundRequest($fundRequest);
    }

    /**
     * Store a newly created fund request item in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $fundRequest = $this->fundRequests->find($id);
        $inputs = $request->validated();
        $inputs['fund_request_id'] = $fundRequest->id;
        $fundRequestActivity = $this->fundRequestActivities->create($inputs);

        if ($fundRequestActivity) {
            return response()->json(['status' => 'ok',
                'fundRequest' => $fundRequestActivity->fundRequest,
                'fundRequestActivity' => $fundRequestActivity,
                'fundActivityCount' => $fundRequest->fundRequestActivities()->count(),
                'message' => 'Fund request activity is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Fund request activity can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified fund request item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($frId, $id)
    {
        $fundRequest = $this->fundRequests->find($frId);
        $fundRequestActivity = $this->fundRequestActivities->find($id);
        $this->authorize('updateActivity', $fundRequest);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();

        return view('FundRequest::Activity.edit')
            ->withActivityCodes($activityCodes)
            ->withFundRequestActivity($fundRequestActivity);
    }

    /**
     * Update the specified fund request item in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $frId, $id)
    {
        $fundRequestActivity = $this->fundRequestActivities->find($id);
        $fundRequest = $this->fundRequests->find($frId);
        $this->authorize('updateActivity', $fundRequest);
        $inputs = $request->validated();
        $fundRequestActivity = $this->fundRequestActivities->update($id, $inputs);
        if ($fundRequestActivity) {
            return response()->json(['status' => 'ok',
                'fundRequest' => $fundRequestActivity->fundRequest,
                'fundRequestActivity' => $fundRequestActivity,
                'fundActivityCount' => $fundRequestActivity->fundRequest->fundRequestActivities()->count(),
                'message' => 'Fund request activity is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Fund request activity can not be updated.'], 422);
    }

    /**
     * Remove the specified fund request item from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($frId, $id)
    {
        $fundRequestActivity = $this->fundRequestActivities->find($id);
        $this->authorize('delete', $fundRequestActivity->fundRequest);
        $flag = $this->fundRequestActivities->destroy($id);
        if ($flag) {
            $fundRequest = $this->fundRequests->find($frId);

            return response()->json([
                'type' => 'success',
                'fundRequest' => $fundRequest,
                'fundActivityCount' => $fundRequest->fundRequestActivities()->count(),
                'message' => 'Fund request activity is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'fundRequest' => $fundRequestActivity->fundRequest,
            'message' => 'Fund request activity can not deleted.',
        ], 422);
    }
}
