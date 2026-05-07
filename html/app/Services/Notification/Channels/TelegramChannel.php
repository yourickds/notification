<?php

namespace App\Services\Notification\Channels;

use App\Services\Notification\Attributes\Channel;
use App\Services\Notification\Contracts\ChannelInterface;

#[Channel(code: 'telegram', name: 'Telegram')]
final class TelegramChannel implements ChannelInterface
{
    public function send(): bool
    {
        // Делаем отправку сообщения, без реальной обработки
        // Пока без очередей и интерфейса чистая сухая база

        return true;
    }
}
