<?php

namespace Modules\Memo\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Memo\Repositories\MemoRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Memo\Notifications\MemoApproved;
use Modules\Memo\Notifications\MemoForRecommend;
use Modules\Memo\Notifications\MemoRecommended;
use Modules\Memo\Notifications\MemoRejected;
use Modules\Memo\Notifications\MemoReturned;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\Memo\Requests\Approve\StoreRequest;
use Modules\Memo\Requests\Approve\UpdateRequest;

use DataTables;
use DB;

class ApproveMemoController extends Controller
{
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
     * Display a listing of the travel request by employee id.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->memo->with(['to', 'createdBy'])
                ->where(function ($query) use ($userId) {
                    $query->where('status_id', config('constant.RECOMMENDED_STATUS'))
                        ->whereHas('to', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });
                })->orWhere(function ($query) use ($userId) {
                    $query->where('status_id', config('constant.SUBMITTED_STATUS'))
                        ->where(function ($subQuery) use ($userId) {
                            $subQuery->whereHas('through', function ($q) use ($userId) {
                                $q->where('user_id', $userId);
                            });
                            $subQuery->orWhere(function ($q) use ($userId) {
                                $q->whereDoesntHave('through');
                                $q->whereHas('to', function ($qq) use ($userId) {
                                    $qq->where('user_id', $userId);
                                });
                            });
                        });
                })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('memo_number', function ($row) {
                    return $row->getMemoNumber();
                })->addColumn('memo_date', function ($row) {
                    return $row->getMemoDate();
                })->addColumn('requester', function ($row) {
                    return $row->getCreatedBy();
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
                    if ($authUser->can('approve', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approve.memo.create', $row->id) . '" rel="tooltip" title="Approve"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['attachment', 'action', 'status'])
                ->make(true);
        }

        return view('Memo::Approve.index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $memo = $this->memo->find($id);
        $this->authorize('approve', $memo);
        $memoTo = $memo->to ? $memo->to->pluck('id')->toArray() : [];
        $memoThrough = $memo->through ? $memo->through->pluck('id')->toArray() : [];
        $memoFrom = $memo->from ? $memo->from->pluck('id')->toArray() : [];
        $attachment = '';
        if ($memo->attachment != NULL) {
            $attachment = asset('storage/' . $memo->attachment);
        }
        return view('Memo::Approve.create')
            ->withAttachment($attachment)
            ->withMemo($memo)
            ->withSelectedMemoTo($memoTo)
            ->withSelectedMemoThrough($memoThrough)
            ->withSelectedMemoFrom($memoFrom)
            ->withUserId(auth()->id());
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $userId = auth()->id();
        $memo = $this->memo->find($id);
        $this->authorize('approve', $memo);
        $inputs = $request->validated();
        $inputs['user_id'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $memo = $this->memo->approve($memo->id, $inputs);

        if ($memo) {
            $message = '';
            if ($memo->status_id == 2) {
                $message = 'Memo is successfully returned.';
                $memo->createdBy->notify(new MemoReturned($memo));
            } else if ($memo->status_id == 4) {
                $message = 'Memo is successfully recommended.';
                $memo->createdBy->notify(new MemoRecommended($memo));
                foreach ($memo->to as $memo_to) {
                    $memo_to->notify(new MemoForRecommend($memo));
                }
            } else if ($memo->status_id == 8) {
                $message = 'Memo is successfully rejected.';
                $memo->createdBy->notify(new MemoRejected($memo));
            } else {
                $message = 'Memo is successfully approved.';
                $memo->createdBy->notify(new MemoApproved($memo));
            }

            return redirect()->route('approve.memo.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Memo can not be approved.');
    }
}
