<?php

namespace Tests\Unit\Policies;

use Carbon\Carbon;
use Modules\Privilege\Models\User;
use Modules\Project\Models\WorkPlan;
use Modules\Project\Policies\WorkPlanPolicy;
use Tests\TestCase;

class WorkPlanPolicyTest extends TestCase
{
    private WorkPlanPolicy $policy;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new WorkPlanPolicy();
        $this->user = new User();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_update_disallows_past_week(): void
    {
        // Simulate "today" as Feb 15, 2026 (Sunday of the following week)
        Carbon::setTestNow(Carbon::create(2026, 2, 15, 9));
        // Build a work plan whose week ended the previous Saturday
        $pastWeekStart = Carbon::now()->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($pastWeekStart);

        $this->assertFalse($this->policy->update($this->user, $workPlan));
    }

    public function test_update_allows_future_week(): void
    {
        // Current time is Monday, Feb 9
        Carbon::setTestNow(Carbon::create(2026, 2, 9, 9));
        // Work plan week lives completely in the future
        $futureWeekStart = Carbon::now()->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($futureWeekStart);

        $this->assertTrue($this->policy->update($this->user, $workPlan));
    }

    public function test_update_allows_current_week_until_monday(): void
    {
        $mondayMorning = Carbon::create(2026, 2, 9, 9);
        Carbon::setTestNow($mondayMorning);
        // Policy should permit editing up to (and including) Monday
        $currentWeekStart = $mondayMorning->copy()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($currentWeekStart);

        $this->assertTrue($this->policy->update($this->user, $workPlan));
    }

    public function test_update_disallows_current_week_after_monday(): void
    {
        $tuesday = Carbon::create(2026, 2, 10, 9);
        Carbon::setTestNow($tuesday);
        // Same week as above but now it's Tuesday — should be locked
        $currentWeekStart = $tuesday->copy()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($currentWeekStart);

        $this->assertFalse($this->policy->update($this->user, $workPlan));
    }

    public function test_update_status_allows_current_week_on_friday(): void
    {
        $friday = Carbon::create(2026, 2, 13, 9);
        Carbon::setTestNow($friday);
        // Friday of current week: only day status changes are allowed
        $currentWeekStart = $friday->copy()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($currentWeekStart);

        $this->assertTrue($this->policy->updateStatus($this->user, $workPlan));
    }

    public function test_update_status_blocks_current_week_before_friday(): void
    {
        $wednesday = Carbon::create(2026, 2, 11, 9);
        Carbon::setTestNow($wednesday);
        // Wednesday should still be blocked for status updates
        $currentWeekStart = $wednesday->copy()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($currentWeekStart);

        $this->assertFalse($this->policy->updateStatus($this->user, $workPlan));
    }

    public function test_update_status_allows_previous_week_within_grace_period(): void
    {
        $today = Carbon::create(2026, 2, 15, 9);
        Carbon::setTestNow($today);
        // Previous week fell within the 8-day post-week grace window
        $previousWeekStart = $today->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($previousWeekStart);

        $this->assertTrue($this->policy->updateStatus($this->user, $workPlan));
    }

    public function test_update_status_disallows_previous_week_after_grace_period(): void
    {
        $today = Carbon::create(2026, 2, 20, 9);
        Carbon::setTestNow($today);
        // Two weeks old – the grace period has expired
        $oldWeekStart = $today->copy()->subWeeks(2)->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($oldWeekStart);

        $this->assertFalse($this->policy->updateStatus($this->user, $workPlan));
    }

    public function test_update_status_disallows_future_week(): void
    {
        $today = Carbon::create(2026, 2, 9, 9);
        Carbon::setTestNow($today);
        // Future weeks never allow status updates
        $futureWeekStart = $today->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
        $workPlan = $this->makeWorkPlan($futureWeekStart);

        $this->assertFalse($this->policy->updateStatus($this->user, $workPlan));
    }

    private function makeWorkPlan(Carbon $weekStart): WorkPlan
    {
        $weekEnd = $weekStart->copy()->addDays(6);

        return new WorkPlan([
            'from_date' => $weekStart->toDateString(),
            'to_date' => $weekEnd->toDateString(),
        ]);
    }
}
