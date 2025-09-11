<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Requests\AccountCode\StoreRequest;
use Modules\Master\Requests\AccountCode\UpdateRequest;

use DataTables;

class AccountCodeController extends Controller
{
    /**
     * The account code repository instance.
     *
     * @var AccountCodeRepository
     */
    protected $accountCodes;

    /**
     * Create a new controller instance.
     *
     * @param AccountCodeRepository $accountCodes
     * @return void
     */
    public function __construct(
        AccountCodeRepository $accountCodes
    )
    {
        $this->accountCodes = $accountCodes;
    }

    /**
     * Display a listing of the account code.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->accountCodes->select([
                'id', 'title', 'description', 'activated_at', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-account-modal-form" href="';
                    $btn .= route('master.account.codes.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.account.codes.destroy', $row->id) . '">';
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
        return view('Master::AccountCode.index')
            ->withAccountCodes($this->accountCodes->all());
    }

    /**
     * Show the form for creating a new account code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::AccountCode.create');
    }

    /**
     * Store a newly created account code in storage.
     *
     * @param \Modules\Master\Requests\AccountCode\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $accountCode = $this->accountCodes->create($inputs);
        if ($accountCode) {
            return response()->json(['status' => 'ok',
                'account code' => $accountCode,
                'message' => 'Account code is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Account code can not be added.'], 422);
    }

    /**
     * Display the specified account code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $accountCode = $this->accountCodes->find($id);
        return response()->json(['status' => 'ok', 'accountCode' => $accountCode], 200);
    }

    /**
     * Show the form for editing the specified account code.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $accountCode = $this->accountCodes->find($id);
        return view('Master::AccountCode.edit')
            ->withAccountCode($accountCode);
    }

    /**
     * Update the specified account code in storage.
     *
     * @param \Modules\Master\Requests\AccountCode\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $accountCode = $this->accountCodes->update($id, $inputs);
        if ($accountCode) {
            return response()->json(['status' => 'ok',
                'account code' => $accountCode,
                'message' => 'Account code is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Account code can not be updated.'], 422);
    }

    /**
     * Remove the specified account code from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->accountCodes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Account code is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Account code can not deleted.',
        ], 422);
    }
}
