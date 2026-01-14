<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentSearchHistory extends Model
{
    protected $table = 'agentsearchhistory';

    protected $fillable = [
        'user_id',
        'command',
        'query',
        'target_urls',
        'apify_run_id',
        'apify_dataset_id',
        'status',
        'total_found',
        'inserted_count',
        'customer_batch_id',
    ];

    protected $casts = [
        'target_urls' => 'array',
        'total_found' => 'integer',
        'inserted_count' => 'integer',
        'customer_batch_id' => 'integer',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * สำหรับอนาคต:
     * batch ของลูกค้าที่ถูก import เข้า DB
     * (ตอนนี้ยังเป็น null เพราะใช้ Google Sheet)
     */
    // public function customerBatch(): BelongsTo
    // {
        // return $this->belongsTo(CustomerBatch::class, 'customer_batch_id');
    // }
}
