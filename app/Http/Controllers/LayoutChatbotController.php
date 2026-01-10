<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\LayoutLogicService;

class LayoutChatbotController extends Controller
{
    public function chat(Request $request, LayoutLogicService $logic)
    {
        // รับข้อความจากผู้ใช้ //
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);
        // สำหรับการระบุ ID ของการสนทนา // การทำเเชทต่อเนื่อง
        $conversationId = $request->input('conversation_id')
            ?: (string) Str::uuid();
        // ส่งไปเเชททั่วไป
        $answer = $logic->handleGeneral(
            trim($validated['message']),
            [
                'conversation_id' => $conversationId,
                'user_id' => optional($request->user())->id,
            ]
        );
        // รหัสการสนทนา ประเภท เเละคำตอบ //
        return response()->json([
            'conversation_id' => $conversationId,
            'type' => 'general',
            'answer' => $answer,
        ]);
    }
}
