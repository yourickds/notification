<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Services\Notification\ChannelDispatcher;
use App\Services\Notification\Contracts\ChannelInterface;
use InvalidArgumentException;
use Tests\TestCase;

class ChannelDispatcherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Теперь config() работает, потому что приложение загружено
        config(['notifications.channels' => [
            'valid' => DummyChannel::class,
            'invalid' => \stdClass::class,
        ]]);
    }

    public function test_resolves_and_calls_valid_channel(): void
    {
        $dispatcher = new ChannelDispatcher;

        $dummyNotification = new Notification([
            'user_id' => 1,
            'channel' => 'valid',
            'message' => 'Test',
            'status' => 'pending',
        ]);

        $result = $dispatcher->send($dummyNotification);

        $this->assertTrue($result);
    }

    public function test_throws_for_unregistered_channel(): void
    {
        $dispatcher = new ChannelDispatcher;

        $dummyNotification = new Notification([
            'channel' => 'unknown',
            'message' => 'Test',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Channel 'unknown' is not registered.");

        $dispatcher->send($dummyNotification);
    }

    public function test_throws_for_non_implementing_class(): void
    {
        $dispatcher = new ChannelDispatcher;

        $dummyNotification = new Notification([
            'channel' => 'invalid',
            'message' => 'Test',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Channel class 'stdClass' must implement ChannelInterface.");

        $dispatcher->send($dummyNotification);
    }
}

// Временная заглушка для теста
class DummyChannel implements ChannelInterface
{
    public function send(Notification $notification): bool
    {
        return true;
    }
}
