<?php

namespace Modules\Contract\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Contract\Repositories\ContractRepository;
use Modules\Contract\Requests\StoreRequest;
use Modules\Contract\Requests\UpdateRequest;

use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Contract\Exports\ContractExport;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Supplier\Repositories\SupplierRepository;

class ContractController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param ContractRepository $contracts
     * @param EmployeeRepository $employees
     * @param SupplierRepository $suppliers
     * @return void
     */
    public function __construct(
        protected ContractRepository $contracts,
        protected EmployeeRepository $employees,
        protected SupplierRepository $suppliers
    )
    {
        $this->destinationPath = 'contracts';
    }

    /**
     * Display a listing of the contract.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->contracts->with(['supplier', 'latestAmendment'])->select(['*'])->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($row){
                    return $row->supplier->getSupplierNameandVAT();
                })->addColumn('contract_amount', function ($row){
                    return $row->getContractAmount();
                })->addColumn('contract_date', function ($row){
                    return $row->getContractDate();
                })->addColumn('expiry_date', function ($row){
                    return $row->getExpiryDate();
                })->addColumn('effective_date', function ($row){
                    return $row->getEffectiveDate();
                })->addColumn('remarks', function ($row){
                    return $row->getShortRemarks() ;
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('contracts.detail', $row->id) . '" rel="tooltip" title="View Contract"><i class="bi-eye"></i></a>';
                    if($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-contract-modal-form" href="';
                        $btn .= route('contracts.edit', $row->id) . '" rel="tooltip" title="Edit Contract"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('contracts.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Contract::index');
    }

    /**
     * Show the form for creating a new contract.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $employees = $this->employees->select(['id', 'full_name'])->whereNotNull('activated_at')->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name', 'vat_pan_number'])->whereNotNull('activated_at')->get();
        return view('Contract::create')
            ->withEmployees($employees)
            ->withSuppliers($suppliers);
    }

    /**
     * Store a newly created contract in storage.
     *
     * @param \Modules\Contract\Requests\StoreRequest $request
     * @return \Illuminate\Http\JSonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $contract = $this->contracts->create($inputs);
        if ($contract) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath .'/'.$contract->id, time() . $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
                $contract = $this->contracts->update($contract->id, $inputs);
            }
            return response()->json(['status' => 'ok',
                'contract' => $contract,
                'message' => 'Contract is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Contract can not be added.'], 422);
    }

    /**
     * Display the specified contract.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = $this->contracts->find($id);
        return response()->json(['status' => 'ok', 'contract' => $contract], 200);
    }

    /**
     * Show the form for editing the specified contract.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
       $contract = $this->contracts->find($id);
        $employees = $this->employees->select(['id', 'full_name'])->whereNotNull('activated_at')->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name', 'vat_pan_number'])->whereNotNull('activated_at')->get();
        return view('Contract::edit')
            ->withContract($contract)
            ->withEmployees($employees)
            ->withSuppliers($suppliers);
    }

    /**
     * Update the specified contract in storage.
     *
     * @param \Modules\Contract\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $contract = $this->contracts->find($id);
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath .'/'.$contract->id, time() . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $contract = $this->contracts->update($id, $inputs);
        if ($contract) {
            return response()->json(['status' => 'ok',
                'contract' => $contract,
                'message' => 'Contract is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Contract can not be updated.'], 422);
    }

    /**
     * Remove the specified contract from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $flag = $this->contracts->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Contract is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Contract can not deleted.',
        ], 422);
    }

    public function detail($id)
    {
        $authUser = auth()->user();
        $contract = $this->contracts->find($id);

        return view('Contract::detail')
            ->withAuthUser($authUser)
            ->withContract($contract);
    }

    public function export()
    {
        return new ContractExport();
    }

}
