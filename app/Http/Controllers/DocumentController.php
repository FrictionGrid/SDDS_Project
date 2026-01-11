<?php

namespace App\Http\Controllers;

use App\Models\DocumentAI;
use App\Services\Document\OrchestratorService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = DocumentAI::query()
            ->latest()
            ->get()
            ->map(function ($doc) {
                return [
                    'uuid' => $doc->uuid,
                    'name' => $doc->title ?: $doc->original_name,
                    'size' => $doc->size_bytes,
                    'created_at' => $doc->created_at,
                    'chunks' => $doc->chunks()->count(),
                    'status' => $doc->status,
                ];
            });

        return view('document', [
            'documents' => $documents,
        ]);
    }


    public function upload(Request $request, OrchestratorService $orchestrator)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50MB
        ]);

        $doc = $orchestrator->ingestUpload(
            $request->file('file'),
            title: $request->file('file')->getClientOriginalName(),
            scopeType: null,
            scopeId: null,
            disk: 'local',
            dir: 'documentai'
        );

        return redirect()
            ->route('document.index')
            ->with('success', 'ระบบได้เรียนรู้เอกสารแล้ว');
    }

 
    public function destroy(string $uuid)
    {
        $doc = DocumentAI::findOrFail($uuid);

        // delete vectors
        $doc->chunks()->delete();

        // delete file
        if ($doc->disk && $doc->path) {
            \Storage::disk($doc->disk)->delete($doc->path);
        }

        $doc->delete();

        return back()->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }
}
