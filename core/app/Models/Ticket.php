<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'title',
        'message',
        'attachment',
        'priority',
        'status',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'priority'    => TicketPriority::class,
        'status'      => TicketStatus::class,
    ];

    public function scopePending($query)
    {
        return $query->where('status', TicketStatus::PENDING);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'ticket_id', 'id');
    }

    /**
     * Checks if the ticket has been replied to by an admin.
     *
     * A ticket is considered replied if the last message from the admin is newer than the last message from the client.
     */
    public function isReplied(): bool
    {
        // Get the last message from the client
        $lastClientMessage = $this->messages()->where('admin_id', '=', null)->latest()->first()->created_at ?? $this->created_at;

        // Get the last message from an admin
        $lastAdminMessage = $this->messages()->where('admin_id', '!=', null)->latest()->first()->created_at ?? null;

        // If there's an admin message, check if it's newer than the last client message
        if ($lastAdminMessage) {
            return $lastClientMessage < $lastAdminMessage;
        }

        // If there's no admin message, the ticket hasn't been replied to
        return false;
    }
}
