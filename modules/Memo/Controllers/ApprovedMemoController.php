<?php

namespace Modules\Memo\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Memo\Repositories\MemoRepository;
use Yajra\DataTables\DataTables;

class ApprovedMemoController extends Controller
{
    public function __construct(
        MemoRepository $memos
    )
    {
        $this->memos = $memos;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->memos->getApproved();

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
            })->addColumn('attachment', function ($row) {
                $attachment = '';
                if ($row->attachment) {
                    $attachment .= '<div class="media"><a href="' . asset('storage/' . $row->attachment) . '" target="_blank" class="fs-5" title="View Attachment">';
                    $attachment .= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                }
                return $attachment;
            })->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('approved.memo.show', $row->id) . '" rel="tooltip" title="View Memo">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                $btn .= route('approved.memo.print', $row->id) . '" rel="tooltip" title="Print Memo"><i class="bi bi-printer"></i></a>';
                return $btn;
            })
            ->rawColumns(['attachment', 'action', 'status'])
            ->make(true);
        }

        return view('Memo::Approved.index');
    }

    public function show(Request $request, $id)
    {
        $memo = $this->memos->find($id);

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
        return view('Memo::Approved.show')
            ->withApprovedDate($approved_date)
            ->withSubmittedDate($submitted_date)
            ->withMemo($memo);
    }

    public function print(Request $request, $id)
    {
        $memo = $this->memos->find($id);

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
        return view('Memo::Approved.print')
            ->withApprovedDate($approved_date)
            ->withSubmittedDate($submitted_date)
            ->withMemo($memo);
    }
}