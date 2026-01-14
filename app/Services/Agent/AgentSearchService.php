<?php

namespace App\Services\Agent;

use App\Models\AgentSearchHistory;
use App\Services\LayoutOpenAIService;
use App\Services\ApifyService;
use Illuminate\Support\Facades\Log;

/**
 * AgentSearchService
 * - รับคำสั่งจาก user
 * - ใช้ AI แปลงคำสั่ง -> SearchPlan (JSON)
 * - ตรวจ/normalize plan
 * - ส่งต่อให้ ApifyService ทำงานจริง (scrape + เขียน Sheet + clear cache)
 */
class AgentSearchService
{
    public function __construct(
        protected LayoutOpenAIService $ai,
        protected ApifyService $apify,
    ) {}

    /**
     * Handle method สำหรับ LayoutOrchestrator
     * @return string reply message
     */
    public function handle(string $userCommand, array $context = []): string
    {
        $userId = $context['user_id'] ?? null;

        $result = $this->startJob($userCommand, $userId, $context);

        if (!$result['ok']) {
            return $result['message'] ?? 'เกิดข้อผิดพลาดในการค้นหา';
        }

        return $result['message'];
    }


    public function startJob(string $userCommand, ?int $userId = null, array $context = []): array
    {
        $plan = $this->buildSearchPlan($userCommand, $context);

        if (!$plan['ok']) {
            return [
                'ok' => false,
                'message' => $plan['message'] ?? 'ไม่สามารถแปลงคำสั่งเป็นแผนค้นหาได้',
            ];
        }

        $searchPlan = $plan['plan'];

 
        $searchPlan['limit'] = max(1, min((int)($searchPlan['limit'] ?? 3), 20));

        $run = $this->apify->runSearch($searchPlan, [
            'user_id' => $userId,
            'original_command' => $userCommand,
        ]);

        if (!$run['ok']) {
            return [
                'ok' => false,
                'message' => $run['message'] ?? 'เรียก Apify ไม่สำเร็จ',
            ];
        }

        // บันทึก history record
        $history = AgentSearchHistory::create([
            'user_id' => $userId,
            'command' => $userCommand,
            'query' => $searchPlan['query'],
            'target_urls' => [$searchPlan['query']], // เก็บ query ที่ใช้ค้นหา
            'apify_run_id' => $run['run_id'],
            'status' => 'running',
        ]);

        Log::info('[AgentSearch] History created', [
            'history_id' => $history->id,
            'run_id' => $run['run_id'],
        ]);

        return [
            'ok' => true,
            'message' => 'เริ่มค้นหาลูกค้าแล้ว ระบบจะอัปเดตผลลัพธ์เมื่อ Apify ทำงานเสร็จ',
            'run_id' => $run['run_id'] ?? null,
            'status' => $run['status'] ?? null,
            'history_id' => $history->id,
        ];
    }


    protected function buildSearchPlan(string $userCommand, array $context = []): array
    {
        $system = <<<SYS
คุณคือ AI ที่ทำหน้าที่ "แปลงคำสั่งค้นหาลูกค้า" ให้เป็น JSON SearchPlan เท่านั้น

กฎ:
- ตอบกลับเป็น JSON อย่างเดียว ห้ามมีข้อความอื่น ห้าม markdown
- ห้ามเดาข้อมูลบริษัทจริง ห้ามสร้างชื่อบริษัท/อีเมลเอง
- ทำแค่สรุป "ต้องไปค้นหาอะไร" เพื่อส่งให้ web-scraper
- โครงสร้าง JSON ต้องเป็นแบบนี้เสมอ:

{
  "query": "string",
  "limit": number,
  "customer_type": "Lead|Customer|Partner|Unknown",
  "business_type": "string|null",
  "location": "string|null",
  "fields": ["company","website","email","phone","address","source_url"],
  "notes": "string|null"
}

แนวทาง:
- query ต้องชัดพอสำหรับค้นเว็บ (ภาษาไทย/อังกฤษได้)
- fields ต้องเป็น subset ของรายการที่กำหนด
- limit ถ้าผู้ใช้ไม่ระบุ ให้เป็น 3
SYS;

        $user = "คำสั่งผู้ใช้: {$userCommand}";

        try {
            $raw = $this->ai->chat([
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ], ['temperature' => 0]);

            $json = $this->extractJson($raw);
            $plan = json_decode($json, true);

            if (!is_array($plan)) {
                return ['ok' => false, 'message' => 'AI ส่งผลลัพธ์ไม่ใช่ JSON ที่ถูกต้อง'];
            }

            // normalize + default
            $plan = $this->normalizePlan($plan);

            // validate minimal requirements
            if (empty($plan['query'])) {
                return ['ok' => false, 'message' => 'SearchPlan ไม่มี query'];
            }

            return ['ok' => true, 'plan' => $plan];
        } catch (\Throwable $e) {
            Log::error('AgentSearchService buildSearchPlan error', [
                'err' => $e->getMessage(),
            ]);
            return ['ok' => false, 'message' => 'เกิดข้อผิดพลาดตอนสร้าง SearchPlan'];
        }
    }

    protected function normalizePlan(array $plan): array
    {
        $allowedFields = ['company','website','email','phone','address','source_url'];

        $plan['query'] = trim((string)($plan['query'] ?? ''));
        $plan['limit'] = (int)($plan['limit'] ?? 3);

        $plan['customer_type'] = (string)($plan['customer_type'] ?? 'Lead');
        $plan['customer_type'] = in_array($plan['customer_type'], ['Lead','Customer','Partner','Unknown'], true)
            ? $plan['customer_type']
            : 'Unknown';

        $plan['business_type'] = isset($plan['business_type']) ? (string)$plan['business_type'] : null;
        $plan['location'] = isset($plan['location']) ? (string)$plan['location'] : null;
        $plan['notes'] = isset($plan['notes']) ? (string)$plan['notes'] : null;

        $fields = $plan['fields'] ?? ['company','website','email','phone','address','source_url'];
        if (!is_array($fields)) $fields = ['company','website','email','phone','address','source_url'];

        $fields = array_values(array_unique(array_filter($fields, fn($f) => in_array($f, $allowedFields, true))));
        if (count($fields) === 0) $fields = ['company','website','email','phone','address','source_url'];

        $plan['fields'] = $fields;

        return $plan;
    }

    
    protected function extractJson(string $raw): string
    {
        $raw = trim($raw);

        if (str_starts_with($raw, '{') && str_ends_with($raw, '}')) {
            return $raw;
        }


        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return substr($raw, $start, $end - $start + 1);
        }

        return $raw;
    }
}
