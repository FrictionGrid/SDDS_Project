<?php

namespace App\Services;

use App\Models\AgentSearchHistory;
use App\Services\CustomerApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyService
{
    public function __construct(
        protected CustomerApiService $customerApi,
    ) {}

    /**
     * เริ่มรัน Apify Web-Scraper
     */
    public function runSearch(array $plan, array $meta = []): array
    {
        $token = config('apify.token');
        $actorId = config('apify.actor_id');
        $webhookUrl = config('apify.webhook_url');

        if (!$token || !$actorId) {
            return ['ok' => false, 'message' => 'Apify config ไม่ครบ'];
        }

        $input = $this->buildActorInput($plan);

        try {
            $url = "https://api.apify.com/v2/acts/{$actorId}/runs?token={$token}";

            Log::info('[Apify] Starting run', [
                'actor_id' => $actorId,
                'query' => $plan['query'] ?? '',
                'limit' => $plan['limit'] ?? 10,
                'webhook_url' => $webhookUrl ?? 'not configured',
            ]);

            if (!$webhookUrl) {
                Log::warning('[Apify] No webhook URL configured - please set webhook in Apify Actor settings');
            } else {
                Log::info('[Apify] Webhook should be configured in Apify Console', [
                    'expected_webhook_url' => $webhookUrl,
                    'note' => 'Go to https://console.apify.com/actors and configure webhook in Actor settings'
                ]);
            }

            // ส่ง request โดยไม่ส่ง webhook ผ่าน API
            // Webhook ควรตั้งค่าถาวรใน Apify Console แทน
            $res = Http::timeout(30)->post($url, $input);

            if ($res->failed()) {
                Log::error('[Apify] Run failed', ['body' => $res->body()]);
                return ['ok' => false, 'message' => 'Apify run failed'];
            }

            $data = $res->json();

            Log::info('[Apify] Run started successfully', [
                'run_id' => data_get($data, 'data.id'),
                'status' => data_get($data, 'data.status'),
            ]);

            return [
                'ok' => true,
                'run_id' => data_get($data, 'data.id'),
                'status' => data_get($data, 'data.status'),
            ];
        } catch (\Throwable $e) {
            Log::error('[Apify] Exception', ['err' => $e->getMessage()]);
            return ['ok' => false, 'message' => 'Apify exception'];
        }
    }

    /**
     * ตรวจสอบสถานะของ Apify run
     */
    public function checkRunStatus(string $runId): array
    {
        $token = config('apify.token');
        $url = "https://api.apify.com/v2/actor-runs/{$runId}?token={$token}";

        try {
            $res = Http::timeout(30)->get($url);

            if ($res->failed()) {
                Log::error('[Apify] Check run status failed', [
                    'run_id' => $runId,
                    'body' => $res->body()
                ]);
                return ['ok' => false, 'message' => 'Failed to check run status'];
            }

            $data = $res->json();
            $status = data_get($data, 'data.status');
            $datasetId = data_get($data, 'data.defaultDatasetId');

            Log::info('[Apify] Run status checked', [
                'run_id' => $runId,
                'status' => $status,
                'dataset_id' => $datasetId,
            ]);

            return [
                'ok' => true,
                'run_id' => $runId,
                'status' => $status,
                'dataset_id' => $datasetId,
                'data' => $data['data'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('[Apify] Check run status exception', [
                'run_id' => $runId,
                'err' => $e->getMessage()
            ]);
            return ['ok' => false, 'message' => 'Exception checking run status'];
        }
    }

    /**
     * Webhook handler
     */
    public function handleWebhook(array $payload): array
    {
        $status = data_get($payload, 'eventData.status');
        $datasetId = data_get($payload, 'eventData.resource.defaultDatasetId');
        $runId = data_get($payload, 'eventData.resource.id');

        Log::info('[Apify] Webhook received', [
            'status' => $status,
            'run_id' => $runId,
            'dataset_id' => $datasetId,
        ]);

        // หา history record จาก run_id
        $history = AgentSearchHistory::where('apify_run_id', $runId)->first();

        if (!$history) {
            Log::warning('[Apify] No history found for run_id', ['run_id' => $runId]);
        }

        // ถ้า status ไม่สำเร็จ
        if ($status !== 'SUCCEEDED' || !$datasetId) {
            if ($history) {
                $history->update([
                    'status' => 'failed',
                    'apify_dataset_id' => $datasetId,
                ]);
            }
            return ['ok' => false, 'message' => 'Apify not succeeded'];
        }

        $items = $this->fetchDataset($datasetId);
        $rows = $this->mapToSheetRows($items);
        $insert = $this->insertSheet($rows);

        $this->customerApi->clearCache();

        // Update history record
        if ($history) {
            $history->update([
                'status' => 'completed',
                'apify_dataset_id' => $datasetId,
                'total_found' => count($items),
                'inserted_count' => $insert['inserted_count'] ?? 0,
            ]);

            Log::info('[Apify] History updated', [
                'history_id' => $history->id,
                'total_found' => count($items),
                'inserted_count' => $insert['inserted_count'] ?? 0,
            ]);
        }

        return $insert;
    }

    /**
     * Input สำหรับ Web-Scraper Actor
     */
protected function buildActorInput(array $plan): array
{
    $limit = (int)($plan['limit'] ?? 10);

    return [
        "startUrls" => [
            ["url" => "https://www.thaitradefair.com/exhibitor-list.php"]
        ],

        "pageFunction" => $this->pageFunction(),

        "proxyConfiguration" => [
            "useApifyProxy" => true
        ],

        // === optional but powerful ===
        "customData" => [
            "query" => $plan['query'] ?? '',
            "limit" => $limit
        ],

        "maxResultsPerCrawl" => $limit,
        "maxPagesPerCrawl" => 0,
        "waitUntil" => ["networkidle2"],
        "useChrome" => false,
        "headless" => true,
    ];
}

protected function pageFunction(): string
{
    return <<<'JS'
async function pageFunction(context) {
    const { page, request, log, customData } = context;

    log.info(`Scraping: ${request.url}`);

    // รอให้ company list โหลด
    await page.waitForSelector(".checkbox-list-company", { timeout: 30000 });

    const results = await page.evaluate(() => {
        const companyLinks = Array.from(document.querySelectorAll(".checkbox-list-company a"));

        return companyLinks.map(link => {
            const companyName = link.innerText.trim();
            if (!companyName) return null;

            return {
                company: companyName,
                customer_type: "Lead",
                business_type: "",
                email: "",
                phone: "",
                phone_backup: "",
                address: "",
                source_url: location.href
            };
        }).filter(x => x);
    });

    log.info(`Found ${results.length} companies from list`);

    return results.map(item => ({
        ...item,
        scraped_at: new Date().toISOString(),
        query: customData.query || ""
    }));
}
JS;
}

    public function fetchDataset(string $datasetId): array
    {
        $token = config('apify.token');
        $url = "https://api.apify.com/v2/datasets/{$datasetId}/items?clean=true&format=json&token={$token}";

        Log::info('[Apify] Fetching dataset', ['dataset_id' => $datasetId]);

        try {
            $res = Http::timeout(30)->get($url);
            $items = $res->ok() ? $res->json() : [];

            Log::info('[Apify] Dataset fetched', [
                'dataset_id' => $datasetId,
                'item_count' => count($items),
                'sample_item' => $items[0] ?? null,
            ]);

            return $items;
        } catch (\Throwable $e) {
            Log::error('[Apify] Dataset error', ['err' => $e->getMessage()]);
            return [];
        }
    }

    protected function mapToSheetRows(array $items): array
    {
        $rows = [];

        Log::info('[Apify] Mapping items to sheet rows', ['item_count' => count($items)]);

        foreach ($items as $it) {
            if (empty($it['company'])) {
                Log::debug('[Apify] Skipping item without company name', ['item' => $it]);
                continue;
            }

            $note = 'Source: ' . ($it['source_url'] ?? '');
            if (!empty($it['website'])) {
                $note .= ' | Website: ' . $it['website'];
            }

            $rows[] = [
                'name' => $it['company'],
                'source' => 'Apify',
                'latest_status' => 'new',
                'customer_type' => $it['customer_type'] ?? 'Lead',
                'Business_type' => $it['business_type'] ?? '',
                'email' => $it['email'] ?? '',
                'phone' => $it['phone'] ?? '',
                'phone_backup' => $it['phone_backup'] ?? '',
                'company_address' => $it['address'] ?? '',
                'start_date' => '',
                'project_value' => '',
                'main_contact' => '',
                'note' => $note,
            ];
        }

        Log::info('[Apify] Mapped to sheet rows', [
            'row_count' => count($rows),
            'sample_row' => $rows[0] ?? null,
        ]);

        return $rows;
    }

    protected function insertSheet(array $rows): array
    {
        if (count($rows) === 0) {
            Log::info('[Sheet] No rows to insert');
            return ['ok' => true, 'inserted_count' => 0];
        }

        $url = config('services.customer_api.url') . '?action=insertCustomers';

        Log::info('[Sheet] Inserting rows to Google Sheet', [
            'row_count' => count($rows),
            'url' => $url,
        ]);

        try {
            $res = Http::post($url, ['items' => $rows]);
            $result = $res->json();

            Log::info('[Sheet] Insert completed', [
                'result' => $result,
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::error('[Sheet] Insert error', ['err' => $e->getMessage()]);
            return ['ok' => false];
        }
    }
}
