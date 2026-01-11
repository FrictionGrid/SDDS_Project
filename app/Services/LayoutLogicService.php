<?php

namespace App\Services;


class LayoutLogicService
{
    public function __construct(
        protected LayoutOpenAIService $chatService,
        protected LayoutSearchService $searchService,
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

    public function handleSpecific(array $messages, array $context): string
{
    $userMessage = end($messages)['content'];

    $chunks = $this->searchService->search($userMessage, 5);

    if (count($chunks) === 0 || $chunks[0]['score'] < 0.2) {
        return "ไม่มีข้อมูลในระบบ";
    }

    $contextText = implode("\n\n", array_map(
        fn ($c) => $c['content'],
        $chunks
    ));

    $system = [
        'role' => 'system',
        'content' => "You are an enterprise AI. 
You MUST answer ONLY using the CONTEXT below.
If the answer is not in the context, reply exactly: ไม่มีข้อมูลในระบบ

CONTEXT:
{$contextText}"
    ];

    return $this->chatService->chat([
        $system,
        ['role' => 'user', 'content' => $userMessage],
    ], ['temperature' => 0]);
}
}
