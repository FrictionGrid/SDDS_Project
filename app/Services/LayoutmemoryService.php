<?php

namespace App\Services;

use App\Models\ChatHistorys;
use Illuminate\Support\Arr;

class LayoutMemoryService
{
    protected int $maxMessages = 20; // ป้องกัน context ยาวเกินไป

    public function prepare(string $message, array $context): array
    {
        $conversationId = $context['conversation_id'];
        $userId = $context['user_id'] ?? null;

        // 1) ดึงประวัติเก่า
        $history = ChatHistorys::byConversation($conversationId)
            ->orderBy('id', 'desc')
            ->limit($this->maxMessages)
            ->get()
            ->reverse();

        // 2) แปลงเป็น OpenAI format
        $messages = [];

        // system prompt คุม hallucination ระดับ global
        $messages[] = [
            'role' => 'system',
            'content' => 'You are an AI assistant for a business system. Answer clearly and accurately.'
        ];

        foreach ($history as $row) {
            $messages[] = [
                'role' => $row->role,
                'content' => $row->content,
            ];
        }

        // 3) ใส่ข้อความใหม่ของ user
        $messages[] = [
            'role' => 'user',
            'content' => trim($message),
        ];

        // 4) บันทึก user message
        ChatHistorys::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'role' => 'user',
            'content' => trim($message),
        ]);

        return [
            'messages' => $messages,
            'conversation_id' => $conversationId,
            'user_id' => $userId,
        ];
    }

    public function storeAssistant(string $answer, array $context): void
    {
        ChatHistorys::create([
            'conversation_id' => $context['conversation_id'],
            'user_id' => $context['user_id'] ?? null,
            'role' => 'assistant',
            'content' => $answer,
        ]);
    }
}
