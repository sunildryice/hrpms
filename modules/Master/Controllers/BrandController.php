<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Repositories\BrandRepository;
use Modules\Master\Requests\Brand\StoreRequest;
use Modules\Master\Requests\Brand\UpdateRequest;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function __construct(
        BrandRepository $brands
    ) {
        $this->brands = $brands;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->brands->select([
                'id',
                'title',
                'created_by',
                'updated_at'
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-brand-modal-form" href="';
                    $btn .= route('master.brands.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= ' <a href="javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.brands.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', fn($row) => $row->getCreatedBy())
                ->addColumn('updated_at', fn($row) => $row->getUpdatedAt())
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::Brand.index');
    }

    public function create()
    {
        return view('Master::Brand.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();

        $brand = $this->brands->create($inputs);

        if ($brand) {
            return response()->json([
                'brand' => $brand,
                'message' => 'Brand successfully added.'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Brand could not be added.'
        ], 422);
    }

    public function show($id)
    {
        $brand = $this->brands->find($id);
        return response()->json(['status' => 'ok', 'brand' => $brand], 200);
    }

    public function edit($id)
    {
        $brand = $this->brands->find($id);
        return view('Master::Brand.edit')
            ->withBrand($brand);
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $brand = $this->brands->update($id, $inputs);

        if ($brand) {
            return response()->json([
                'status' => 'ok',
                'brand' => $brand,
                'message' => 'Brand successfully updated.'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Brand could not be updated.'
        ], 422);
    }

    public function destroy($id)
    {
        $flag = $this->brands->destroy($id);

        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Brand successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Brand could not be deleted.',
        ], 422);
    }
}