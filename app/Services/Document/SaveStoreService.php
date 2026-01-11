<?php

namespace App\Services\Document;

use App\Models\DocumentAI;
use App\Models\DocumentChunks;
use Illuminate\Support\Facades\DB;

class SaveStoreService
{
    /**
     * @param array<int, array{chunk_index:int, content:string}> $chunks
     * @param array<int, array> $vectors
     */
    public function save(DocumentAI $doc, array $chunks, array $vectors): void
    {
        if (count($chunks) !== count($vectors)) {
            throw new \RuntimeException("Chunks count != vectors count");
        }

        DB::transaction(function () use ($doc, $chunks, $vectors) {
            // Re-index safe: remove old
            DocumentChunks::query()->where('document_id', $doc->uuid)->delete();

            foreach ($chunks as $i => $chunk) {
                DocumentChunks::create([
                    'document_id' => $doc->uuid,
                    'chunk_index' => (int) $chunk['chunk_index'],
                    'content' => (string) $chunk['content'],
                    'embedding' => $vectors[$i],
                    'metadata' => [
                        'source' => $doc->source_type ?? 'file',
                        'path' => $doc->path,
                        'mime_type' => $doc->mime_type,
                        'title' => $doc->title,
                    ],
                ]);
            }
        });
    }
}
