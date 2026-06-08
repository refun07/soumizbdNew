<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'file_path',
        'file_type',
        'original_name',
        'file_size',
    ];

    protected $appends = [
        'full_url',
        'file_extension',
        'is_image',
        'is_video',
        'formatted_size',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getFullUrlAttribute()
    {
        return url($this->file_path);
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    public function getIsImageAttribute()
    {
        return $this->file_type === 'image';
    }

    public function getIsVideoAttribute()
    {
        return $this->file_type === 'video';
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if ($attachment->file_path) {
                Storage::delete(ltrim($attachment->file_path, '/'));
            }
        });
    }
}