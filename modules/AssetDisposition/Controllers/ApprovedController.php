<?php

namespace Modules\AssetDisposition\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\AssetDisposition\Repositories\DispositionRequestRepository;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\DispositionTypeRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApprovedController extends Controller
{
    private $user;

    public function __construct(
        protected DispositionRequestRepository $dispositionRequest,
        protected DispositionTypeRepository $dispositionTypes,
        protected AssetRepository $assets,
        UserRepository $user,
    ) {
        $this->user = $user;

    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $inputs = $this->dispositionRequest->getApproved();

            return DataTables::of($inputs)
                ->addIndexColumn()
                ->addColumn('office_name', function ($row) {
                    return $row->office->office_name;
                })
                ->addColumn('assets', function ($row) {
                    return implode(',<br> ', $row->getDisposedAssetCodes());
                })
                ->addColumn('disposition_type', function ($row) {
                    return $row->getDispositionType();
                })->addColumn('disposition_date', function ($row) {
                    return $row->getDispositionDate();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('approver', function ($row) {
                    return $row->getApproverName();
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.asset.disposition.show', $row->id).'" rel="tooltip" title="View  Asset Disposition">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'assets'])
                ->make(true);
        }

        return view('AssetDisposition::Approved.index');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $dispositionRequest = $this->dispositionRequest->find($id);
        $requester = $dispositionRequest->requester->employee;

        return view('AssetDisposition::Approved.show')
            ->withDispositionRequest($dispositionRequest)
            ->withRequester($requester);
    }

    public function print($id)
    {
        $dispositionRequest = $this->dispositionRequest->find($id);
        $requester = $dispositionRequest->requester->employee;

        return view('AssetDisposition::Approved.print')
            ->withAssetDisposition($dispositionRequest)
            ->withRequester($requester);
    }
}
