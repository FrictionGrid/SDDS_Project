<?php

namespace App\Services;

use App\Services\Agent\AgentEmailService;
use App\Services\LayoutMemoryService;
use App\Services\LayoutLogicService;
use App\Services\LayoutClassifierService;

class LayoutOrchestrator
{
    public function __construct(
        protected LayoutMemoryService $memoryService,
        protected LayoutLogicService $logicService,
        protected LayoutClassifierService $classifier,
        protected AgentEmailService $agentEmail,
    ) {}

    public function handle(string $message, array $context): string
    {
        $type = $this->classifier->classify($message);

        // ถ้าเป็น Email Agent //
        if ($type === 'agent_email') {

            // เก็บคำสั่งของ user ใน chat history
            $prepared = $this->memoryService->prepare($message, $context);

            // ให้ AgentEmail ทำงาน
            $result = $this->agentEmail->createDraft(
                $message,
                $context['user_id'] ?? null
            );

            // เก็บผลลัพธ์ของ Agent ลง chat history
            $this->memoryService->storeAssistant($result, $prepared);

            return $result;
        }

        $prepared = $this->memoryService->prepare($message, $context);

        //กรณี specific/general
        $answer = $type === 'specific'
            ? $this->logicService->handleSpecific($prepared['messages'], $prepared)
            : $this->logicService->handleGeneral($prepared['messages'], $prepared);

        $this->memoryService->storeAssistant($answer, $prepared);

        return $answer;
    }
}
