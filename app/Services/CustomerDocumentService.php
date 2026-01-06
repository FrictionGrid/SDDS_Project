<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerDocumentService
{
//  ดึงเอกสารทั้งหมดของลูกค้าตาม ID //
    public function getDocuments(int $customerId)
    {
        return Document::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
//  อัปโหลดเอกสาร สร้างชื่อเเละเก็บลง DB //
    public function uploadDocument(int $customerId, UploadedFile $file, ?string $uploadedBy = null): Document
    {
    
        $directory = "documents/{$customerId}";

        $fileName = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();

      
        $filePath = $file->storeAs($directory, $fileName);

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
// อัปพร้อมกันได้หลายไฟล์ //
    public function uploadMultipleDocuments(int $customerId, array $files, ?string $uploadedBy = null): array
    {
        $uploadedDocuments = [];

        foreach ($files as $file) {
            $uploadedDocuments[] = $this->uploadDocument($customerId, $file, $uploadedBy);
        }

        return $uploadedDocuments;
    }
// ดาวโหลดเอกสารเข้าเครื่อง
    public function downloadDocument(int $documentId)
    {
        $document = Document::findOrFail($documentId);
// ไม่ให้โหลดไฟล์มั่วเวลาไม่มีใน storage //
        if (!Storage::exists($document->file_path)) {
            throw new \Exception('File not found in storage');
        }
        return Storage::download($document->file_path, $document->file_name);
    }
// ลบเอกสารออกจาก DB เเละ storage //
    public function deleteDocument(int $documentId): bool
    {
        $document = Document::findOrFail($documentId);

  
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

   
        return $document->delete();
    }
// ตรวจสอบชนิดไฟล์ที่อนุญาต //
    public function isAllowedFileType(string $extension): bool
    {
        $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];
        return in_array(strtolower($extension), $allowedTypes);
    }
// ตรวจสอบขนาดไฟล์ //
    public function isValidFileSize(int $sizeInBytes): bool
    {
        $maxSizeInBytes = 10 * 1024 * 1024; 
        return $sizeInBytes <= $maxSizeInBytes;
    }
// เช็คว่ามีใน DB มั้ย //
    public function getDocument(int $documentId): Document
    {
        return Document::findOrFail($documentId);
    }
}
