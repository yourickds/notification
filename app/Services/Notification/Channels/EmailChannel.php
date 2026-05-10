<?php

namespace App\Services\Notification\Channels;

use App\Models\Notification;
use App\Services\Notification\Contracts\ChannelInterface;
use App\Services\Notification\Exceptions\FatalChannelException;
use RuntimeException;

final class EmailChannel implements ChannelInterface
{
    public function send(Notification $notification): bool
    {
        // Симуляция задержки сети (опционально)
        usleep(rand(100_000, 500_000)); // 100-500ms

        // 30% шанс на транзитную ошибку (5xx) → триггерит retry
        if (rand(1, 100) <= 30) {
            throw new RuntimeException("Email 5xx: temporary failure for notification #{$notification->id}");
        }

        // 15% шанс на фатальную ошибку (4xx) → без retry, сразу ERROR
        if (rand(1, 100) <= 15) {
            throw new FatalChannelException(
                "Email 4xx: invalid email for user #{$notification->user_id} (notification #{$notification->id})"
            );
        }

        // 65% — успех
        return true;
    }
}
