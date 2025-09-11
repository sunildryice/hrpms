<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Master\Requests\TrainingQuestion\StoreRequest;
use Modules\Master\Requests\TrainingQuestion\UpdateRequest;

use DataTables;

class TrainingQuestionController extends Controller
{
    /**
     * The training question repository instance.
     *
     * @var TrainingQuestionRepository
     */
    protected $trainingQuestions;

    /**
     * Create a new controller instance.
     *
     * @param TrainingQuestionRepository $trainingQuestions
     * @return void
     */
    public function __construct(
        TrainingQuestionRepository $trainingQuestions
    )
    {
        $this->trainingQuestions = $trainingQuestions;
    }

    /**
     * Display a listing of the training question.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->trainingQuestions->select([
                'id', 'question','answer_type', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-training-modal-form" href="';
                    $btn .= route('master.training.questions.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.training.questions.destroy', $row->id) . '">';
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
        return view('Master::TrainingQuestion.index');
    }

    /**
     * Show the form for creating a new training question.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::TrainingQuestion.create');
    }

    /**
     * Store a newly created training question in storage.
     *
     * @param \Modules\Master\Requests\TrainingQuestion\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $trainingQuestion = $this->trainingQuestions->create($inputs);
        if ($trainingQuestion) {
            return response()->json(['status' => 'ok',
                'trainingQuestion' => $trainingQuestion,
                'message' => 'Training question is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Training question can not be added.'], 422);
    }

    /**
     * Display the specified training question.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $trainingQuestion = $this->trainingQuestions->find($id);
        return response()->json(['status' => 'ok', 'trainingQuestion' => $trainingQuestion], 200);
    }

    /**
     * Show the form for editing the specified training question.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $trainingQuestion = $this->trainingQuestions->find($id);
        return view('Master::TrainingQuestion.edit')
            ->withTrainingQuestion($trainingQuestion);
    }

    /**
     * Update the specified training question in storage.
     *
     * @param \Modules\Master\Requests\TrainingQuestion\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $trainingQuestion = $this->trainingQuestions->update($id, $inputs);
        if ($trainingQuestion) {
            return response()->json(['status' => 'ok',
                'trainingQuestion' => $trainingQuestion,
                'message' => 'Training question is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Training question can not be updated.'], 422);
    }

    /**
     * Remove the specified training question from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->trainingQuestions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Training question is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Training question can not deleted.',
        ], 422);
    }
}
