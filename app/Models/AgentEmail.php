<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agentemail extends Model
{
    protected $table = 'agentemails';

    protected $fillable = [
        'user_id',
        'user_command',
        'recipient_label',
        'to_email',
        'customer_ref_id',
        'subject',
        'body',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }



    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }


    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
