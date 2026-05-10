<?php

namespace App\Jobs;

use App\Enums\NotificationStatus;
use App\Models\Notification;
use App\Services\Notification\Contracts\ChannelDispatcherInterface;
use App\Services\Notification\Exceptions\FatalChannelException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Notification $notification) {}

    /**
     * Execute the job.
     */
    public function handle(ChannelDispatcherInterface $dispatcher): void
    {
        try {
            $success = $dispatcher->send($this->notification);

            DB::table('notifications')
                ->where('id', $this->notification->id)
                ->update(['status' => $success ? NotificationStatus::SENT->value : NotificationStatus::ERROR->value]);

        } catch (FatalChannelException $e) {
            //            report($e);

            Log::error($e->getMessage(), [
                'notification_id' => $this->notification->id,
                'user_id' => $this->notification->user_id,
                'channel' => $this->notification->channel,
                'message_preview' => mb_strimwidth($this->notification->message, 0, 50, '...'),
                'exception' => get_class($e),
            ]);

            DB::table('notifications')
                ->where('id', $this->notification->id)
                ->update(['status' => NotificationStatus::ERROR->value]);

            return;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error("Notification #{$this->notification->id} failed after max attempts", [
            'notification_id' => $this->notification->id,
            'user_id' => $this->notification->user_id,
            'channel' => $this->notification->channel,
            'message_preview' => mb_strimwidth($this->notification->message, 0, 50, '...'),
            'error' => $e->getMessage(),
        ]);

        DB::table('notifications')
            ->where('id', $this->notification->id)
            ->update(['status' => NotificationStatus::ERROR->value]);
    }
}
