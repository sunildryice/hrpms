<?php

namespace Modules\Master\Repositories;

use App\Helper;
use App\Repositories\Repository;
use Modules\Master\Models\Office;

class OfficeRepository extends Repository
{
    public function __construct(Office $office)
    {
        $this->model = $office;
    }

    public function getOffices()
    {
        return $this->model->orderBy('office_name')->get();
    }

    public function getActiveOffices()
    {
        return $this->model->select(['*'])
            ->whereNotNull('activated_at')
            ->orderBy('office_name')->get();
    }

    public function getHolidays($officeId)
    {
        $office = $this->model->find($officeId);
        if($office){
            $officeHolidays = $office->holidays()->pluck('holiday_date')->toArray();
            $holidays = $sundays = [];
            foreach ($officeHolidays as $holiday) {
                $holidays[] = $holiday->format('Y-m-d');
            }

            $firstDayOfYear = date('Y-m-d', strtotime(date('Y-01-01')));
            $lastDayOfYear = date('Y-m-d', strtotime(date('Y')+1 . '-12-31'));
            if ($office->weekend_type == 2) {
                $sundays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 0);
            }

            $saturdays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 6);
            return array_merge($saturdays, $sundays, $holidays);
        }
        return [];
    }

    public function getHolidaysOneYear($officeId, $year=null)
    {
        $office = $this->model->find($officeId);
        $year = !$year ? date('Y') : $year;

        if($office){
            $officeHolidays = $office->holidays()->pluck('holiday_date')->toArray();
            $holidays = $sundays = [];
            foreach ($officeHolidays as $holiday) {
                $holidays[] = $holiday->format('Y-m-d');
            }

            $firstDayOfYear = date('Y-m-d', strtotime(date($year.'-01-01')));
            $lastDayOfYear = date('Y-m-d', strtotime($year . '-12-31'));

            if ($office->weekend_type == 2) {
                $sundays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 0);
            }

            $saturdays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 6);

            return array_merge($saturdays, $sundays, $holidays);
        }
        return [];
    }

    public function getOfficeHolidays($officeId)
    {
        $office = $this->model->find($officeId);

        if($office){
            $officeHolidays = $office->holidays()->pluck('holiday_date')->toArray();
            $holidays = [];
            foreach ($officeHolidays as $holiday) {
                $holidays[] = $holiday->format('Y-m-d');
            }
            return $holidays;
        }
        return [];
    }

    public function getOfficeWeekends($officeId, $year=null)
    {
        $office = $this->model->find($officeId);
        $year = !$year ? date('Y') : $year;

        if($office){
            $sundays = [];

            $firstDayOfYear = date('Y-m-d', strtotime($year .'01-01'));
            $lastDayOfYear = date('Y-m-d', strtotime($year . '-12-31'));

            if ($office->weekend_type == 2) {
                $sundays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 0);
            }

            $saturdays = Helper::getSpecificDays($firstDayOfYear, $lastDayOfYear, 6);

            return array_merge($saturdays, $sundays);
        }
        return [];
    }

    public function getParentOffices($officeType = null)
    {
        $officeTypes = [
            'head_office' => 1,
            'cluster' => 2,
            'district' => 3
        ];

        if ($officeType == $officeTypes['cluster']) {
            return $this->model->whereIn('office_type_id', [$officeTypes['head_office']])->get();
        } elseif ($officeType == $officeTypes['district']) {
            return $this->model->whereIn('office_type_id', [$officeTypes['head_office'], $officeTypes['cluster']])->get();
        }

        return [];
    }
}
