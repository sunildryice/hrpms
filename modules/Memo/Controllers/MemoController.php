<?php

namespace Modules\Memo\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Memo\Repositories\MemoRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Memo\Notifications\MemoSubmitted;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\Memo\Requests\StoreRequest;
use Modules\Memo\Requests\UpdateRequest;

use DataTables;
use DB;

class MemoController extends Controller
{
    protected $memo;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees ,
     * @param MemoRepository $memo ,
     * @param RoleRepository $roles ,
     * @param StatusRepository $status ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        EmployeeRepository $employees,
        MemoRepository     $memo,
        RoleRepository     $roles,
        StatusRepository   $status,
        UserRepository     $user
    )
    {
        $this->employees = $employees;
        $this->memo = $memo;
        $this->roles = $roles;
        $this->status = $status;
        $this->user = $user;
        $this->destinationPath = 'memo';
    }

    /**
     * Display a listing of the memo by user id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        // $this->authorize('memo-request');
        $userId = auth()->id();

        if ($request->ajax()) {
            $data = $this->memo->with(['createdBy', 'logs'])
                ->where(function ($q) use ($authUser) {
                    $q->where('created_by', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('memo_number', function ($row) {
                    return $row->getMemoNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('memo_date', function ($row) {
                    return $row->getMemoDate();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('attachment', function ($row) use ($authUser) {
                    $attachment = '';
                    if ($row->attachment) {
                        $attachment .= '<div class="media"><a href="' . asset('storage/' . $row->attachment) . '" target="_blank" class="fs-5" title="View Attachment">';
                        $attachment .= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                    }
                    return $attachment;
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('memo.view', $row->id) . '" rel="tooltip" title="View Memo">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                        $btn .= route('memo.print', $row->id) . '" rel="tooltip" title="Print Memo"><i class="bi bi-printer"></i></a>';
                    }
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('memo.edit', $row->id) . '" rel="tooltip" title="Edit Memo"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('memo.destroy', $row->id) . 'rel="tooltip" title="Delete Memo">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    } else if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-record"';
                        $btn .= 'data-href = "' . route('memo.amend', $row->id) . '" data-number = "' . $row->getMemoNumber() . '"  title="Amend Memo">';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['attachment', 'action', 'status'])
                ->make(true);
        }

        return view('Memo::index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('manage-memo');
        $authUser = auth()->user();
        $approvers = $this->user->permissionBasedUsers('approve-memo');
        $supervisors = $this->user->getSupervisor($authUser);
        return view('Memo::create')
            ->withApprovers($approvers)
            ->withSupervisors($supervisors)
            ->withUsers($this->user->select(['*'])->whereNotNull('activated_at')->whereNot('id', $authUser->id)->orderBy('full_name', 'asc')->get());

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-memo');
        $userId = auth()->id();
        $inputs = $request->validated();
        $employee_id = auth()->user()->employee_id;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee_id, time() . '_memo.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['status_id'] = config('constant.CREATED_STATUS');
        $inputs['created_by'] = $userId;
        $inputs['requester_id'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $memo = $this->memo->create($inputs);

        if ($memo) {
            if ($memo->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Memo is successfully submitted.';
                if ($memo->memoThrough->count() > 0) {
                    foreach ($memo->memoThrough as $memo_through) {
                        $memo_through->notify(new MemoSubmitted($memo));
                    }
                } else {
                    foreach ($memo->to as $memo_to) {
                        $memo_to->notify(new MemoSubmitted($memo));
                    }
                }
            } else {
                $message = 'Memo is successfully added.';
            }
            return redirect()->route('memo.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Memo can not be added.');

    }

    /**
     * Show the form for editing the specified Memo.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function edit($id)
    {
        $authUser = auth()->user();
        $memo = $this->memo->find($id);
        $this->authorize('update', $memo);
        $memoTo = $memo->to ? $memo->to->pluck('id')->toArray() : [];
        $memoThrough = $memo->memoThrough ? $memo->memoThrough->pluck('id')->toArray() : [];
        $memoFrom = $memo->from ? $memo->from->pluck('id')->toArray() : [];
        $attachment = '';
        if ($memo->attachment != NULL) {
            $attachment = asset('storage/' . $memo->attachment);
        }
        $approvers = $this->user->permissionBasedUsers('approve-memo');
        $supervisors = $this->user->getSupervisor($authUser);

        return view('Memo::edit')
            ->withApprovers($approvers)
            ->withAttachment($attachment)
            ->withMemo($memo)
            ->withSelectedMemoTo($memoTo)
            ->withSelectedMemoThrough($memoThrough)
            ->withSelectedMemoFrom($memoFrom)
            ->withSupervisors($supervisors)
            ->withUsers($this->user->select(['*'])->whereNotNull('activated_at')->whereNot('id', $authUser->id)->orderBy('full_name', 'asc')->get());
    }

    /**
     * Update the specified employee in storage.
     *
     * @param \Modules\Employee\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
//        $this->authorize('manage-employee');
        $memo = $this->memo->find($id);
        $this->authorize('update', $memo);
        $inputs = $request->validated();
        $employee_id = auth()->user()->employee_id;
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee_id, time() . '_memo.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $memo = $this->memo->update($id, $inputs);
        if ($memo) {
            if ($memo->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Memo is successfully submitted.';
                if ($memo->memoThrough->isNotEmpty()) {
                    foreach ($memo->memoThrough as $memo_through) {
                        $memo_through->notify(new MemoSubmitted($memo));
                    }
                } else {
                    foreach ($memo->to as $memo_to) {
                        $memo_to->notify(new MemoSubmitted($memo));
                    }
                }
            } else {
                $message = 'Memo is successfully updated.';
            }
            return redirect()->route('memo.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Memo can not be updated.');
    }

    /**
     * Veiw the specified maintenance request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */

    public function view($id)
    {
        $memo = $this->memo->find($id);

        $approved_date = '';
        $submitted_date = '';
        foreach ($memo->logs as $log) {
            if ($log->status_id == 3) {
                $submitted_date = $log->created_at;
            }
            if ($log->status_id == 6) {
                $approved_date = $log->created_at;
            }
        }
        return view('Memo::show')
            ->withApprovedDate($approved_date)
            ->withSubmittedDate($submitted_date)
            ->withMemo($memo);
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $memo = $this->memo->find($id);
        $this->authorize('delete', $memo);
        $flag = $this->memo->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Memo is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Memo can not deleted.',
        ], 422);
    }

    /**
     * Show the specified purchase order in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printMemo($id)
    {
        $authUser = auth()->user();
        $memo = $this->memo->find($id);
        $this->authorize('print', $memo);
        $approved_date = '';
        $submitted_date = '';
        foreach ($memo->logs as $log) {
            if ($log->status_id == 3) {
                $submitted_date = $log->created_at;
            }
            if ($log->status_id == 6) {
                $approved_date = $log->created_at;
            }
        }
        return view('Memo::print')
            ->withApprovedDate($approved_date)
            ->withSubmittedDate($submitted_date)
            ->withMemo($memo);
    }

    public function amend($id)
    {
        $memo = $this->memo->find($id);
        $this->authorize('amend', $memo);

        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->memo->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Memo is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Memo can not amended.',
        ], 422);
    }
}
