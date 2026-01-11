<?php

namespace App\Services\Document;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class EmbeddingService
{
    public function embed(string $text): array
    {
        $text = trim($text);
        if ($text === '') return [];

        $cacheKey = 'emb:' . sha1($text);
        return Cache::remember($cacheKey, 60 * 24, function () use ($text) {
            return $this->callEmbeddingApi([$text])[0] ?? [];
        });
    }

    /**
     * @param array<int, string> $texts
     * @return array<int, array> vectors
     */
    public function embedBatch(array $texts): array
    {
        $texts = array_values(array_map('trim', $texts));
        $texts = array_values(array_filter($texts, fn ($t) => $t !== ''));

        if (count($texts) === 0) return [];

        // simple batching to avoid huge payload (tune as needed)
        $batchSize = 64;
        $out = [];

        for ($i = 0; $i < count($texts); $i += $batchSize) {
            $slice = array_slice($texts, $i, $batchSize);

            // Cache per item
            $vectors = [];
            $toRequest = [];
            $map = [];

            foreach ($slice as $idx => $t) {
                $key = 'emb:' . sha1($t);
                $cached = Cache::get($key);
                if (is_array($cached) && count($cached) > 0) {
                    $vectors[$idx] = $cached;
                } else {
                    $map[count($toRequest)] = $idx; // reqIndex -> localIndex
                    $toRequest[] = $t;
                }
            }

            if (count($toRequest) > 0) {
                $fresh = $this->callEmbeddingApi($toRequest);
                foreach ($fresh as $reqIndex => $vec) {
                    $localIndex = $map[$reqIndex];
                    $vectors[$localIndex] = $vec;
                    Cache::put('emb:' . sha1($slice[$localIndex]), $vec, 60 * 24);
                }
            }

            // Preserve order
            ksort($vectors);
            foreach ($vectors as $vec) {
                $out[] = $vec;
            }
        }

        return $out;
    }

    /**
     * @param array<int, string> $inputs
     * @return array<int, array> vectors
     */
    private function callEmbeddingApi(array $inputs): array
    {
        $apiKey = config('openai.api_key');
        $model = config('openai.embedding_model', 'text-embedding-3-small');
        $timeout = (int) config('openai.timeout', 60);

        if (!$apiKey) {
            throw new RuntimeException("OPENAI_API_KEY is missing");
        }

        $payload = [
            'model' => $model,
            'input' => $inputs,
        ];

        $lastErr = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $res = Http::withToken($apiKey)
                    ->timeout($timeout)
                    ->acceptJson()
                    ->post('https://api.openai.com/v1/embeddings', $payload);

                if (!$res->successful()) {
                    $body = $res->json();
                    $msg = is_array($body) ? json_encode($body) : $res->body();
                    throw new RuntimeException("OpenAI embeddings failed: HTTP {$res->status()} {$msg}");
                }

                $data = $res->json('data');
                if (!is_array($data)) {
                    throw new RuntimeException("OpenAI embeddings invalid response");
                }

                // Each item has: ['embedding' => [...]]
                $vectors = [];
                foreach ($data as $row) {
                    $vectors[] = $row['embedding'] ?? [];
                }
                return $vectors;

            } catch (Throwable $e) {
                $lastErr = $e;
                // small backoff
                usleep(150000 * $attempt);
            }
        }

        throw new RuntimeException("OpenAI embeddings failed after retries: " . ($lastErr?->getMessage() ?? 'unknown'));
    }
}
