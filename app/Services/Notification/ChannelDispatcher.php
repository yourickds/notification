<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Services\Notification\Contracts\ChannelDispatcherInterface;
use App\Services\Notification\Contracts\ChannelInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

final class ChannelDispatcher implements ChannelDispatcherInterface
{
    /**
     * @throws BindingResolutionException
     */
    public function send(Notification $notification): bool
    {
        $channelCode = $notification->channel;
        $class = config("notifications.channels.{$channelCode}");

        if (! is_string($class) || ! class_exists($class)) {
            throw new InvalidArgumentException("Channel '{$channelCode}' is not registered.");
        }

        if (! is_subclass_of($class, ChannelInterface::class)) {
            throw new InvalidArgumentException("Channel class '{$class}' must implement ChannelInterface.");
        }

        return app()->make($class)->send($notification);
    }
}
