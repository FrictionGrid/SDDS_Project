<?php

namespace App\Services;

use App\Models\DocumentChunks;
use App\Services\Document\EmbeddingService;

class LayoutSearchService
{
    public function __construct(
        protected EmbeddingService $embeddingService,
    ) {}

    /**
     * @return array<int, array{content:string, score:float}>
     */
    public function search(string $query, int $limit = 5): array
    {
        $queryVector = $this->embeddingService->embed($query);
        if (count($queryVector) === 0) return [];

        $results = [];

        foreach (DocumentChunks::all() as $chunk) {
            $score = $this->cosineSimilarity($queryVector, $chunk->embedding);
            $results[] = [
                'content' => $chunk->content,
                'score' => $score,
            ];
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0; $magA = 0; $magB = 0;

        foreach ($a as $i => $v) {
            $dot += $v * ($b[$i] ?? 0);
            $magA += $v * $v;
            $magB += ($b[$i] ?? 0) * ($b[$i] ?? 0);
        }

        if ($magA == 0 || $magB == 0) return 0;

        return $dot / (sqrt($magA) * sqrt($magB));
    }
}
