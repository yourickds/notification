<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'message',
        'channel',
        'status',
    ];

    protected $casts = [
        'status' => NotificationStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
