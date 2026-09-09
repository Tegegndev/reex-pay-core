<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'admin_id', 'message', 'attachment'];

    /**
     * Get the URL for the file attachment, if applicable.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
