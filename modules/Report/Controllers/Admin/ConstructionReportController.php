<?php

namespace Modules\Report\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Report\Exports\Admin\ConstructionReportExport;

class ConstructionReportController extends Controller
{
    private $constructions;
    private $districts;

    public function __construct(
        ConstructionRepository $constructions,
        DistrictRepository $districts,
    )
    {
        $this->constructions = $constructions;
        $this->districts = $districts;
    }
    public function index(Request $request)
    {
        $data = $this->constructions->query();

        if ($request->filled('district')) {
            $data->where('district_id', $request->district);
        }

        if ($request->filled('year')) {
            $data->whereYear('signed_date', $request->year);
        }

        if ($request->filled('donor')) {
            $data->where('donor', 'like', '%'.$request->donor.'%');
        }

        $data = $data->orderBy('signed_date')->get();

        $array = [
            'constructions' => $data,
            'districts'     => $this->districts->getDistricts(),
            'years'         => ['2020', '2021', '2022', '2023', '2024', '2025']
        ];

        return view('Report::Admin.Construction.index', $array);
    }

    public function export(Request $request)
    {
        return new ConstructionReportExport((object)$request->only('district', 'year', 'donor'));
    }
}
