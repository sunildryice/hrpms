<?php

namespace Modules\EmployeeAttendance\Imports;

use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SheetImport implements ToCollection, WithEvents, WithHeadingRow
{
    private $attendance;

    private $donors;

    private $employee;

    private $attendanceDetail;

    public $year;

    public $month;

    public $employee_name;

    public $employee_id;

    public function __construct()
    {
        $this->donors = app(DonorCodeRepository::class);
        $this->employee = app(EmployeeRepository::class);
        $this->attendanceDetail = app(AttendanceDetailRepository::class);
        $this->attendance = app(AttendanceRepository::class);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->year = explode('/', $event->getSheet()->getCell('B20')->getValue())[2];
                $this->month = $event->getSheet()->getCell('E9')->getValue();
                $this->employee_name = explode(' [', $event->getSheet()->getCell('F13')->getValue())[0];
                $employee_id = explode(']', explode(' [', $event->getSheet()->getCell('F13')->getValue())[1])[0];
                $this->employee_id = $employee_id;
            },
        ];
    }

    public function headingRow(): int
    {
        return 19;
    }

    public function collection(Collection $rows)
    {
        $employee = $this->employee->where('employee_code', '=', $this->employee_id)->first();

        if ($employee == null) {
            return;
        }

        $donorCodes = implode(',', $this->donors->getArrayOfEnabledDonorCodes());

        $attendance_masters = [
            'employee_id' => $employee->id,
            'department_id' => $employee->latestTenure->department_id,
            'designation_id' => $employee->latestTenure->designation_id,
            'office_id' => $employee->latestTenure->office_id,
            'duty_station_id' => $employee->latestTenure->duty_station_id,
            'year' => $this->year,
            'donor_codes' => $donorCodes,
            'month' => Helper::getMonthNumber($this->month),
            'status_id' => config('constant.CREATED_STATUS'),
            'requester_id' => $employee->user?->id ?? null,
            'updated_by' => auth()->user()->id,
        ];

        $attendance = $this->attendance->select('*')->where('employee_id', $employee->id)
            ->where('year', $this->year)
            ->where('month', Helper::getMonthNumber($this->month))
            ->first();

        if ($attendance) {
            if ($attendance->status_id != config('constant.CREATED_STATUS')) {
                return;
            }
            $attendance->update($attendance_masters);
        } else {
            $attendance = $this->attendance->create($attendance_masters);
        }

        // $attendance = $this->attendance->updateOrCreate([
        //     'employee_id' => $employee->id,
        //     'year' => $this->year,
        //     'month' => Helper::getMonthNumber($this->month),
        // ], $attendance_masters);

        $count = cal_days_in_month(CAL_GREGORIAN, Helper::getMonthNumber($this->month), $this->year);

        foreach ($rows as $key => $row) {
            if ($key == $count) {
                break;
            }
            if (empty($row['date'])) {
                break;
            }

            [$day, $month, $year] = explode('/', $row['date']);

            $date = $year.'-'.$month.'-'.$day;

            $checkinTime = $row['time_in'] != '' ? Date::excelToDateTimeObject($row['time_in'], 'Asia/Kathmandu') : null;
            $checkoutTime = $row['time_out'] != '' ? Date::excelToDateTimeObject($row['time_out'], 'Asia/Kathmandu') : null;

            $interval = 0;
            if (! is_null($checkinTime) && ! is_null($checkoutTime)) {
                $start = new Carbon($checkinTime);
                $start->startOfMinute();
                $end = new Carbon($checkoutTime);
                $end->startOfMinute();
                $interval = $start->diff($end)->format('%H.%I');
            }

            $attendance_details = [
                'attendance_master_id' => $attendance->id,
                'attendance_date' => $date,
                'checkin' => $row['time_in'] ? Helper::convertTime($row['time_in']) : null,
                'checkout' => $row['time_out'] ? Helper::convertTime($row['time_out']) : null,
                'created_by' => $employee->user?->id ?? auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'worked_hours' => $interval,
                'unrestricted_hours' => $interval,
            ];

            $attendanceDetail = $this->attendanceDetail->updateOrCreate([
                'attendance_master_id' => $attendance->id,
                'attendance_date' => $date,
            ], $attendance_details);

            $attendanceDetail->update([
                'unrestricted_hours' => $attendanceDetail->worked_hours - $attendanceDetail->charged_hours,
            ]);
        }
    }
}
