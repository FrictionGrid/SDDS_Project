<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class LayoutOpenAIService
{
    // สำหรับการสนทนาแบบทั่วไป //
    public function chatGeneral(string $userMessage): string
    {

        return $this->callChat([
            ['role' => 'system', 'content' =>
            // prompt ก่อนส่งไปยัง OpenAI //
            'You are a helpful assistant. Answer naturally and concisely.'],
            // ข้อความจากผู้ใช้ //
            ['role' => 'user', 'content' => $userMessage],
        ]);
    }
    // สำหรับการสนทนาแบบมีบริบทเฉพาะ // ส่งพร้อมกับเอกสาร 
    public function chatSpecific(string $userMessage, string $contextText): string
    {
        // พรอมก่อนส่งไปยัง OpenAI //
$system = <<<SYS
คุณคือผู้ช่วย AI สำหรับองค์กร
กฎการตอบ:
1) ต้องตอบโดยใช้ข้อมูลจาก CONTEXT ที่ให้มาเท่านั้น
2) หากไม่พบคำตอบที่ระบุไว้อย่างชัดเจนใน CONTEXT ให้ตอบว่า: "ไม่มีข้อมูลในระบบ"
3) ห้ามใช้ความรู้ภายนอก ห้ามคาดเดา และห้ามเพิ่มข้อมูลที่ไม่ได้อยู่ใน CONTEXT
4) ตอบให้สั้น กระชับ และเป็นทางการเชิงธุรกิจ
SYS;

        $user = <<<USR
CONTEXT:
{$contextText}
QUESTION:
{$userMessage}
USR;

        return $this->callChat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ]);
    }
// เปลี่ยน setup การเรียก API ChatGPT //
    private function callChat(array $messages): string
    {
        $apiKey  = config('openai.api_key');
        $model   = config('openai.chat_model', 'gpt-4o-mini');
        $baseUrl = rtrim(config('openai.base_url'), '/');
        $timeout = (int) config('openai.timeout', 30);
// ไม่มีการตั้งค่า API Key //
        if (empty($apiKey)) {
            throw new \RuntimeException('OPENAI_API_KEY is not configured');
        }
// $res คือผลลัพธ์ที่ได้จากการเรียก API //
        try {
            $res = Http::timeout($timeout)
                ->withToken($apiKey)
                ->acceptJson()
                ->post($baseUrl . '/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2, // ลดโอกาสมั่ว
                ]);

            if (!$res->successful()) {
                Log::error('[Chat] API error', ['status' => $res->status(), 'body' => $res->body()]);
                throw new \RuntimeException('Chat API failed');
            }
// ดึงข้อความมาเป็น Array เเล้วเอาคำตอบเเรก //
            $json = $res->json();
            return trim($json['choices'][0]['message']['content'] ?? '');
        } catch (\Throwable $e) {
            Log::error('[Chat] Exception', ['err' => $e->getMessage()]);
            throw $e;
        }
    }
}
