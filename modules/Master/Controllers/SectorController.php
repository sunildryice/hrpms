<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\SectorRepository;
use Modules\Master\Requests\Sector\StoreRequest;
use Modules\Master\Requests\Sector\UpdateRequest;
use DataTables;

class SectorController extends Controller
{
    protected $sectors;

    public function __construct(SectorRepository $sectors)
    {
        $this->sectors = $sectors;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->sectors->select([
                'id', 'title', 'created_by', 'updated_at'
            ])->orderBy('title');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-sector-modal-form" href="';
                    $btn .= route('master.sectors.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.sectors.destroy', $row->id) . '">';
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

        return view('Master::Sector.index')
            ->withSectors($this->sectors->all());
    }

    public function create()
    {
        return view('Master::Sector.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();

        $sector = $this->sectors->create($inputs);

        if ($sector) {
            return response()->json(['status' => 'ok',
                'sector' => $sector,
                'message' => 'Sector is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Sector can not be added.'], 422);
    }

    public function show($id)
    {
        $sector = $this->sectors->find($id);
        return response()->json(['status' => 'ok', 'sector' => $sector], 200);
    }

    public function edit($id)
    {
        $sector = $this->sectors->find($id);
        return view('Master::Sector.edit')
            ->withSector($sector);
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $sector = $this->sectors->update($id, $inputs);

        if ($sector) {
            return response()->json(['status' => 'ok',
                'sector' => $sector,
                'message' => 'Sector is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Sector can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->sectors->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Sector is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Sector can not deleted.',
        ], 422);
    }
}
