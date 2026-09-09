<?php

namespace App\Models;

use App\Enums\NotificationActionType;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    protected $fillable = ['identifier', 'user_type', 'action_type', 'name', 'icon', 'info', 'variables'];

    protected $casts = [
        'user_type'   => UserType::class,
        'action_type' => NotificationActionType::class,
        'variables'   => 'array',
    ];

    public function channels(): HasMany
    {
        return $this->hasMany(NotificationTemplateChannel::class, 'template_id');
    }
}
