<?php

namespace Tests\Unit\OffDayWork;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\LieuLeave\Models\LieuLeaveBalance;
use Modules\Master\Models\FiscalYear;
use Modules\OffDayWork\Models\OffDayWork;
use Modules\OffDayWork\Notifications\OffDayWorkApproved;
use Modules\OffDayWork\Notifications\OffDayWorkRejected;
use Modules\Privilege\Models\User;
use Tests\TestCase;

/**
 * Regression coverage for ApproveController::update() calling
 * LieuLeaveBalanceRepository::addBalance() unconditionally instead of only
 * on approval. Before the fix, rejecting an off-day-work request (or
 * re-submitting the same approval) still minted a lieu leave balance row,
 * which is how the index page could show a balance count higher than the
 * number of actually-approved off-day-work requests.
 *
 * Uses the same lightweight SQLite fixture strategy as
 * tests/Unit/LieuLeave/RequestControllerIndexTest.php - see that file's
 * class docblock for why RefreshDatabase isn't used here.
 */
class ApproveControllerAddBalanceTest extends TestCase
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

    public function test_approving_grants_exactly_one_balance(): void
    {
        Notification::fake();

        [$requester, $approver, $offDayWork] = $this->makePendingOffDayWork();

        $response = $this->actingAs($approver)->post(
            route('approve.off.day.work.update', $offDayWork->id),
            ['status_id' => config('constant.APPROVED_STATUS'), 'approver_remarks' => 'ok']
        );

        $response->assertRedirect(route('approve.off.day.work.index'));

        $this->assertSame(
            1,
            LieuLeaveBalance::where('off_day_work_id', $offDayWork->id)->count()
        );

        Notification::assertSentTo($requester, OffDayWorkApproved::class);
    }

    public function test_rejecting_grants_no_balance(): void
    {
        Notification::fake();

        [$requester, $approver, $offDayWork] = $this->makePendingOffDayWork();

        $response = $this->actingAs($approver)->post(
            route('approve.off.day.work.update', $offDayWork->id),
            ['status_id' => config('constant.REJECTED_STATUS'), 'approver_remarks' => 'not eligible']
        );

        $response->assertRedirect(route('approve.off.day.work.index'));

        $this->assertSame(
            0,
            LieuLeaveBalance::where('off_day_work_id', $offDayWork->id)->count()
        );

        Notification::assertSentTo($requester, OffDayWorkRejected::class);
    }

    public function test_re_approving_the_same_request_does_not_duplicate_balance(): void
    {
        Notification::fake();

        [, $approver, $offDayWork] = $this->makePendingOffDayWork();

        $this->actingAs($approver)->post(
            route('approve.off.day.work.update', $offDayWork->id),
            ['status_id' => config('constant.APPROVED_STATUS'), 'approver_remarks' => 'ok']
        );

        // Simulate a duplicate submit / re-triggered approval on the same request.
        $this->actingAs($approver)->post(
            route('approve.off.day.work.update', $offDayWork->id),
            ['status_id' => config('constant.APPROVED_STATUS'), 'approver_remarks' => 'ok again']
        );

        $this->assertSame(
            1,
            LieuLeaveBalance::where('off_day_work_id', $offDayWork->id)->count()
        );
    }

    /**
     * @return array{0: User, 1: User, 2: OffDayWork}
     */
    private function makePendingOffDayWork(): array
    {
        $date = Carbon::create(2026, 6, 20);

        $requester = User::create([
            'full_name' => 'Requester',
            'email_address' => 'requester' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
        ]);

        $approver = User::create([
            'full_name' => 'Approver',
            'email_address' => 'approver' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
        ]);

        $fiscalYear = FiscalYear::create([
            'title' => 'FY ' . $date->year,
            'start_date' => $date->copy()->startOfYear(),
            'end_date' => $date->copy()->endOfYear(),
        ]);

        $offDayWork = OffDayWork::create([
            'date' => $date->toDateString(),
            'requester_id' => $requester->id,
            'approver_id' => $approver->id,
            'fiscal_year_id' => $fiscalYear->id,
            'reason' => 'Worked on off day',
            'deliverables' => [],
            'status_id' => config('constant.SUBMITTED_STATUS'),
            'request_date' => $date->toDateString(),
        ]);

        return [$requester, $approver, $offDayWork];
    }
}
