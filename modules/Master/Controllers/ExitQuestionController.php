<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ExitQuestionRepository;
use Modules\Master\Requests\ExitQuestion\StoreRequest;
use Modules\Master\Requests\ExitQuestion\UpdateRequest;

use DataTables;

class ExitQuestionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ExitQuestionRepository $exitQuestions
     * @return void
     */
    public function __construct(
        ExitQuestionRepository $exitQuestions
    )
    {
        $this->exitQuestions = $exitQuestions;
    }

    /**
     * Display a listing of the exit question.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->exitQuestions->select([
                'id', 'question','answer_type', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-exit-questions-modal-form" href="';
                    $btn .= route('master.exit.questions.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.exit.questions.destroy', $row->id) . '">';
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
        return view('Master::ExitQuestion.index');
    }

    /**
     * Show the form for creating a new exit question.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExitQuestion.create');
    }

    /**
     * Store a newly created exit question in storage.
     *
     * @param \Modules\Master\Requests\ExitQuestion\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $inputs['options'] = $request->options ? json_encode(array_filter($request->options)) : [];

        $exitQuestion = $this->exitQuestions->create($inputs);
        if ($exitQuestion) {
            return response()->json(['status' => 'ok',
                'exitQuestion' => $exitQuestion,
                'message' => 'Exit question is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit question can not be added.'], 422);
    }

    /**
     * Display the specified exit question.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $exitQuestion = $this->exitQuestions->find($id);
        return response()->json(['status' => 'ok', 'exitQuestion' => $exitQuestion], 200);
    }

    /**
     * Show the form for editing the specified exit question.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $exitQuestion = $this->exitQuestions->find($id);
        return view('Master::ExitQuestion.edit')
            ->withExitQuestion($exitQuestion);
    }

    /**
     * Update the specified exit question in storage.
     *
     * @param \Modules\Master\Requests\ExitQuestion\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['options'] = $request->options ? json_encode(array_filter($request->options)) : [];
        $exitQuestion = $this->exitQuestions->update($id, $inputs);
        if ($exitQuestion) {
            return response()->json(['status' => 'ok',
                'exitQuestion' => $exitQuestion,
                'message' => 'Exit question is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit question can not be updated.'], 422);
    }

    /**
     * Remove the specified exit question from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->exitQuestions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit question is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit question can not deleted.',
        ], 422);
    }
}
