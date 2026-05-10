<?php

namespace App\Services\Notification\Contracts;

use App\Models\Notification;

interface ChannelDispatcherInterface
{
    public function send(Notification $notification): bool;
}
