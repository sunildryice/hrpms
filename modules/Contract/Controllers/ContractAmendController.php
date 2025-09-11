<?php

namespace Modules\Contract\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Contract\Repositories\ContractAmendmentRepository;
use Modules\Contract\Repositories\ContractRepository;
use Modules\Contract\Requests\AmendStoreRequest;

class ContractAmendController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ContractAmendmentRepository $contractAmendments,
        ContractRepository $contracts
    ) {
        $this->contractAmendments = $contractAmendments;
        $this->contracts = $contracts;
        $this->destinationPath = 'contracts';
    }

    /**
     * Display a listing of the contract.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $contractId)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->contractAmendments->with(['contract.latestAmendment'])->select(['*'])
                ->whereContractId($contractId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('attachment', function ($row) {
                    $attachment = 'File does not exists.';
                    if (file_exists('storage/'.$row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "'.asset('storage/'.$row->attachment).'" target = "_blank" class="fs-5" ';
                        $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }

                    return $attachment;
                })
                ->addColumn('contract_amount', function ($row) {
                    return number_format($row->contract_amount, 2);
                })
                ->addColumn('expiry_date', function ($row) {
                    return $row->getExpiryDate();
                })->addColumn('remarks', function ($row) {
                    return $row->getShortRemarks();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-contract-modal-form" href="';
                    $btn .= route('contracts.amendments.edit', [$row->contract_id, $row->id]).'" rel="tooltip" title="Edit Amendment"><i class="bi-pencil-square"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('contracts.amendments.destroy', [$row->contract_id, $row->id]).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'attachment'])
                ->make(true);
        }

        return true;
    }

    /**
     * Show the form for creating amend contract.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $id)
    {
        $contract = $this->contracts->find($id);

        return view('Contract::Amend.create')
            ->withContract($contract);
    }

    /**
     * Store a newly created contract in storage.
     *
     * @return \Illuminate\Http\JSonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(AmendStoreRequest $request, $id)
    {
        $contract = $this->contracts->find($id);
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath.'/'.$contract->id, time().$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $contract = $this->contracts->amend($id, $inputs);

        if ($contract) {
            return response()->json(['status' => 'ok',
                'contract' => $contract,
                'message' => 'Contract is successfully amended.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Contract can not be amended.'], 422);
    }

    /**
     * Show the form for editing amend contract.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Request $request, $contractId, $id)
    {
        $contract = $this->contracts->find($contractId);
        $contractAmend = $this->contractAmendments->find($id);

        return view('Contract::Amend.edit')
            ->withContract($contract)
            ->withContractAmend($contractAmend);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AmendStoreRequest $request, $contractId, $id)
    {
        $contractAmendment = $this->contractAmendments->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath.'/'.$contractAmendment->contract_id, time().$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $contractAmendment = $this->contractAmendments->update($id, $inputs);

        if ($contractAmendment) {
            return response()->json(['status' => 'ok',
                'contract' => $contractAmendment->contract,
                'contractAmendment' => $contractAmendment,
                'message' => 'Contract amendment is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Contract amendment can not be updated.'], 422);
    }

    /**
     * Remove the specified amended contract from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($contractId, $id)
    {
        $flag = $this->contractAmendments->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Contract amendment is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Contract amendment can not deleted.',
        ], 422);
    }
}
