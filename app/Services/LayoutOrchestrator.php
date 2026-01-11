<?php

namespace App\Services;

class LayoutOrchestrator
{
    public function __construct(
        protected LayoutMemoryService $memoryService,
        protected LayoutLogicService $logicService,
        protected LayoutClassifierService $classifier,
    ) {}

    public function handle(string $message, array $context): string
    {
        // เตรียม memory + messages
        $prepared = $this->memoryService->prepare($message, $context);

        $type = $this->classifier->classify($message);

        // สำหรับสองโหมด อนาคตต้องมาเปลี่ยนโครงสร้างตรงนี้สำหรับรองรับการขยาย //
           $answer = $type === 'specific'
        ? $this->logicService->handleSpecific($prepared['messages'], $prepared)
        : $this->logicService->handleGeneral($prepared['messages'], $prepared);

        // เก็บคำตอบของ AI
        $this->memoryService->storeAssistant($answer, $prepared);

        return $answer;
    }
}
