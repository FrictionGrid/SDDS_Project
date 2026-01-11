<?php

namespace App\Services\Document;

use App\Models\DocumentAI;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OrchestratorService
{
    public function __construct(
        private readonly ExactFileService $exactFileService,
        private readonly ChunkingService $chunkingService,
        private readonly EmbeddingService $embeddingService,
        private readonly SaveStoreService $saveStoreService,
    ) {}

    /**
     * Upload file -> create DocumentAI -> ingest -> return DocumentAI
     */
    public function ingestUpload(
        UploadedFile $file,
        ?string $title = null,
        ?string $scopeType = null,
        ?int $scopeId = null,
        string $disk = 'local',
        string $dir = 'documents'
    ): DocumentAI {
        // 1) Save file
        $storedPath = $file->store($dir, $disk);

        // 2) Create document registry
        $doc = new DocumentAI();
        $doc->title = $title ?: $file->getClientOriginalName();
        $doc->source_type = 'file';
        $doc->original_name = $file->getClientOriginalName();
        $doc->mime_type = $file->getMimeType();
        $doc->disk = $disk;
        $doc->path = $storedPath;
        $doc->size_bytes = (int) $file->getSize();
        $doc->status = 'uploaded';
        $doc->scope_type = $scopeType;
        $doc->scope_id = $scopeId;
        $doc->metadata = [
            'uploaded_ext' => $file->getClientOriginalExtension(),
        ];
        $doc->save();

        // 3) Ingest
        $this->ingestDocument($doc);

        return $doc->fresh();
    }

    /**
     * Ingest existing DocumentAI (must have disk/path)
     */
    public function ingestDocument(DocumentAI $doc): void
    {
        $doc->status = 'processing';
        $doc->error_message = null;
        $doc->save();

        try {
            // A) Extract text
            $text = $this->exactFileService->extractFromStorage(
                (string) $doc->disk,
                (string) $doc->path,
                $doc->mime_type
            );

            if (trim($text) === '') {
                throw new \RuntimeException("Extracted text is empty");
            }

            // B) Chunking
            $chunks = $this->chunkingService->makeChunks($text);

            if (count($chunks) === 0) {
                throw new \RuntimeException("No chunks produced");
            }

            // C) Embedding batch
            $chunkTexts = array_map(fn ($c) => (string) $c['content'], $chunks);
            $vectors = $this->embeddingService->embedBatch($chunkTexts);

            if (count($vectors) !== count($chunks)) {
                throw new \RuntimeException("Embedding vectors mismatch");
            }

            // D) Save to store
            $this->saveStoreService->save($doc, $chunks, $vectors);

            // E) Done
            $doc->status = 'indexed';
            $doc->save();

        } catch (Throwable $e) {
            Log::error('Document ingest failed', [
                'document_uuid' => $doc->uuid,
                'error' => $e->getMessage(),
            ]);

            $doc->status = 'failed';
            $doc->error_message = $e->getMessage();
            $doc->save();

            // rethrow if you want controller to catch
            throw $e;
        }
    }

    /**
     * Optional: remove stored file too (if you want cleanup endpoint later)
     */
    public function deleteDocumentFile(DocumentAI $doc): void
    {
        if ($doc->disk && $doc->path && Storage::disk($doc->disk)->exists($doc->path)) {
            Storage::disk($doc->disk)->delete($doc->path);
        }
    }
}
