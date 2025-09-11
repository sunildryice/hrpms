<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\PartnerOrganizationRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Mfr\Repositories\AgreementRepository;
use Modules\Mfr\Requests\Agreement\StoreRequest;
use Modules\Mfr\Requests\Agreement\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class AgreementController extends Controller
{
    public function __construct(
        protected AgreementRepository $agreement,
        protected UserRepository $user,
        protected PartnerOrganizationRepository $partners,
        protected DistrictRepository $districts,
        protected ProjectCodeRepository $projectCodes
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->agreement->with('latestAmendment')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('partner_organization', function ($row) {
                    return $row->partnerOrganization->name;
                })
                ->addColumn('district', function ($row) {
                    return $row->district->district_name;
                })
                ->addColumn('effective_from', function ($row) {
                    return $row->getEffectiveFromDate();
                })
                ->addColumn('effective_to', function ($row) {
                    return $row->getEffectiveToDate();
                })
                ->addColumn('approved_budget', function ($row) {
                    // dd( $row->getApprovedBudget());
                    return $row->getApprovedBudget();
                })
                ->addColumn('action', function ($row) use ($authUser) {

                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('mfr.agreement.show', $row->id).'" rel="tooltip" title="Show Agreement"><i class="bi bi-eye"></i></a>';

                    // if($authUser->can('update', $row)){
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('mfr.agreement.edit', $row->id).'" rel="tooltip" title="Edit Agreement"><i class="bi bi-pencil-square"></i></a>';
                    // }
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('mfr.agreement.show.transactions', $row->id).'" rel="tooltip" title="View Transactions"><i class="bi bi-collection"></i></a>';
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('mfr.agreement.destroy', $row->id).'" rel="tooltip" title="Delete GRN">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.print', $row->transactions()->select('id')->where('status_id', 6)->latest()->first()->id).'" target="_blank" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }

                    return $btn;
                })
                ->make(true);
        }

        return view('Mfr::Agreements.index');
    }

    public function create()
    {
        return view('Mfr::Agreements.create')->with('partnerOrganizations', $this->partners->get())
            ->with('districts', $this->districts->get())
            ->with('projectCodes', $this->projectCodes->get());
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $dupAgreement = $this->agreement->select(['*'])
            ->where('partner_organization_id', $inputs['partner_organization_id'])
            ->where('project_id', '=', $inputs['project_id'])
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '<=', $inputs['effective_from'])
                        ->whereDate('effective_to', '>=', $inputs['effective_from']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '<=', $inputs['effective_to'])
                        ->whereDate('effective_to', '>=', $inputs['effective_to']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '>', $inputs['effective_from'])
                        ->whereDate('effective_to', '<', $inputs['effective_to']);
                });
            })->first();

        if ($dupAgreement) {
            return redirect()->back()->withInput()
                ->withWarningMessage('Agreement with the partner organization already exists');
        }

        $inputs['created_by'] = auth()->user()->id;
        $agreement = $this->agreement->create($inputs);

        if ($agreement) {
            return redirect()->route('mfr.agreement.edit', $agreement->id)->withSuccessMessage('Agreement with partner organization created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Agreement with partner organization could not be created.');
        }
    }

    public function show($agreeemntId)
    {
        $agreement = $this->agreement->find($agreeemntId);

        return view('Mfr::Agreements.show')->with('agreement', $agreement);
    }

    public function showTransactions(Request $request, $agreementId)
    {
        $authUser = auth()->user();
        $agreement = $this->agreement->find($agreementId);
        if ($request->ajax()) {
            $data = $agreement->transactions()->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('month', function ($row) {
                    return $row->transaction_date->format('F');
                    // return date('F', mktime(0, 0, 0, $row->month, 10));
                })
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('Y-m-d');
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('questioned_cost', function ($row) {
                    return $row->getQuestionedCost();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('mfr.transaction.show', [$row->id]).'" rel="tooltip" title="View transaction"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.edit', [$row->id]).'" rel="tooltip" title="Edit transaction"><i class="bi bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.print', $row->id).'" target="_blank" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }

                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('mfr.transaction.delete', $row->id).'">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Mfr::Transaction.index', ['agreement' => $agreement]);
    }

    public function edit($id)
    {
        $agreement = $this->agreement->find($id);

        return view('Mfr::Agreements.edit', [
            'agreement' => $agreement,
            'hasAmendments' => $agreement->amendments()->select('id')->count()
        ])->with('partnerOrganizations', $this->partners->get())
            ->with('districts', $this->districts->get())
            ->with('projectCodes', $this->projectCodes->get());
    }

    public function update(UpdateRequest $request, $id)
    {
        $agreement = $this->agreement->find($id);
        // $this->authorize('update', $agreement);
        $inputs = $request->validated();
        $inputs['effective_to'] = $inputs['effective_to'] ?? $agreement->getEffectiveToDate();

        $dupAgreement = $this->agreement->select(['*'])
            ->where('partner_organization_id', $inputs['partner_organization_id'])
            ->where('project_id', '=', $inputs['project_id'])
            ->whereNot('id', $agreement->id)
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '<=', $inputs['effective_from'])
                        ->whereDate('effective_to', '>=', $inputs['effective_from']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '<=', $inputs['effective_to'])
                        ->whereDate('effective_to', '>=', $inputs['effective_to']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('effective_from', '>', $inputs['effective_from'])
                        ->whereDate('effective_to', '<', $inputs['effective_to']);
                });
            })->first();

        if ($dupAgreement) {
            return redirect()->back()->withInput()
                ->withWarningMessage('Agreement with the partner organization already exists');
        }

        $inputs['created_by'] = auth()->user()->id;
        $agreement = $this->agreement->update($agreement->id, $inputs);

        if ($agreement) {
            return redirect()->route('mfr.agreement.edit', $agreement->id)->withSuccessMessage('Agreement with partner organization updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Agreement with partner organization could not be updated.');
        }
    }

    public function destroy($agreementId)
    {
        $deleted = $this->agreement->destroy($agreementId);
        if ($deleted) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Agreement deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Agreement cannot be deleted.',
            ], 422);
        }
    }

    public function amend(Request $request, $agreementId)
    {
        $agreement = $this->agreement->find($agreementId);
        $this->authorize('amend', $this->agreement->find($agreementId));
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);
        $inputs['status_id'] = config('constant.RETURNED_STATUS');
        $inputs['user_id'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $flag = $this->agreement->amend($agreement->id, $inputs);

        if ($flag) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Attendance reversed successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Attendance cannot be reversed.',
        ], 422);
    }

    public function print($agreementId)
    {
        $agreement = $this->agreement->find($agreementId);

        return view('Mfr::Agreement.print', ['agreement' => $agreement]);
    }
}
