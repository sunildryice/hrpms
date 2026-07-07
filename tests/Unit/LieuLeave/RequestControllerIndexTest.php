<?php

namespace Tests\Unit\LieuLeave;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Models\AttendanceDetail;
use Modules\LieuLeave\Controllers\RequestController;
use Modules\LieuLeave\Models\LieuLeaveBalance;
use Modules\LieuLeave\Models\LieuLeaveRequest;
use Modules\Master\Models\FiscalYear;
use Modules\OffDayWork\Models\OffDayWork;
use Modules\Privilege\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Covers the "Lieu Leave Balances" summary cards on LieuLeave::index
 * (Applied Leave / Lieu Balance / Available Status).
 *
 * Regression coverage for the bug where balances earned in the previous
 * month (but not yet expired, per the 30-day rollover) were silently
 * dropped from the index page's eligibility count because
 * RequestController::index() queried LieuLeaveBalanceRepository::getPresentOffDayWorkDates()
 * with the current month instead of a month-back cursor.
 *
 * Deliberately does not use RefreshDatabase: this app's full migration set
 * currently can't run from scratch on the installed Laravel version
 * (database/migrations/2022_06_01_131056_create_trainings_table.php calls
 * Blueprint::unsignedDecimal(), which no longer exists), and
 * AuthServiceProvider::boot() unconditionally queries the `permissions`
 * table, so the app can't even boot against an unmigrated database. Point
 * DB_DATABASE at a SQLite file pre-built with just the tables this test
 * needs (see scratchpad/build_test_schema.php) and this test wraps each
 * case in its own transaction for isolation instead.
 */
class RequestControllerIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_available_when_off_day_work_earned_this_month_and_attended(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user] = $this->makeUserWithAttendance('2026-07-05');
        $this->makeApprovedBalance($user, '2026-07-05');

        $data = $this->getIndexData($user);

        $this->assertSame(0, $data['appliedLeaveofMonth']);
        $this->assertSame(1, $data['lieuLeaveBalance']);
        $this->assertSame('Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_available_when_balance_earned_previous_month_is_still_valid(): void
    {
        // "Now" is mid-July; the off-day-work happened on Jun 28 and the
        // 30-day expiry (Jul 28) hasn't passed yet. Before the fix, the
        // index page queried only the current month and reported this as
        // unavailable even though the Create page's balance check (which
        // looks at expires_at, not earned_date) said it was available.
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user] = $this->makeUserWithAttendance('2026-06-28');
        $this->makeApprovedBalance($user, '2026-06-28');

        $data = $this->getIndexData($user);

        $this->assertSame(1, $data['lieuLeaveBalance']);
        $this->assertSame('Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_not_available_when_balance_predates_the_lookback_window(): void
    {
        // Earned two months before "now" - outside even the one-month
        // lookback, so it should not be counted.
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user] = $this->makeUserWithAttendance('2026-05-10');
        $this->makeApprovedBalance($user, '2026-05-10', expiresAt: '2026-06-09');

        $data = $this->getIndexData($user);

        $this->assertSame(0, $data['lieuLeaveBalance']);
        $this->assertSame('Not Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_not_available_when_balance_has_already_expired(): void
    {
        // Earned Jun 2 (inside the one-month lookback from Jul 1), but its
        // 30-day expiry (Jul 2) has already passed by "now" (Jul 15).
        // Regression coverage for a second gap in getPresentOffDayWorkDates():
        // it filtered only by earned_date and never checked expires_at, so an
        // expired balance still inside the lookback window was counted as available.
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user] = $this->makeUserWithAttendance('2026-06-02');
        $this->makeApprovedBalance($user, '2026-06-02', expiresAt: '2026-07-02');

        $data = $this->getIndexData($user);

        $this->assertSame(0, $data['lieuLeaveBalance']);
        $this->assertSame('Not Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_not_available_when_employee_was_absent_on_off_day_work_date(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        // Attendance record exists for the date, but without checkin/checkout
        // (i.e. the employee did not actually attend on their off day).
        [$user] = $this->makeUserWithAttendance('2026-07-05', present: false);
        $this->makeApprovedBalance($user, '2026-07-05');

        $data = $this->getIndexData($user);

        $this->assertSame(0, $data['lieuLeaveBalance']);
        $this->assertSame('Not Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_not_available_when_lieu_leave_already_applied_for_the_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user, $employee] = $this->makeUserWithAttendance('2026-07-05');
        $this->makeApprovedBalance($user, '2026-07-05');

        LieuLeaveRequest::create([
            'requester_id' => $user->id,
            'approver_id' => $user->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-10',
            'request_date' => '2026-07-08',
            'reason' => 'Personal',
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $data = $this->getIndexData($user);

        $this->assertSame(1, $data['appliedLeaveofMonth']);
        $this->assertSame('Not Available', $data['availableBalanceofMonthStatus']);
    }

    public function test_balance_already_linked_to_a_request_is_excluded(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15, 10));

        [$user] = $this->makeUserWithAttendance('2026-07-05');

        $lieuLeaveRequest = LieuLeaveRequest::create([
            'requester_id' => $user->id,
            'approver_id' => $user->id,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-01',
            'request_date' => '2026-05-28',
            'reason' => 'Already used',
            'status_id' => config('constant.APPROVED_STATUS'),
        ]);

        $this->makeApprovedBalance($user, '2026-07-05', linkedRequestId: $lieuLeaveRequest->id);

        $data = $this->getIndexData($user);

        $this->assertSame(0, $data['lieuLeaveBalance']);
        $this->assertSame('Not Available', $data['availableBalanceofMonthStatus']);
    }

    /**
     * @return array{0: User, 1: Employee}
     */
    private function makeUserWithAttendance(string $date, bool $present = true): array
    {
        $carbonDate = Carbon::parse($date);

        $employee = Employee::create([
            'employee_code' => random_int(100000, 999999),
            'full_name' => 'Test Employee',
        ]);

        $user = User::create([
            'employee_id' => $employee->id,
            'full_name' => 'Test Employee',
            'email_address' => 'employee' . $employee->id . '@example.test',
            'password' => bcrypt('secret'),
        ]);

        $attendanceMaster = Attendance::create([
            'employee_id' => $employee->id,
            'year' => $carbonDate->year,
            'month' => $carbonDate->month,
        ]);

        AttendanceDetail::create([
            'attendance_master_id' => $attendanceMaster->id,
            'attendance_date' => $carbonDate->toDateString(),
            'checkin' => $present ? '09:00:00' : null,
            'checkout' => $present ? '17:00:00' : null,
        ]);

        return [$user, $employee];
    }

    private function makeApprovedBalance(
        User $user,
        string $offDayWorkDate,
        ?string $expiresAt = null,
        ?int $linkedRequestId = null
    ): LieuLeaveBalance {
        $earnedDate = Carbon::parse($offDayWorkDate);
        $expiresAt = $expiresAt ? Carbon::parse($expiresAt) : $earnedDate->copy()->addDays(30);

        $fiscalYear = FiscalYear::create([
            'title' => 'FY ' . $earnedDate->year,
            'start_date' => $earnedDate->copy()->startOfYear(),
            'end_date' => $earnedDate->copy()->endOfYear(),
        ]);

        $offDayWork = OffDayWork::create([
            'date' => $earnedDate->toDateString(),
            'requester_id' => $user->id,
            'approver_id' => $user->id,
            'fiscal_year_id' => $fiscalYear->id,
            'reason' => 'Worked on off day',
            'deliverables' => [],
            'status_id' => config('constant.APPROVED_STATUS'),
            'request_date' => $earnedDate->toDateString(),
        ]);

        return LieuLeaveBalance::create([
            'user_id' => $user->id,
            'earned_date' => $earnedDate->toDateString(),
            'earned_month' => $earnedDate->copy()->startOfMonth()->toDateString(),
            'off_day_work_id' => $offDayWork->id,
            'expires_at' => $expiresAt->toDateString(),
            'lieu_leave_request_id' => $linkedRequestId,
        ]);
    }

    private function getIndexData(User $user): array
    {
        $this->actingAs($user);

        $response = app(RequestController::class)->index(Request::create('/lieu-leave/requests', 'GET'));

        return $response->getData();
    }
}
