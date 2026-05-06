<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\CountryRepository;
use Modules\Master\Requests\Country\StoreRequest;
use Modules\Master\Requests\Country\UpdateRequest;
use DataTables;

class CountryController extends Controller
{
    protected $countries;

    public function __construct(CountryRepository $countries)
    {
        $this->countries = $countries;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->countries->select([
                'id', 'title', 'created_by', 'updated_at'
            ])->orderBy('title');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-country-modal-form" href="';
                    $btn .= route('master.countries.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.countries.destroy', $row->id) . '">';
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

        return view('Master::Country.index')
            ->withCountries($this->countries->all());
    }

    public function create()
    {
        return view('Master::Country.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();

        $country = $this->countries->create($inputs);

        if ($country) {
            return response()->json(['status' => 'ok',
                'country' => $country,
                'message' => 'Country is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Country can not be added.'], 422);
    }

    public function show($id)
    {
        $country = $this->countries->find($id);
        return response()->json(['status' => 'ok', 'country' => $country], 200);
    }

    public function edit($id)
    {
        $country = $this->countries->find($id);
        return view('Master::Country.edit')
            ->withCountry($country);
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $country = $this->countries->update($id, $inputs);

        if ($country) {
            return response()->json(['status' => 'ok',
                'country' => $country,
                'message' => 'Country is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Country can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->countries->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Country is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Country can not deleted.',
        ], 422);
    }
}
