<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\HolidayRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Requests\Holiday\StoreRequest;
use Modules\Master\Requests\Holiday\UpdateRequest;

use DataTables;

class HolidayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param HolidayRepository $holidays
     * @return void
     */
    public function __construct(
        HolidayRepository $holidays,
        OfficeRepository $offices,
    ) {
        $this->holidays = $holidays;
        $this->offices = $offices;
    }

    /**
     * Display a listing of the holiday.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->holidays->select(['*'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-holiday-modal-form" href="';
                    $btn .= route('master.holidays.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.holidays.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('holiday_date', function ($row) {
                    return $row->getHolidayDate();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->addColumn('holiday_date', function ($row) {
                    $displayDate = $row->getHolidayDate();
                    $sortDate = $row->holiday_date->format('Y-m-d');

                    return '<span data-order="' . $sortDate . '">' . $displayDate . '</span>';
                })
                ->rawColumns(['action', 'holiday_date'])
                ->make(true);
        }
        return view('Master::Holiday.index')
            ->withHolidays($this->holidays->all());
    }

    /**
     * Show the form for creating a new holiday.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $offices = $this->offices->getActiveOffices();
        return view('Master::Holiday.create')
            ->withOffices($offices);
    }

    /**
     * Store a newly created holiday in storage.
     *
     * @param \Modules\Master\Requests\Holiday\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $inputs['created_by'] = auth()->id();
        $holiday = $this->holidays->create($inputs);
        if ($holiday) {
            return response()->json([
                'status' => 'ok',
                'holiday' => $holiday,
                'message' => 'Holiday is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Holiday can not be added.'
        ], 422);
    }

    /**
     * Display the specified holiday.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $holiday = $this->holidays->find($id);
        return response()->json(['status' => 'ok', 'holiday' => $holiday], 200);
    }

    /**
     * Show the form for editing the specified holiday.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $holiday = $this->holidays->find($id);
        $offices = $this->offices->getActiveOffices();
        return view('Master::Holiday.edit')
            ->withHoliday($holiday)
            ->withOffices($offices);
    }

    /**
     * Update the specified holiday in storage.
     *
     * @param \Modules\Master\Requests\Holiday\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $holiday = $this->holidays->update($id, $inputs);
        if ($holiday) {
            return response()->json([
                'status' => 'ok',
                'holiday' => $holiday,
                'message' => 'Holiday is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Holiday can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified holiday from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->holidays->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Holiday is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Holiday can not deleted.',
        ], 422);
    }
}
