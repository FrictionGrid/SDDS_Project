<?php

namespace App\Http\Controllers;

use App\Services\ApifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApifyWebhookController extends Controller
{
    public function __construct(
        protected ApifyService $apifyService
    ) {}

    /**
     * Handle Apify webhook callback
     */
    public function webhook(Request $request)
    {
        Log::info('[ApifyWebhook] Received', [
            'payload' => $request->all(),
        ]);

        try {
            $payload = $request->all();
            $result = $this->apifyService->handleWebhook($payload);

            return response()->json([
                'success' => $result['ok'] ?? false,
                'message' => 'Webhook processed',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('[ApifyWebhook] Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
