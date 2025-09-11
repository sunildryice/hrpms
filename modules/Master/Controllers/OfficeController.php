<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Requests\Office\StoreRequest;
use Modules\Master\Requests\Office\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\OfficeTypeRepository;

class OfficeController extends Controller
{
    private $districts;
    private $offices;
    private $officeTypes;

    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param OfficeRepository $offices
     * @return void
     */
    public function __construct(
        DistrictRepository $districts,
        OfficeRepository $offices,
        OfficeTypeRepository $officeTypes
    )
    {
        $this->districts = $districts;
        $this->offices = $offices;
        $this->officeTypes = $officeTypes;
    }

    /**
     * Display a listing of the office.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->offices->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-office-modal-form" href="';
                    $btn .= route('master.offices.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.offices.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('office_type', function ($row) {
                    return $row->getOfficeType();
                })
                ->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::Office.index');
    }

    /**
     * Show the form for creating a new office.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::Office.create')
            ->withOfficeTypes($this->officeTypes->get())
            ->withDistricts($this->districts->get());
    }

    /**
     * Store a newly created office in storage.
     *
     * @param \Modules\Master\Requests\Office\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $office = $this->offices->create($inputs);

        if ($office) {
            return response()->json(['status' => 'ok',
                'office' => $office,
                'message' => 'Office is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Office can not be added.'], 422);
    }

    /**
     * Display the specified office.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $office = $this->offices->find($id);
        return response()->json(['status' => 'ok', 'office' => $office], 200);
    }

    /**
     * Show the form for editing the specified office.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $office = $this->offices->find($id);
        $parentOffices = $this->offices->getParentOffices($office->office_type_id);
        return view('Master::Office.edit')
            ->withDistricts($this->districts->get())
            ->withOffice($office)
            ->withParentOffices($parentOffices)
            ->withOfficeTypes($this->officeTypes->get());

    }

    /**
     * Update the specified office in storage.
     *
     * @param \Modules\Master\Requests\Office\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $office = $this->offices->find($id);
        $inputs['updated_by'] = auth()->id();
        $inputs['activated_at'] = $request->active ? ($office->activated_at ?: date('Y-m-d H:i:s')) : NULL;
        $office = $this->offices->update($id, $inputs);
        if ($office) {
            return response()->json(['status' => 'ok',
                'office' => $office,
                'message' => 'Office is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Office can not be updated.'], 422);
    }

    /**
     * Remove the specified office from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->offices->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Office is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Office can not deleted.',
        ], 422);
    }

    public function getParentOffices($officeType = null)
    {
        $offices = $this->offices->getParentOffices($officeType);

        $options = '<option value="">Select parent office</option>';
        if (count($offices)) {
            foreach ($offices as $office) {
                $options .= '<option value="'.$office->id.'">'.$office->getOfficeName().'</option>';
            }
        }
        return $options;
    }
}
