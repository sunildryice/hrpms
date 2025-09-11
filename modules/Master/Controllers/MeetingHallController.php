<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\MeetingHallRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Requests\MeetingHall\StoreRequest;
use Modules\Master\Requests\MeetingHall\UpdateRequest;

use DataTables;

class MeetingHallController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param MeetingHallRepository $meetingHall
     * @return void
     */
    public function __construct(
        MeetingHallRepository $meetingHall,
        OfficeRepository $offices
    )
    {
        $this->meetingHall = $meetingHall;
        $this->offices = $offices;
    }

    /**
     * Display a listing of the meeting hall.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->meetingHall->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function ($row) {
                    return $row->getOfficeName();
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-meetingHall-modal-form" href="';
                    $btn .= route('master.meeting.hall.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    if(date('Y-m-d',strtotime($row->created_at)) == date('Y-m-d')){
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('master.meeting.hall.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
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
        return view('Master::MeetingHall.index')
            ->withMeetingHall($this->meetingHall->all());
    }

    /**
     * Show the form for creating a new meeting hall.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $offices = $this->offices->select(['id', 'office_name'])->whereNotNull('activated_at')->get();
        return view('Master::MeetingHall.create')
            ->withOffices($offices);
    }

    /**
     * Store a newly created meeting hall in storage.
     *
     * @param \Modules\Master\Requests\MeetingHall\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $meetingHall = $this->meetingHall->create($inputs);
        if ($meetingHall) {
            return response()->json(['status' => 'ok',
                'meeting hall' => $meetingHall,
                'message' => 'Meeting Hall is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Meeting Hall can not be added.'], 422);
    }

    /**
     * Display the specified meeting hall.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meetingHall = $this->meetingHall->find($id);
        return response()->json(['status' => 'ok', 'meeting hall' => $meetingHall], 200);
    }

    /**
     * Show the form for editing the specified meeting hall.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $meetingHall = $this->meetingHall->find($id);
        $offices = $this->offices->select(['id', 'office_name'])->whereNotNull('activated_at')->get();
        return view('Master::MeetingHall.edit')
            ->withMeetingHall($meetingHall)
            ->withOffices($offices);
    }

    /**
     * Update the specified meeting hall in storage.
     *
     * @param \Modules\Master\Requests\MeetingHall\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $meetingHall = $this->meetingHall->update($id, $inputs);
        if ($meetingHall) {
            return response()->json(['status' => 'ok',
                'meetingHall' => $meetingHall,
                'message' => 'Meeting Hall is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Meeting Hall can not be updated.'], 422);
    }

    /**
     * Remove the specified meeting hall from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->meetingHall->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Meeting Hall is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Meeting Hall can not deleted.',
        ], 422);
    }
}
