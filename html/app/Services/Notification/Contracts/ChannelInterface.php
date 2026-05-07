<?php

namespace App\Services\Notification\Contracts;

interface ChannelInterface
{
    public function send(): bool;
}
