<?php

namespace App\Services;

class LayoutLogicService
{
    public function __construct(
        protected LayoutOpenAIService $chatService,
    ) {}


    public function handleGeneral(array $messages, array $context = []): string
    {
        // คุณสามารถปรับ system prompt ตรงนี้ได้
        $systemPrompt = [
            'role' => 'system',
            'content' => 'You are a helpful AI assistant for a business system. Answer clearly and concisely.'
        ];

        // ถ้า messages มี system อยู่แล้ว (จาก Memory) ให้แทนที่
        if ($messages[0]['role'] === 'system') {
            $messages[0] = $systemPrompt;
        } else {
            array_unshift($messages, $systemPrompt);
        }

        return $this->chatService->chat($messages, [
            'temperature' => 0.2,
        ]);
    }
}
