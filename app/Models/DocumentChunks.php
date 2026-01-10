<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentChunks extends Model
{
    protected $table = 'document_chunks';

    protected $fillable = [
        'document_id','chunk_index','content','embedding','metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata' => 'array',
        'chunk_index' => 'integer',
    ];


    public function document(): BelongsTo
    {
        return $this->belongsTo(DocumentAI::class, 'document_id', 'uuid');
    }

    public function scopeByDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId)
                    ->orderBy('chunk_index', 'asc');
    }

    public function scopeOrderedByIndex($query)
    {
        return $query->orderBy('chunk_index', 'asc');
    }
}
