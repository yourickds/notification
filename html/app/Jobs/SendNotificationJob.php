<?php

namespace App\Jobs;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Notification $notification) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->notification->update(['status' => NotificationStatus::SENT->value]);
    }
}
