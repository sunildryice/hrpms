<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ExitFeedbackRepository;
use Modules\Master\Requests\ExitFeedback\StoreRequest;
use Modules\Master\Requests\ExitFeedback\UpdateRequest;

use DataTables;

class ExitFeedbackController extends Controller
{
    /**
     * The exit feedback repository instance.
     *
     * @var ExitFeedbackRepository
     */
    protected $exitFeedbacks;

    /**
     * Create a new controller instance.
     *
     * @param ExitFeedbackRepository $exitFeedbacks
     * @return void
     */
    public function __construct(
        ExitFeedbackRepository $exitFeedbacks
    )
    {
        $this->exitFeedbacks = $exitFeedbacks;
    }

    /**
     * Display a listing of the exit feedback.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->exitFeedbacks->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-feedbacks-modal-form" href="';
                    $btn .= route('master.exit.feedbacks.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.exit.ratings.destroy', $row->id) . '">';
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
        return view('Master::ExitFeedback.index');
    }

    /**
     * Show the form for creating a new exit feedback.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExitFeedback.create');
    }

    /**
     * Store a newly created exit feedback in storage.
     *
     * @param \Modules\Master\Requests\ExitFeedback\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $exitFeedback = $this->exitFeedbacks->create($inputs);
        if ($exitFeedback) {
            return response()->json(['status' => 'ok',
                'exitFeedback' => $exitFeedback,
                'message' => 'Exit feedback is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit feedback can not be added.'], 422);
    }

    /**
     * Display the specified exit feedback.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $exitFeedback = $this->exitFeedbacks->find($id);
        return response()->json(['status' => 'ok', 'exitFeedback' => $exitFeedback], 200);
    }

    /**
     * Show the form for editing the specified exit feedback.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $exitFeedback = $this->exitFeedbacks->find($id);
        return view('Master::ExitFeedback.edit')
            ->withExitFeedback($exitFeedback);
    }

    /**
     * Update the specified exit feedback in storage.
     *
     * @param \Modules\Master\Requests\ExitFeedback\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $exitFeedback = $this->exitFeedbacks->update($id, $inputs);
        if ($exitFeedback) {
            return response()->json(['status' => 'ok',
                'exitFeedback' => $exitFeedback,
                'message' => 'Exit feedback is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit feedback can not be updated.'], 422);
    }

    /**
     * Remove the specified exit feedback from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->exitFeedbacks->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit feedback is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit feedback can not deleted.',
        ], 422);
    }
}
