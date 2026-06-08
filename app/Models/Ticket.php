<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_id',
        'user_id',
        'order_id',
        'phone',
        'subject',
        'description',
        'audio_path',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected $appends = [
        'audio_url',
        'status_label',
        'priority_label',
        'status_badge_class',
        'priority_badge_class',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (empty($ticket->public_id)) {
                $ticket->public_id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $adminId)
    {
        return $query->where('assigned_to', $adminId);
    }

    public function getAudioUrlAttribute()
    {
        return $this->audio_path ? url($this->audio_path) : null;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'open' => translate('Open'),
            'in_progress' => translate('In Progress'),
            'resolved' => translate('Resolved'),
            'closed' => translate('Closed'),
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => translate('Low'),
            'medium' => translate('Medium'),
            'high' => translate('High'),
        ];
        
        return $labels[$this->priority] ?? $this->priority;
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'open' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
        ];
        
        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPriorityBadgeClassAttribute()
    {
        $classes = [
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800',
        ];
        
        return $classes[$this->priority] ?? 'bg-gray-100 text-gray-800';
    }
}