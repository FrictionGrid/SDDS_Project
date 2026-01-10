<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ChatHistorys extends Model
{
    protected $table = 'chathistorys'; 

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'content',
        'scope_type',
        'scope_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    public function scopeByConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId)
                     ->orderBy('id', 'asc');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByScope($query, $scopeType, $scopeId)
    {
        return $query->where('scope_type', $scopeType)
                     ->where('scope_id', $scopeId);
    }
}
