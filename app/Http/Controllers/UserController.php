<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationsUserRequest;
use App\Http\Resources\NotificationResource;
use App\Models\User;

class UserController extends Controller
{
    public function notifications(User $user, NotificationsUserRequest $request)
    {
        $query = $user->notifications()
            ->when(
                $request->status, fn ($q, $status) => $q->where('status', $status)
            )
            ->when(
                $request->channel, fn ($q, $channel) => $q->where('channel', $channel)
            )
            ->latest();

        return NotificationResource::collection($query->get());
    }
}
