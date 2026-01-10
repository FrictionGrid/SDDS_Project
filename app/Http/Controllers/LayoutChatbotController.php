<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\LayoutOrchestrator;

class LayoutChatbotController extends Controller
{
    public function chat(Request $request, LayoutOrchestrator $orchestrator)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'conversation_id' => ['nullable', 'uuid'],
        ]);

        $conversationId = $validated['conversation_id'] ?? (string) Str::uuid();

        $answer = $orchestrator->handle(
            $validated['message'],
            [
                'conversation_id' => $conversationId,
                'user_id' => optional($request->user())->id,
            ]
        );

        return response()->json([
            'conversation_id' => $conversationId,
            'answer' => $answer,
        ]);
    }
}
