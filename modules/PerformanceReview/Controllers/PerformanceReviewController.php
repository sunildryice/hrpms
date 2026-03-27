<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Models\PerformanceReviewType;
use Modules\PerformanceReview\Notifications\PerformanceReviewCreated;
use Modules\PerformanceReview\Notifications\PerformanceReviewSubmitted;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\PerformanceReview\Requests\PerformanceReview\StoreRequest;
use Modules\PerformanceReview\Requests\PerformanceReview\UpdateRequest;
use Yajra\DataTables\DataTables;

class PerformanceReviewController extends Controller
{

    public function __construct(
        protected EmployeeRepository $employee,
        protected PerformanceReviewRepository $performanceReview,
        protected PerformanceReviewType $performanceReviewType,
        protected PerformanceReviewQuestion $performanceReviewQuestion,
        protected FiscalYearRepository $fiscalYear
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('managePerformance', new PerformanceReview);

        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->performanceReview->with(['employee', 'fiscalYear', 'status', 'reviewType'])
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($performanceReview) {
                    return $performanceReview->getEmployeeName();
                })
                ->addColumn('fiscal_year', function ($performanceReview) {
                    return $performanceReview->getFiscalYear();
                })
                ->addColumn('review_type', function ($performanceReview) {
                    return $performanceReview->getReviewType();
                })
                ->addColumn('review_from', function ($performanceReview) {
                    return $performanceReview->getReviewFromDate();
                })
                ->addColumn('review_to', function ($performanceReview) {
                    return $performanceReview->getReviewToDate();
                })
                ->addColumn('deadline_date', function ($performanceReview) {
                    return $performanceReview->getDeadlineDate();
                })
                ->addColumn('status', function ($performanceReview) {
                    return '<span class="' . $performanceReview->getStatusClass() . '">' . $performanceReview->getStatus() . '</span>';
                })
                ->addColumn('action', function ($performanceReview) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $performanceReview)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.show', [$performanceReview->id]) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';
                    }

                    if ($authUser->can('edit', $performanceReview)) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.edit', $performanceReview->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('print', $performanceReview)) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.print', [$performanceReview->id]) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }

                    // $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                    // $btn .= route('performance.fill', [$performanceReview->id]).'" rel="tooltip" title="Fill Performance Review Form"><i class="bi bi-ui-checks"></i></a>';
    
                    if ($authUser->can('delete', $performanceReview)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete" ';
                        $btn .= 'data-href="' . route('performance.destroy', [$performanceReview->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::index');
    }

    public function employeeIndex(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->performanceReview->where('requester_id', '=', auth()->user()->id)->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($performanceReview) {
                    return $performanceReview->getEmployeeName();
                })
                ->addColumn('fiscal_year', function ($performanceReview) {
                    return $performanceReview->getFiscalYear();
                })
                ->addColumn('review_type', function ($performanceReview) {
                    return $performanceReview->getReviewType();
                })
                ->addColumn('review_from', function ($performanceReview) {
                    return $performanceReview->getReviewFromDate();
                })
                ->addColumn('review_to', function ($performanceReview) {
                    return $performanceReview->getReviewToDate();
                })
                ->addColumn('status', function ($performanceReview) {
                    return '<span class="' . $performanceReview->getStatusClass() . '">' . $performanceReview->getStatus() . '</span>';
                })
                ->addColumn('action', function ($performanceReview) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('performance.employee.show', [$performanceReview->id]) . '" rel="tooltip" title="View Performance Review"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('employeeFill', $performanceReview)) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.fill', [$performanceReview->id]) . '" rel="tooltip" title="Fill Performance Review Form"><i class="bi bi-ui-checks"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::Employee.index');
    }

    public function create()
    {
        $this->authorize('managePerformance', new PerformanceReview);

        $employees = $this->employee->getActiveEmployees();
        $reviewTypes = $this->performanceReviewType->orderBy('id', 'desc')->get();
        $fiscalYears = $this->fiscalYear->getFiscalYears();
        $currentFiscalYearId = $this->fiscalYear->getCurrentFiscalYearId();

        return view('PerformanceReview::create', compact('employees', 'reviewTypes', 'fiscalYears', 'currentFiscalYearId'));
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('managePerformance', new PerformanceReview);

        $inputs = $request->validated();

        if ($request->has('employee_all')) {
            $employees = $this->employee->getActiveEmployees();
            foreach ($employees as $employee) {
                if (!$employee->user) {
                    continue;
                }
                $inputs['employee_id'] = $employee->id;
                $inputs['requester_id'] = $employee->user->id;

                $performanceReview = $this->performanceReview->where('employee_id', '=', $inputs['employee_id'])
                    ->where('fiscal_year_id', '=', $inputs['fiscal_year_id'])
                    ->where('review_type_id', '=', $inputs['review_type_id'])
                    ->first();
                if ($performanceReview) {
                    continue;
                }

                $performanceReview = $this->performanceReview->create($inputs);
                if ($performanceReview) {
                    $performanceReview->requester->notify(new PerformanceReviewCreated($performanceReview));
                }
            }
            return redirect()->route('performance.index')->withSuccessMessage('Performance review successfully created.');
        } else {
            $employee = $this->employee->find($inputs['employee_id']);
            $inputs['requester_id'] = $employee->user->id;

            $performanceReview = $this->performanceReview->where('employee_id', '=', $inputs['employee_id'])
                ->where('fiscal_year_id', '=', $inputs['fiscal_year_id'])
                ->where('review_type_id', '=', $inputs['review_type_id'])
                ->first();
            if ($performanceReview) {
                return redirect()->route('performance.create')->withWarningMessage('Performance review already exists.')->withInput();
            }

            $performanceReview = $this->performanceReview->create($inputs);
            if ($performanceReview) {
                $performanceReview->requester->notify(new PerformanceReviewCreated($performanceReview));
                return redirect()->route('performance.index')->withSuccessMessage('Performance review successfully created.');
            } else {
                return redirect()->route('performance.create')->withWarningMessage('Performance review could not be created.');
            }
        }
    }

    public function show($id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('view', $performanceReview);

        $record = array(
            'performanceReview' => $performanceReview,
            'groupBQuestions' => $this->performanceReviewQuestion->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->performanceReviewQuestion->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->performanceReviewQuestion->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->performanceReviewQuestion->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->performanceReviewQuestion->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->performanceReviewQuestion->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->performanceReviewQuestion->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        );

        if ($performanceReview->getReviewType() == 'Annual Review') {

            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }
            // if (is_null($midTermReview)) {
            //     return redirect()->back()->withWarningMessage('Mid-Term Performance Review not filled yet.');
            // }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }

            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::AnnualPerformanceReview.show', $record, compact('keyGoalReview', 'midTermReview', 'keygoals', 'professionalDevelopmentPlan'));

        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            $keygoals = $keygoals->concat($performanceReview->keyGoals()->where('type', 'current')->get());
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::MidTermPerformanceReview.show', $record, compact('keygoals', 'professionalDevelopmentPlan'));
        } else {
            return view('PerformanceReview::KeyGoalsReview.show', [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            ]);
        }
    }

    public function print($id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('managePerformance', $performanceReview);

        $record = array(
            'performanceReview' => $performanceReview,
            'groupBQuestions' => $this->performanceReviewQuestion->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->performanceReviewQuestion->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->performanceReviewQuestion->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->performanceReviewQuestion->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->performanceReviewQuestion->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->performanceReviewQuestion->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->performanceReviewQuestion->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        );

        if ($performanceReview->getReviewType() == 'Annual Review') {
            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }
            // if (is_null($midTermReview)) {
            //     return redirect()->back()->withWarningMessage('Mid-Term Performance Review not filled yet.');
            // }
            //
            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }


            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::AnnualPerformanceReview.print', $record, compact('keyGoalReview', 'midTermReview', 'keygoals', 'professionalDevelopmentPlan'));

        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::MidTermPerformanceReview.print', $record, compact('keygoals', 'professionalDevelopmentPlan'));
        } else {
            return view('PerformanceReview::KeyGoalsReview.print', [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            ]);
        }
    }


    public function employeeShow($id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('selfView', $performanceReview);

        $record = array(
            'performanceReview' => $performanceReview,
            'groupBQuestions' => $this->performanceReviewQuestion->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->performanceReviewQuestion->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->performanceReviewQuestion->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->performanceReviewQuestion->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->performanceReviewQuestion->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->performanceReviewQuestion->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->performanceReviewQuestion->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        );

        if ($performanceReview->getReviewType() == 'Annual Review') {
            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }

            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::Employee.AnnualPerformanceReview.show', [
                ...$record,
                'keyGoalReview' => $keyGoalReview,
                'midTermReview' => $midTermReview,
                'keygoals' => $keygoals,
                'performanceReview' => $performanceReview,
                'challenges' => $performanceReview->challenges,
                'coreCompetencies' => $performanceReview->coreCompetencies,
            ]);
        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            $keygoals = $keygoals->concat($performanceReview->keyGoals()->where('type', 'current')->get());
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::Employee.MidTermPerformanceReview.show', $record, compact('keygoals', 'professionalDevelopmentPlan'));
        } else {
            return view('PerformanceReview::Employee.KeyGoalsReview.show', [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            ]);
        }
    }

    public function edit($performanceReview)
    {
        $performanceReview = $this->performanceReview->find($performanceReview);

        $this->authorize('edit', $performanceReview);

        return view('PerformanceReview::edit')->withPerformanceReview($performanceReview);
    }

    public function update(UpdateRequest $request, $performanceReview)
    {
        $inputs = $request->validated();
        $performanceReview = $this->performanceReview->find($performanceReview);
        $this->authorize('edit', $performanceReview);
        $performanceReview->update($inputs);
        return redirect()->route('performance.index')->withSuccessMessage('Performance review successfully updated.');
    }

    public function destroy($id)
    {
        $performanceReview = $this->performanceReview->find($id);
        $flag = $this->performanceReview->destroy($id);
        if ($flag) {
            return response()->json(['type' => 'success', 'message' => 'Performance review successfully deleted.'], 200);
        } else {
            return response()->json(['type' => 'error', 'message' => 'Performance review could not be deleted.'], 422);
        }
    }

    public function fill($id)
    {
        $performanceReview = $this->performanceReview->with(['employee'])->find($id);

        $this->authorize('employeeFill', $performanceReview);

        $record = array(
            'performanceReview' => $performanceReview,
            'groupBQuestions' => $this->performanceReviewQuestion->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->performanceReviewQuestion->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->performanceReviewQuestion->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->performanceReviewQuestion->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->performanceReviewQuestion->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->performanceReviewQuestion->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->performanceReviewQuestion->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        );

        if ($performanceReview->getReviewType() == 'Annual Review') {
            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }
            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');

            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }

            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();

            return view('PerformanceReview::AnnualPerformanceReview.create', [
                ...$record,
                'keyGoalReview' => $keyGoalReview,
                'midTermReview' => $midTermReview,
                'keygoals' => $keygoals,
                'performanceReview' => $performanceReview,
                'challenges' => $performanceReview->challenges,
                'coreCompetencies' => $performanceReview->coreCompetencies,
            ]);
        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            // $keygoals = $keygoals->concat($performanceReview->keyGoals()->where('type', 'current')->get());

            $newKeyGoals = $performanceReview->keyGoals()->where('type', 'current')->get();
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::MidTermPerformanceReview.create', $record, compact('keyGoalReview', 'keygoals', 'professionalDevelopmentPlan', 'newKeyGoals'))
                ->with('performanceReview', $performanceReview)
                ->with('challenges', $performanceReview->challenges ?? collect())
                ->with('coreCompetencies', $performanceReview->coreCompetencies ?? collect());
            ;
        } else {
            $existingDevPlans = $performanceReview->developmentPlans;
            return view('PerformanceReview::KeyGoalsReview.create', [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
                'existingDevPlans' => $existingDevPlans,
            ]);
        }
    }

    public function submit($id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('submit', $performanceReview);

        $supervisor_id = $performanceReview->employee->latestTenure?->supervisor->user->id;
        if ($supervisor_id == null) {
            return redirect()->back()->withInput()->withWarningMessage('Supervisor not assigned to submit performance review.');
        }

        $inputs = array(
            'status_id' => config('constant.SUBMITTED_STATUS'),
            'reviewer_id' => $supervisor_id,
            'user_id' => auth()->user()->id,
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
            'log_remarks' => 'Performance review submitted.',
        );

        $performanceReview = $this->performanceReview->update($id, $inputs);
        $performanceReview->logs()->create($inputs);

        if ($performanceReview) {
            $performanceReview->reviewer->notify(new PerformanceReviewSubmitted($performanceReview));

            return redirect()->route('performance.employee.index')
                ->withSuccessMessage('Performance review successfully submitted.');
        } else {
            return redirect()->back()
                ->withInput()
                ->withWarningMessage('Performance review could not be submitted.');
        }
    }

    public function showPrevious($id)
    {
        $performanceReview = $this->performanceReview->find($id);
        $previousFY = $this->fiscalYear->where('title', '=', $performanceReview->fiscalYear->title - 1)->first();
        $annualPER = $this->performanceReview->select('id')
            ->where('employee_id', $performanceReview->employee_id)
            ->where('review_type_id', config('constant.ANNUAL_REVIEW'))
            ->where('fiscal_year_id', $previousFY->id)
            ->first();
        if ($annualPER) {
            return redirect(route('performance.employee.show', $annualPER->id) . '#identifyKeyGoals');
        }
        return redirect()->back()->withWarningMessage('Previous record could not be found');
    }
}
