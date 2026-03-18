<?php

namespace Tests\Unit\TravelRequest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Modules\TravelAuthorization\Models\TravelAuthorization;
use Modules\Privilege\Models\User;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationApproved;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationRejected;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationReturned;
use Modules\TravelAuthorization\Notifications\TravelAuthorizationSubmitted;
use Illuminate\Support\Facades\Notification;

class TravelRequestApproveTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_approves_travel_authorization()
    {
        Notification::fake();

        $user = User::factory()->create();
        $travel = TravelAuthorization::factory()->create([
            'approver_id' => $user->id,
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('approve.ta.requests.store', $travel->id), [
                'status_id' => config('constant.APPROVED_STATUS'),
                'log_remarks' => 'Approved for travel.',
            ]);

        $response->assertRedirect(route('approve.ta.requests.index'));
        $response->assertSessionHas('successMessage', 'Travel Authorization request is successfully approved.');

        $this->assertDatabaseHas('travel_authorizations', [
            'id' => $travel->id,
            'status_id' => config('constant.APPROVED_STATUS'),
        ]);

        Notification::assertSentTo(
            $travel->requester,
            TravelAuthorizationApproved::class,
            function ($notification, $channels) use ($travel) {
                return $notification->travel->id === $travel->id;
            }
        );
    }

    public function test_store_rejects_travel_authorization()
    {
        Notification::fake();

        $user = User::factory()->create();
        $travel = TravelAuthorization::factory()->create([
            'approver_id' => $user->id,
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('approve.ta.requests.store', $travel->id), [
                'status_id' => config('constant.REJECTED_STATUS'),
                'log_remarks' => 'Rejected due to budget.',
            ]);

        $response->assertRedirect(route('approve.ta.requests.index'));
        $response->assertSessionHas('successMessage', 'Travel Authorization request is rejected.');

        $this->assertDatabaseHas('travel_authorizations', [
            'id' => $travel->id,
            'status_id' => config('constant.REJECTED_STATUS'),
        ]);

        Notification::assertSentTo(
            $travel->requester,
            TravelAuthorizationRejected::class,
            function ($notification, $channels) use ($travel) {
                return $notification->travel->id === $travel->id;
            }
        );
    }

    public function test_store_returns_travel_authorization()
    {
        Notification::fake();

        $user = User::factory()->create();
        $travel = TravelAuthorization::factory()->create([
            'approver_id' => $user->id,
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('approve.ta.requests.store', $travel->id), [
                'status_id' => config('constant.RETURNED_STATUS'),
                'log_remarks' => 'Please provide more details.',
            ]);

        $response->assertRedirect(route('approve.ta.requests.index'));
        $response->assertSessionHas('successMessage', 'Travel Authorization request is successfully returned.');

        $this->assertDatabaseHas('travel_authorizations', [
            'id' => $travel->id,
            'status_id' => config('constant.RETURNED_STATUS'),
        ]);

        Notification::assertSentTo(
            $travel->requester,
            TravelAuthorizationReturned::class,
            function ($notification, $channels) use ($travel) {
                return $notification->travel->id === $travel->id;
            }
        );
    }

    public function test_store_recommends_travel_authorization()
    {
        Notification::fake();

        $user = User::factory()->create();
        $recommendedTo = User::factory()->create();
        $travel = TravelAuthorization::factory()->create([
            'approver_id' => $user->id,
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('approve.ta.requests.store', $travel->id), [
                'status_id' => config('constant.RECOMMENDED_STATUS'),
                'recommended_to' => $recommendedTo->id,
                'log_remarks' => 'Recommended for approval.',
            ]);

        $response->assertRedirect(route('approve.ta.requests.index'));
        $response->assertSessionHas('successMessage', 'Travel Authorization request is successfully recommended.');

        $this->assertDatabaseHas('travel_authorizations', [
            'id' => $travel->id,
            'status_id' => config('constant.RECOMMENDED_STATUS'),
            'recommender_id' => $user->id,
            'approver_id' => $recommendedTo->id,
        ]);

        Notification::assertSentTo(
            $recommendedTo,
            TravelAuthorizationSubmitted::class,
            function ($notification, $channels) use ($travel) {
                return $notification->travel->id === $travel->id;
            }
        );
    }

    public function test_store_fails_without_authorization()
    {
        $user = User::factory()->create();
        $travel = TravelAuthorization::factory()->create([
            'approver_id' => User::factory()->create()->id, // Different user
            'status_id' => config('constant.SUBMITTED_STATUS'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('approve.ta.requests.store', $travel->id), [
                'status_id' => config('constant.APPROVED_STATUS'),
                'log_remarks' => 'Approved.',
            ]);

        $response->assertForbidden();
    }
}
