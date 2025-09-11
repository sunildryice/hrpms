<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ProbationaryQuestionRepository;
use Modules\Master\Requests\ProbationaryQuestion\StoreRequest;
use Modules\Master\Requests\ProbationaryQuestion\UpdateRequest;

use DataTables;

class ProbationaryQuestionController extends Controller
{
    /**
     * The probationary question repository instance.
     *
     * @var ProbationaryQuestionRepository
     */
    protected $probationaryQuestions;

    /**
     * Create a new controller instance.
     *
     * @param ProbationaryQuestionRepository $probationaryQuestions
     * @return void
     */
    public function __construct(
        ProbationaryQuestionRepository $probationaryQuestions
    )
    {
        $this->probationaryQuestions = $probationaryQuestions;
    }

    /**
     * Display a listing of the probationary question.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->probationaryQuestions->select([
                'id', 'question', 'created_by', 'updated_at'
            ])->orderBy('position', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-question-modal-form" href="';
                    $btn .= route('master.probationary.questions.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.probationary.questions.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::ProbationaryQuestion.index');
    }

    /**
     * Show the form for creating a new probationary question.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ProbationaryQuestion.create');
    }

    /**
     * Store a newly created probationary question in storage.
     *
     * @param \Modules\Master\Requests\ProbationaryQuestion\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $probationaryQuestion = $this->probationaryQuestions->create($inputs);
        if ($probationaryQuestion) {
            return response()->json(['status' => 'ok',
                'probationaryQuestion' => $probationaryQuestion,
                'message' => 'Probationary question is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Probationary question can not be added.'], 422);
    }

    /**
     * Display the specified probationary question.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $probationaryQuestion = $this->probationaryQuestions->find($id);
        return response()->json(['status' => 'ok', 'probationaryQuestion' => $probationaryQuestion], 200);
    }

    /**
     * Show the form for editing the specified probationary question.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $probationaryQuestion = $this->probationaryQuestions->find($id);
        return view('Master::ProbationaryQuestion.edit')
            ->withProbationaryQuestion($probationaryQuestion);
    }

    /**
     * Update the specified probationary question in storage.
     *
     * @param \Modules\Master\Requests\ProbationaryQuestion\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $probationaryQuestion = $this->probationaryQuestions->update($id, $inputs);
        if ($probationaryQuestion) {
            return response()->json(['status' => 'ok',
                'probationaryQuestion' => $probationaryQuestion,
                'message' => 'Probationary question is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Probationary question can not be updated.'], 422);
    }

    /**
     * Remove the specified probationary question from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->probationaryQuestions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Probationary question is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Probationary question can not deleted.',
        ], 422);
    }
}
