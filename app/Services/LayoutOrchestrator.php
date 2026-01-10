<?php

namespace App\Services;

class LayoutOrchestrator
{
    public function __construct(
        protected LayoutmemoryService $memoryService,
        protected LayoutLogicService $logicService,
    ) {}

    public function handle(string $message, array $context): string
    {

        $preparedContext = $this->memoryService->prepare(
            $message,
            $context
        );

        return $this->logicService->handleGeneral(
            $preparedContext['message'],
            $preparedContext
        );
    }
}
