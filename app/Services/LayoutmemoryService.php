<?php

namespace App\Services;

class LayoutmemoryService
{
// เตรียมสำหรับการคุยยาว //
    public function prepare(string $message, array $context): array
    {
        return [
            'message' => trim($message),
            'conversation_id' => $context['conversation_id'] ?? null,
            'user_id' => $context['user_id'] ?? null,
        ];
    }
}
