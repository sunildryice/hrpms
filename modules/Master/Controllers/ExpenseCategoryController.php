<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ExpenseCategoryRepository;
use Modules\Master\Requests\ExpenseCategory\StoreRequest;
use Modules\Master\Requests\ExpenseCategory\UpdateRequest;

use DataTables;

class ExpenseCategoryController extends Controller
{
    /**
     * The expense type repository instance.
     *
     * @var ExpenseCategoryRepository
     */
    protected $expenseCategories;

    /**
     * Create a new controller instance.
     *
     * @param ExpenseCategoryRepository $expenseCategories
     * @return void
     */
    public function __construct(
        ExpenseCategoryRepository $expenseCategories
    )
    {
        $this->expenseCategories = $expenseCategories;
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
            $data = $this->expenseCategories->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-expense-categories-modal-form" href="';
                    $btn .= route('master.expense.categories.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.expense.categories.destroy', $row->id) . '">';
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
        return view('Master::ExpenseCategory.index');
    }

    /**
     * Show the form for creating a new expense type.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExpenseCategory.create');
    }

    /**
     * Store a newly created expense type in storage.
     *
     * @param \Modules\Master\Requests\ExpenseCategory\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $expenseCategory = $this->expenseCategories->create($inputs);
        if ($expenseCategory) {
            return response()->json([
                'expenseCategory' => $expenseCategory,
                'message' => 'Expense category is successfully added.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Expense category can not be added.'
        ], 422);
    }

    /**
     * Display the specified expense type.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $expenseCategory = $this->expenseCategories->find($id);
        return response()->json(['status' => 'ok', 'expenseCategory' => $expenseCategory], 200);
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
        $expenseCategory = $this->expenseCategories->find($id);
        return view('Master::ExpenseCategory.edit')
            ->withExpenseCategory($expenseCategory);
    }

    /**
     * Update the specified expense type in storage.
     *
     * @param \Modules\Master\Requests\ExpenseCategory\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $expenseCategory = $this->expenseCategories->update($id, $inputs);
        if ($expenseCategory) {
            return response()->json(['status' => 'ok',
                'expenseCategory' => $expenseCategory,
                'message' => 'Expense category is successfully updated.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Expense category can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified expense category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->expenseCategories->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Expense category is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Expense category can not deleted.',
        ], 422);
    }
}
