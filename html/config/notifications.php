<?php

use App\Services\Notification\Channels\TelegramChannel;

return [
    'channels' => [
        'telegram' => TelegramChannel::class,
        // 'email' => EmailChannel::class,
    ],
];
