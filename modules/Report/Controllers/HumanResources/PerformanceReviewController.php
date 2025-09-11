<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\Report\Exports\HumanResources\PerformanceReviewExport;
use Yajra\DataTables\DataTables;

class PerformanceReviewController extends Controller
{
    public function __construct(
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected PerformanceReviewQuestion $performanceReviewQuestion
    ) {}

    public function index(Request $request)
    {
        $data = PerformanceReview::query();
        // $data = $data->where('review_type_id', config('constant.ANNUAL_REVIEW'));

        if ($request->ajax()) {
            if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000);
                $end_date = date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000);
                if ($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                }
            }

            if ($request->has('employee') && $request->employee) {
                $employee_user_id = $request->employee;
                $data->where('requester_id', $employee_user_id);
            }

            if ($request->has('duty_station') && $request->duty_station) {
                $dutyStationId = $request->duty_station;
                $data->whereHas('requester', function ($q) use ($dutyStationId) {
                    $q->whereHas('employee', function ($q) use ($dutyStationId) {
                        $q->whereHas('latestTenure', function ($q) use ($dutyStationId) {
                            $q->where('duty_station_id', $dutyStationId);
                        });
                    });
                });
            }

            if ($request->has('goal_setting_date') && $request->goal_setting_date) {
                $goal_setting_date = date('Y-m-d 00:00:00', ((int) $request->goal_setting_date) / 1000);
                $data->whereDate('goal_setting_date', '=', $goal_setting_date);
            }

            if ($request->has('mid_term_per_date') && $request->mid_term_per_date) {
                $mid_term_per_date = date('Y-m-d 00:00:00', ((int) $request->mid_term_per_date) / 1000);
                $data->orWhereDate('mid_term_per_date', $mid_term_per_date);
            }

            if ($request->has('final_per_date') && $request->final_per_date) {
                $final_per_date = date('Y-m-d 00:00:00', ((int) $request->final_per_date) / 1000);
                $data->orWhereDate('final_per_date', $final_per_date);
            }

            $data->get();

            return DataTables::of($data->orderBy('fiscal_year_id', 'desc'))
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->requester->employee->getFullName();
                })
                ->addColumn('designation', function ($row) {
                    return $row->requester->employee->latestTenure->getDesignationName();
                })
                ->addColumn('duty_station', function ($row) {
                    return $row->requester->employee->latestTenure->getDutyStation();
                })
                ->addColumn('supervisor', function ($row) {
                    return $row->requester->employee->latestTenure->getSupervisorName();
                })
                ->addColumn('fiscal_year', function ($row) {
                    return $row->getFiscalYear();
                })
                ->addColumn('status', function ($row) {
                    return $row->getStatus();
                })
                ->addColumn('goal_setting_date', function ($row) {
                    return $row->goal_setting_date?->toFormattedDateString();
                    // return $row->getGoalSettingDate();
                })
                ->addColumn('mid_term_per_date', function ($row) {
                    return $row->mid_term_per_date?->toFormattedDateString();
                    // return $row->getMidTermPerDate();
                })
                ->addColumn('final_per_date', function ($row) {
                    return $row->final_per_date?->toFormattedDateString();
                    // return $row->getFinalPerDate();
                })
                ->addColumn('major_achievements', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(1);
                    }

                    return null;
                })
                ->addColumn('major_challenges', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(2);
                    }

                    return null;
                })
                ->addColumn('working_relationship', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(3);
                    }

                    return null;
                })
                ->addColumn('productivity', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(4);
                    }

                    return null;
                })
                ->addColumn('leadership', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(5);
                    }

                    return null;
                })
                ->addColumn('problem_solving', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(6);
                    }

                    return null;
                })
                ->addColumn('accountability', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(7);
                    }

                    return null;
                })
                ->addColumn('identified_strengths', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(8);
                    }

                    return null;
                })
                ->addColumn('identified_growth_areas', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(9);
                    }

                    return null;
                })
                ->addColumn('performance_evaluation', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getPerformanceEvalAnswer();
                    }

                    return null;
                })
                ->addColumn('employee_comment', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(17);
                    }

                    return null;
                })
                ->addColumn('supervisor_comment', function ($row) {
                    if ($row->review_type_id == config('constant.ANNUAL_REVIEW')) {
                        return $row->getAnswerShort(18);
                    }

                    return null;
                })
                ->make(true);
        }

        $array = [
            'dutyStations' => $this->districts->getDistricts(),
            'employees' => $this->employees->getActiveEmployees(),
        ];

        return view('Report::HumanResources.PerformanceReview.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000) : null;
        $employee = $request->employee ? $request->employee : null;
        $duty_station = $request->duty_station ? $request->duty_station : null;
        $goal_setting_date = $request->goal_setting_date ? date('Y-m-d 00:00:00', ((int) $request->goal_setting_date) / 1000) : null;
        $mid_term_per_date = $request->mid_term_per_date ? date('Y-m-d 00:00:00', ((int) $request->mid_term_per_date) / 1000) : null;
        $final_per_date = $request->final_per_date ? date('Y-m-d 00:00:00', ((int) $request->final_per_date) / 1000) : null;

        return new PerformanceReviewExport($start_date, $end_date, $employee, $duty_station, $goal_setting_date, $mid_term_per_date, $final_per_date);
    }
}
