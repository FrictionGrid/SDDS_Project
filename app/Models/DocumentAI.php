<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DocumentAI extends Model
{
    protected $table = 'documentai';

    protected $fillable = [
        'uuid','title','source_type','original_name','mime_type','disk','path',
        'size_bytes','status','scope_type','scope_id','metadata','error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

  
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunks::class, 'document_id', 'uuid');
    }


    public function scopeIndexed($query)
    {
        return $query->where('status', 'indexed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByScope($query, $scopeType, $scopeId)
    {
        return $query->where('scope_type', $scopeType)
                    ->where('scope_id', $scopeId);
    }
}
