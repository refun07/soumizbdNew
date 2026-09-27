<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashPersonalPayment extends Model
{
    public const METHOD = 'bkash_personal';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        'verified_at' => 'datetime',
    ];

    public function orderGroup()
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'senderNumber' => $this->sender_number,
            'trxId' => $this->trx_id,
            'amount' => (float) $this->amount,
            'status' => $this->status,
            'adminNote' => $this->admin_note,
            'verifiedBy' => $this->verifier?->name,
            'verifiedAt' => $this->verified_at?->format('d M, Y h:i A'),
            'submittedAt' => $this->created_at?->format('d M, Y h:i A'),
        ];
    }
}
