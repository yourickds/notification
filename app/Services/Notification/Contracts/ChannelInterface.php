<?php

namespace App\Services\Notification\Contracts;

use App\Models\Notification;

interface ChannelInterface
{
    public function send(Notification $notification): bool;
}
