<?php

namespace App\Services;

class LayoutOrchestrator
{
    public function __construct(
        protected LayoutMemoryService $memoryService,
        protected LayoutLogicService $logicService,
    ) {}

    public function handle(string $message, array $context): string
    {
        // เตรียม memory + messages
        $prepared = $this->memoryService->prepare($message, $context);

        // ตอนนี้ยังเรียก general (เดี๋ยวใส่ classifier ทีหลัง)
        $answer = $this->logicService->handleGeneral(
            $prepared['messages'],
            $prepared
        );

        // เก็บคำตอบของ AI
        $this->memoryService->storeAssistant($answer, $prepared);

        return $answer;
    }
}
