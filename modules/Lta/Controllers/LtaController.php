<?php

namespace Modules\Lta\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Lta\Repositories\LtaContractRepository;
use Modules\Lta\Requests\StoreRequest;
use Modules\Lta\Requests\UpdateRequest;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Supplier\Repositories\SupplierRepository;
use Yajra\DataTables\Facades\DataTables;

class LtaController extends Controller
{
    protected $ltaContracts;
    protected $employees;
    protected $suppliers;
    protected $destinationPath;
    protected $offices;
    /**
     * Create a new controller instance.
     *
     * @param LtaContractRepository $contracts
     * @param EmployeeRepository $employees
     * @param SupplierRepository $suppliers
     * @return void
     */
    public function __construct(
        LtaContractRepository $ltaContracts,
        EmployeeRepository $employees,
        SupplierRepository $suppliers,
        OfficeRepository $offices
    ) {
        $this->ltaContracts = $ltaContracts;
        $this->employees = $employees;
        $this->suppliers = $suppliers;
        $this->destinationPath = 'lta';
        $this->offices = $offices;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->ltaContracts->with(['supplier'])->select(['*'])->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier->getSupplierNameandVAT();
                })->addColumn('contract_date', function ($row) {
                return $row->getContractDate();
            })->addColumn('expiry_date', function ($row) {
                return $row->getEndDate();
            })->addColumn('remarks', function ($row) {
                return $row->getShortRemarks();
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('lta.detail', $row->id) . '" rel="tooltip" title="View Lta"><i class="bi-eye"></i></a>';
                // if ($authUser->can('update', $row)) {
                $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-lta-modal-form" href="';
                $btn .= route('lta.edit', $row->id) . '" rel="tooltip" title="Edit Lta"><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('lta.destroy', $row->id) . '">';
                $btn .= '<i class="bi-trash"></i></a>';
                // }
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Lta::index');
    }

    public function create()
    {
        $employees = $this->employees->select(['id', 'full_name'])->whereNotNull('activated_at')->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name', 'vat_pan_number'])->whereNotNull('activated_at')->get();
        $offices = $this->offices->getActiveOffices();
        return view('Lta::create')
            ->withOffices($offices)
            ->withEmployees($employees)
            ->withSuppliers($suppliers);
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $contract = $this->ltaContracts->create($inputs);
        if ($contract) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath . '/' . $contract->id, time() . $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
                $contract = $this->ltaContracts->update($contract->id, $inputs);
            }
            return response()->json(['status' => 'ok',
                'contract' => $contract,
                'message' => 'Contract is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Contract can not be added.'], 422);
    }

    public function show($id)
    {
        $contract = $this->ltaContracts->find($id);
        return response()->json(['status' => 'ok', 'contract' => $contract], 200);
    }

    public function edit($id)
    {
        $lta = $this->ltaContracts->find($id);
        $employees = $this->employees->select(['id', 'full_name'])->whereNotNull('activated_at')->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name', 'vat_pan_number'])->whereNotNull('activated_at')->get();
        $offices = $this->offices->getActiveOffices();

        return view('Lta::edit')
            ->withOffices($offices)
            ->withLta($lta)
            ->withEmployees($employees)
            ->withSuppliers($suppliers);
    }

    public function update(UpdateRequest $request, $id)
    {
        $contract = $this->ltaContracts->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $contract->id, time() . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $contract = $this->ltaContracts->update($id, $inputs);
        if ($contract) {
            return response()->json(['status' => 'ok',
                'contract' => $contract,
                'message' => 'Contract is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Contract can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->ltaContracts->destroy($id);
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
        $ltaContract = $this->ltaContracts->find($id);

        return view('Lta::detail')
            ->withAuthUser($authUser)
            ->with('lta', $ltaContract);
    }

}
