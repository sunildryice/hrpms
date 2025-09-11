<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ExpenseTypeRepository;
use Modules\Master\Requests\ExpenseType\StoreRequest;
use Modules\Master\Requests\ExpenseType\UpdateRequest;

use DataTables;

class ExpenseTypeController extends Controller
{
    /**
     * The expense type repository instance.
     *
     * @var ExpenseTypeRepository
     */
    protected $expenseTypes;

    /**
     * Create a new controller instance.
     *
     * @param ExpenseTypeRepository $expenseTypes
     * @return void
     */
    public function __construct(
        ExpenseTypeRepository $expenseTypes
    )
    {
        $this->expenseTypes = $expenseTypes;
    }

    /**
     * Display a listing of the expense type.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->expenseTypes->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-expense-type-modal-form" href="';
                    $btn .= route('master.expense.types.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.expense.types.destroy', $row->id) . '">';
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
        return view('Master::ExpenseType.index');
    }

    /**
     * Show the form for creating a new expense type.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExpenseType.create');
    }

    /**
     * Store a newly created expense type in storage.
     *
     * @param \Modules\Master\Requests\ExpenseType\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $expenseType = $this->expenseTypes->create($inputs);
        if ($expenseType) {
            return response()->json(['status' => 'ok',
                'expenseType' => $expenseType,
                'message' => 'Expense type is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Expense type can not be added.'], 422);
    }

    /**
     * Display the specified expense type.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $expenseType = $this->expenseTypes->find($id);
        return response()->json(['status' => 'ok', 'expenseType' => $expenseType], 200);
    }

    /**
     * Show the form for editing the specified expense type.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $expenseType = $this->expenseTypes->find($id);
        return view('Master::ExpenseType.edit')
            ->withExpenseType($expenseType);
    }

    /**
     * Update the specified expense type in storage.
     *
     * @param \Modules\Master\Requests\ExpenseType\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $expenseType = $this->expenseTypes->update($id, $inputs);
        if ($expenseType) {
            return response()->json(['status' => 'ok',
                'expenseType' => $expenseType,
                'message' => 'Expense type is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Expense type can not be updated.'], 422);
    }

    /**
     * Remove the specified expense type from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->expenseTypes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Expense type is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Expense type can not deleted.',
        ], 422);
    }
}
