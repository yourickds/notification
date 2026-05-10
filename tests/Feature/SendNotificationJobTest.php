<?php

namespace Tests\Feature;

use App\Enums\NotificationStatus;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notification\Contracts\ChannelDispatcherInterface;
use App\Services\Notification\Exceptions\FatalChannelException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_updates_status_to_sent_on_success(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'channel' => 'telegram',
            'status' => NotificationStatus::PENDING,
        ]);

        $dispatcher = $this->mock(ChannelDispatcherInterface::class);

        $dispatcher->shouldReceive('send')
            ->with(\Mockery::type(Notification::class))
            ->once()
            ->andReturn(true);

        $job = new SendNotificationJob($notification);
        $job->handle($dispatcher);

        $this->assertEquals(NotificationStatus::SENT, $notification->fresh()->status);
    }

    public function test_job_sets_error_on_fatal_exception_and_does_not_retry(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'channel' => 'email',
            'status' => NotificationStatus::PENDING,
        ]);

        $dispatcher = $this->mock(ChannelDispatcherInterface::class);
        $dispatcher->shouldReceive('send')
            ->with(\Mockery::type(Notification::class))
            ->andThrow(new FatalChannelException('4xx'));

        $job = new SendNotificationJob($notification);
        $job->handle($dispatcher);

        $this->assertEquals(NotificationStatus::ERROR, $notification->fresh()->status);
    }

    public function test_job_throws_transient_error_for_retry(): void
    {
        $notification = Notification::factory()->create(['channel' => 'telegram']);

        $dispatcher = $this->mock(ChannelDispatcherInterface::class);
        $dispatcher->shouldReceive('send')
            ->with(\Mockery::type(Notification::class))
            ->andThrow(new \RuntimeException('5xx'));

        $job = new SendNotificationJob($notification);

        $this->expectException(\RuntimeException::class);
        $job->handle($dispatcher);
    }
}
