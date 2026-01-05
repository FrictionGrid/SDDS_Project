<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactHistory extends Model
{
    protected $fillable = [
        'customer_id',
        'contact_type',
        'subject',
        'description',
        'contacted_at',
        'contacted_by',
        'status',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    /**
     * Default scope: เรียงตามวันที่ล่าสุดก่อน
     */
    protected static function booted()
    {
        static::addGlobalScope('latest', function ($query) {
            $query->orderBy('contacted_at', 'desc');
        });
    }
}
