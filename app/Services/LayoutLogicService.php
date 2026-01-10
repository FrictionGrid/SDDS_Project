<?php
namespace App\Services;

use App\Services\LayoutSearchService;
use App\Services\LayoutOpenAIService;
use App\Services\AgentEmailService;

class LayoutLogicService
{   
    // เพิ่ม services ต่างๆมาใช้สำหรับฟังชั่น chat ในอนาคต // เเยก service ชัดเจน 
    public function __construct(
        protected LayoutOpenAIService $chatService,
    ) {}
    // ฟังชั่นหลักสำหรับการจัดการเเชททั่วไป // เป็นการตอบกลับเเบบธรรมดา 
    // อันนี้ตอบ ส่วนที่ดึงมาคือคิดยังไง //
    public function handleGeneral(string $message, array $context = []): string
    {
        return $this->chatService->chatGeneral($message);
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
