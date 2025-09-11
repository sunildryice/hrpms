<?php

namespace Modules\Supplier\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Supplier\Repositories\SupplierRepository;
use Modules\Supplier\Requests\StoreRequest;
use Modules\Supplier\Requests\UpdateRequest;

use DataTables;

class SupplierController extends Controller
{
    /**
     * The supplier repository instance.
     *
     * @var SupplierRepository
     */
    protected $suppliers;

    /**
     * Create a new controller instance.
     *
     * @param SupplierRepository $suppliers
     * @return void
     */
    public function __construct(
        SupplierRepository $suppliers
    )
    {
        $this->suppliers = $suppliers;
    }

    /**
     * Display a listing of the supplier.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('manage-supplier');
        if ($request->ajax()) {
            $data = $this->suppliers->select([
                'id', 'contact_number', 'vat_pan_number', 'supplier_name', 'email_address', 'contact_person_name',
                'contact_person_email_address', 'account_number', 'account_name', 'bank_name', 'branch_name', 'swift_code'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-supplier-modal-form" href="';
                    $btn .= route('suppliers.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('suppliers.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Supplier::index')
            ->withSuppliers($this->suppliers->all());
    }

    /**
     * Show the form for creating a new supplier.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
//        $this->authorize('manage-supplier');
        return view('Supplier::create');
    }

    /**
     * Store a newly created supplier in storage.
     *
     * @param \Modules\Supplier\Requests\StoreRequest $request
     * @return \Illuminate\Http\JSonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $supplier = $this->suppliers->create($inputs);
        if ($supplier) {
            return response()->json(['status' => 'ok',
                'supplier' => $supplier,
                'message' => 'Supplier is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Supplier can not be added.'], 422);
    }

    /**
     * Display the specified supplier.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show(Request $request, $id)
    {
        $supplier = $this->suppliers->find($id);
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'supplier' => $supplier], 200);
        }
        return view('Supplier::show')
            ->withSupplier($supplier);
    }

    /**
     * Show the form for editing the specified supplier.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
//        $this->authorize('manage-supplier');
        $supplier = $this->suppliers->find($id);
        return view('Supplier::edit')
            ->withSupplier($supplier);
    }

    /**
     * Update the specified supplier in storage.
     *
     * @param \Modules\Supplier\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
//        $this->authorize('manage-supplier');
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $supplier = $this->suppliers->update($id, $inputs);
        if ($supplier) {
            return response()->json(['status' => 'ok',
                'supplier' => $supplier,
                'message' => 'Supplier is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Supplier can not be updated.'], 422);
    }

    /**
     * Remove the specified supplier from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
//        $this->authorize('manage-supplier');
        $flag = $this->suppliers->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Supplier is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Supplier can not deleted.',
        ], 422);
    }
}
