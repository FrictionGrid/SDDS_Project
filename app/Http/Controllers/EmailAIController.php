<?php

namespace App\Http\Controllers;

use App\Models\Agentemail;
use Illuminate\Http\Request;

class EmailAIController extends Controller
{
    public function index()
    {
        // ดึง draft emails ทั้งหมด เรียงล่าสุดก่อน
        $drafts = Agentemail::query()
            ->where('status', 'draft')
            ->latest()
            ->get()
            ->map(function ($draft) {
                return [
                    'id' => $draft->id,
                    'to_email' => $draft->to_email,
                    'subject' => $draft->subject,
                    'body' => $draft->body,
                    'recipient_label' => $draft->recipient_label,
                    'created_at' => $draft->created_at,
                    'time_ago' => $this->getTimeAgo($draft->created_at),
                ];
            });

        return view('email_ai', [
            'drafts' => $drafts,
        ]);
    }

    public function show($id)
    {
        $draft = Agentemail::findOrFail($id);

        return response()->json([
            'id' => $draft->id,
            'to_email' => $draft->to_email,
            'subject' => $draft->subject,
            'body' => $draft->body,
            'recipient_label' => $draft->recipient_label,
            'user_command' => $draft->user_command,
            'created_at' => $draft->created_at->format('d M Y H:i'),
        ]);
    }

    public function destroy($id)
    {
        $draft = Agentemail::findOrFail($id);
        $draft->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบ Draft สำเร็จ',
        ]);
    }

    private function getTimeAgo($datetime)
    {
        $now = now();
        $diff = $datetime->diff($now);

        if ($diff->d > 0) {
            return $diff->d . 'd ago';
        } elseif ($diff->h > 0) {
            return $diff->h . 'h ago';
        } elseif ($diff->i > 0) {
            return $diff->i . 'm ago';
        } else {
            return 'just now';
        }
    }
}
