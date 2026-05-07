<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Http\Requests\NotificationStore;
use App\Http\Resources\NotificationResource;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function store(NotificationStore $request): NotificationResource
    {
        $validated = $request->validated();
        $notification = Notification::query()->create([
            ...$validated,
            'status' => NotificationStatus::PENDING->value,
        ]);

        SendNotificationJob::dispatch($notification);

        return NotificationResource::make($notification);
    }
}
