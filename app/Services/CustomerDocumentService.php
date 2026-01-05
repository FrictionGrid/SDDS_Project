<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerDocumentService
{
    /**
     * Get all documents for a customer
     */
    public function getDocuments(int $customerId)
    {
        return Document::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Upload a single document
     */
    public function uploadDocument(int $customerId, UploadedFile $file, ?string $uploadedBy = null): Document
    {
        // Create directory path
        $directory = "documents/{$customerId}";

        // Generate unique filename
        $fileName = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store file
        $filePath = $file->storeAs($directory, $fileName);

        // Save to database
        return Document::create([
            'customer_id' => $customerId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * Upload multiple documents at once
     */
    public function uploadMultipleDocuments(int $customerId, array $files, ?string $uploadedBy = null): array
    {
        $uploadedDocuments = [];

        foreach ($files as $file) {
            $uploadedDocuments[] = $this->uploadDocument($customerId, $file, $uploadedBy);
        }

        return $uploadedDocuments;
    }

    /**
     * Download a document
     */
    public function downloadDocument(int $documentId)
    {
        $document = Document::findOrFail($documentId);

        if (!Storage::exists($document->file_path)) {
            throw new \Exception('File not found in storage');
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    /**
     * Delete a document
     */
    public function deleteDocument(int $documentId): bool
    {
        $document = Document::findOrFail($documentId);

        // Delete file from storage
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        // Delete record from database
        return $document->delete();
    }

    /**
     * Check if file type is allowed
     */
    public function isAllowedFileType(string $extension): bool
    {
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($extension), $allowedTypes);
    }

    /**
     * Validate file size (max 10 MB)
     */
    public function isValidFileSize(int $sizeInBytes): bool
    {
        $maxSizeInBytes = 10 * 1024 * 1024; // 10 MB
        return $sizeInBytes <= $maxSizeInBytes;
    }

    /**
     * Get a single document by ID
     */
    public function getDocument(int $documentId): Document
    {
        return Document::findOrFail($documentId);
    }
}
