<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachment_path',
        'is_admin_reply',
        'read_at',
    ];

    protected $casts = [
        'is_admin_reply' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'attachment_url',
        'is_read',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment_path) {
            return url($this->attachment_path);
        }
        return null;
    }

    public function getIsReadAttribute()
    {
        return $this->read_at !== null;
    }

    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}