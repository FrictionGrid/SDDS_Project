<?php

namespace App\Http\Controllers;

use App\Models\ContactHistory;
use App\Models\Project;
use App\Services\CustomerDocumentService;
use Illuminate\Http\Request;

class CustomerDetailController extends Controller
{
// ==================== ประวัติการติดต่อ ====================
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'contact_type' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_date' => 'nullable|date',
            'contact_time' => 'nullable',
            'contacted_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        // รวม contact_date และ contact_time เป็น contacted_at
        if (!empty($validated['contact_date'])) {
            $time = $validated['contact_time'] ?? '00:00';
            $validated['contacted_at'] = $validated['contact_date'] . ' ' . $time;
        }

        unset($validated['contact_date'], $validated['contact_time']);

        ContactHistory::create($validated);

        return redirect()
            ->back()
            ->with('success', 'บันทึกประวัติการติดต่อสำเร็จ');
    }

//   ยังไม่สมบูรณ์ //
    public function updateContact(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'contact_type' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contacted_at' => 'nullable|date',
            'contacted_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $contact = ContactHistory::findOrFail($id);
        $contact->update($validated);

        return redirect()
            ->back()
            ->with('success', 'แก้ไขประวัติการติดต่อสำเร็จ');
    }
// ลบประวัติการติดต่อ
    public function destroyContact($id)
    {
        $contact = ContactHistory::findOrFail($id);
        $contact->delete();

        return redirect()
            ->back()
            ->with('success', 'ลบประวัติการติดต่อสำเร็จ');
    }


    // ==================== เอกสารที่เกี่ยวข้อง (Documents) ====================


    public function uploadDocument(Request $request, CustomerDocumentService $documentService)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:10240', // 10MB
        ]);

        try {
            $uploadedDocuments = $documentService->uploadMultipleDocuments(
                $validated['customer_id'],
                $request->file('files'),
                $request->input('uploaded_by') // อนาคตจะเป็น auth()->user()->name
            );

            return redirect()
                ->back()
                ->with('success', 'อัพโหลดเอกสารสำเร็จ ' . count($uploadedDocuments) . ' ไฟล์');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function downloadDocument($id, CustomerDocumentService $documentService)
    {
        try {
            return $documentService->downloadDocument($id);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถดาวน์โหลดไฟล์ได้: ' . $e->getMessage());
        }
    }

    public function destroyDocument($id, CustomerDocumentService $documentService)
    {
        try {
            $documentService->deleteDocument($id);

            return redirect()
                ->back()
                ->with('success', 'ลบเอกสารสำเร็จ');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบเอกสารได้: ' . $e->getMessage());
        }
    }
}
