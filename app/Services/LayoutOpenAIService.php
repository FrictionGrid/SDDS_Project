<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class LayoutOpenAIService
{
    /**
     * ส่งข้อความไปยัง OpenAI
     * Service นี้ "ไม่รู้บริบท" และ "ไม่ตัดสินใจ"
     */
    public function chat(array $messages, array $options = []): string
    {
        return $this->callChat($messages, $options);
    }

    /**
     * Low-level OpenAI API call
     */
    protected function callChat(array $messages, array $options = []): string
    {
        $apiKey  = config('openai.api_key');
        $model   = $options['model'] ?? config('openai.chat_model', 'gpt-4o-mini');
        $timeout = (int) config('openai.timeout', 30);
        $baseUrl = rtrim(config('openai.base_url'), '/');

        if (empty($apiKey)) {
            throw new \RuntimeException('OPENAI_API_KEY is not configured');
        }

        try {
            /** @var Response $res */
            $res = Http::timeout($timeout)
                ->withToken($apiKey)
                ->acceptJson()
                ->post($baseUrl . '/chat/completions', [
                    'model'       => $model,
                    'messages'    => $messages,
                    'temperature' => $options['temperature'] ?? 0.2,
                ]);

            if (!$res->successful()) {
                Log::error('[OpenAI] API error', [
                    'status' => $res->status(),
                    'body'   => $res->body(),
                ]);

                throw new \RuntimeException('OpenAI chat API failed');
            }

            $json = $res->json();

            return trim(
                $json['choices'][0]['message']['content'] ?? ''
            );
        } catch (\Throwable $e) {
            Log::error('[OpenAI] Exception', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
