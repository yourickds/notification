<?php

namespace Tests\Feature;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_notification_dispatches_job_and_sets_pending_status(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/notification', [
            'user_id' => $user->id,
            'message' => 'Test message',
            'channel' => 'telegram',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendNotificationJob::class);
    }

    public function test_validation_rejects_unknown_channel_and_long_message(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/notification', [
            'user_id' => $user->id,
            'message' => str_repeat('a', 501),
            'channel' => 'whatsapp',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['message', 'channel']);
    }

    public function test_can_filter_user_notifications_by_status_and_channel(): void
    {
        $user = User::factory()->create();

        // ✅ Теперь factory() работает
        Notification::factory()->create(['user_id' => $user->id, 'status' => 'sent', 'channel' => 'telegram']);
        Notification::factory()->create(['user_id' => $user->id, 'status' => 'error', 'channel' => 'email']);
        Notification::factory()->create(['user_id' => $user->id, 'status' => 'pending', 'channel' => 'telegram']);

        $response = $this->getJson("/api/v1/users/{$user->id}/notifications?status=sent");
        $response->assertSuccessful()->assertJsonCount(1, 'data');

        $response = $this->getJson("/api/v1/users/{$user->id}/notifications?channel=email");
        $response->assertSuccessful()->assertJsonCount(1, 'data');
    }
}
