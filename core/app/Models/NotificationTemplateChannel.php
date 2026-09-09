<?php

namespace App\Models;

use App\Enums\NotificationChannelType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplateChannel extends Model
{
    protected $fillable = ['template_id', 'channel', 'title', 'message', 'is_active'];

    protected $casts = [
        'channel'   => NotificationChannelType::class,
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }
}
