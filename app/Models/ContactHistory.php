<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactHistory extends Model
{
     protected $table = 'contacthistory';
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


    protected static function booted()
    {
        static::addGlobalScope('latest', function ($query) {
            $query->orderBy('contacted_at', 'desc');
        });
    }
}
