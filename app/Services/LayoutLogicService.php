<?php

namespace App\Services;


use Illuminate\Support\Collection;
use App\Services\LayoutSearchService;
use App\Services\LayoutOpenAIService;
use App\Services\AgentEmailService;
use App\Models\ChatHistorys;

class LayoutLogicService
{
    public function __construct(
        protected LayoutOpenAIService $chatService,
    ) {}
    // ฟังชั่นหลักสำหรับการจัดการเเชททั่วไป // เป็นการตอบกลับเเบบธรรมดา 
    // อันนี้ตอบ ส่วนที่ดึงมาคือคิดยังไง //
 public function handleGeneral(string $message, array $context = []): string
{
    // เตรียม prompt
    $messages = [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => $message],
    ];

    // เรียก AI
    return $this->chatService->chat($messages);
}
    // ฟังชั่นหลักสำหรับการจัดการเเชทมีบริบทเฉพาะ // 
    private function handlespecific(array $chunks): string
    {
        $lines = [];
        foreach ($chunks as $c) {
            $meta = $c['metadata'] ?? [];
            $title = $meta['title'] ?? ($c['document_title'] ?? 'Untitled');
            $chunkIndex = $c['chunk_index'] ?? null;

            $lines[] = "### Source: {$title} | chunk={$chunkIndex}\n" . trim($c['content'] ?? '');
        }
        return implode("\n\n", $lines);
    }

    private function handleAgentemail(string $message, array $classification, array $context = []): string
    {
        $agent = $classification['agent'] ?? 'unknown';

        if ($agent === 'email') {
            return 'รับคำสั่งแล้ว (Email Agent) แต่ตอนนี้ระบบยังไม่เปิดใช้งานการส่งอีเมล';
        }

        return 'รับคำสั่งแล้ว แต่ Agent นี้ยังไม่ถูกเปิดใช้งานในระบบ';
    }
}
