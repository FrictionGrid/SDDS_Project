<?php

namespace App\Console\Commands;

use App\Services\ApifyService;
use App\Models\AgentSearchHistory;
use Illuminate\Console\Command;

class CheckApifyRun extends Command
{
    protected $signature = 'apify:check {run_id? : The Apify run ID to check}';
    protected $description = 'Check Apify run status';

    public function handle(ApifyService $apify)
    {
        $runId = $this->argument('run_id');

        // ถ้าไม่ระบุ run_id ให้เอา run ล่าสุด
        if (!$runId) {
            $latest = AgentSearchHistory::latest()->first();
            if (!$latest) {
                $this->error('No Apify runs found in history');
                return 1;
            }
            $runId = $latest->apify_run_id;
            $this->info("Using latest run: {$runId}");
        }

        $this->info("Checking Apify run status...");
        $result = $apify->checkRunStatus($runId);

        if (!$result['ok']) {
            $this->error('Failed to check run status: ' . ($result['message'] ?? 'Unknown error'));
            return 1;
        }

        $status = $result['status'];
        $datasetId = $result['dataset_id'] ?? 'N/A';

        $this->info("Run ID: {$runId}");
        $this->info("Status: {$status}");
        $this->info("Dataset ID: {$datasetId}");

        // แสดงข้อมูลเพิ่มเติม
        if ($status === 'SUCCEEDED' && $datasetId && $datasetId !== 'N/A') {
            $this->info("\n✅ Run completed successfully!");
            $this->info("Dataset: https://console.apify.com/storage/datasets/{$datasetId}");

            // ดึงข้อมูลจาก dataset
            $this->info("\nFetching dataset items...");
            $items = $apify->fetchDataset($datasetId);
            $this->info("Found " . count($items) . " items");

            if (count($items) > 0) {
                $this->info("\nFirst item:");
                $this->line(json_encode($items[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } elseif ($status === 'RUNNING') {
            $this->warn("⏳ Run is still in progress...");
        } elseif ($status === 'READY') {
            $this->warn("🔵 Run is ready to start...");
        } elseif ($status === 'FAILED') {
            $this->error("❌ Run failed!");
        }

        return 0;
    }
}
