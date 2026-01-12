<?php

namespace App\Services\Agent;

use App\Models\Agentemail;
use App\Services\CustomerApiService;
use App\Services\LayoutOpenAIService;

class AgentEmailService
{
    public function __construct(
        protected CustomerApiService $customerApi,
        protected LayoutOpenAIService $openAI,
    ) {}

    /**
     * Create draft email from user command
     */
    public function createDraft(string $command, ?int $userId): string
    {
        $customers = $this->customerApi->getCustomers();

        if (count($customers) === 0) {
            return "ไม่พบข้อมูลผู้รับในระบบ";
        }

        // detect recipient group (VIP, customer, partner...)
        $label = $this->detectRecipientLabel($command);

        // filter by label if specified
       $filtered = $label
    ? array_values(array_filter($customers, function ($c) use ($label) {
        return strtolower($c['note'] ?? '') === strtolower($label)
            || strtolower($c['customer_type'] ?? '') === strtolower($label)
            || strtolower($c['Business_type'] ?? '') === strtolower($label);
    }))
    : $customers;

        if (count($filtered) === 0) {
            return "ไม่พบผู้รับในกลุ่ม {$label}";
        }

        // สร้าง draft ให้ทุกคนที่เจอ keyword
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($filtered as $recipient) {
            // ข้ามถ้าไม่มี email
            if (empty($recipient['email'])) {
                $skippedCount++;
                continue;
            }

            // Ask GPT to draft email
            $draft = $this->generateEmail($command, $recipient);

            // Save draft
            Agentemail::create([
                'user_id' => null,
                'user_command' => $command,
                'recipient_label' => $label,
                'to_email' => $recipient['email'],
                'customer_ref_id' => $recipient['id'] ?? null,
                'subject' => $draft['subject'],
                'body' => $draft['body'],
                'status' => 'draft',
            ]);

            $createdCount++;
        }

        if ($createdCount === 0) {
            return "ไม่สามารถสร้าง Draft ได้ (ข้อมูลผู้รับไม่สมบูรณ์)";
        }

        $message = "สร้าง Draft Email สำเร็จ {$createdCount} ฉบับ";
        if ($label) {
            $message .= " สำหรับกลุ่ม {$label}";
        }
        if ($skippedCount > 0) {
            $message .= " (ข้าม {$skippedCount} รายการ)";
        }

        return $message;
    }

    protected function detectRecipientLabel(string $command): ?string
    {
        if (str_contains(strtolower($command), 'vip')) return 'VIP';
        if (str_contains(strtolower($command), 'partner')) return 'Partner';
        if (str_contains(strtolower($command), 'customer') || str_contains($command, 'ลูกค้า')) return 'Customer';
        return null;
    }

    protected function generateEmail(string $command, array $recipient): array
    {
        $system = <<<SYS
You are AgentEmail.
You draft business emails only.
You must use ONLY the recipient email provided.
Do not guess or invent recipients.
If missing data, write a neutral professional email.
SYS;

        $recipientName = $recipient['name'] ?? '';
        $recipientEmail = $recipient['email'];

        $user = <<<USER
User Command:
{$command}

Recipient:
Name: {$recipientName}
Email: {$recipientEmail}

Write ONE professional business email.
Return JSON:
{
  "subject": "...",
  "body": "..."
}
USER;

        $json = $this->openAI->chat([
            ['role'=>'system','content'=>$system],
            ['role'=>'user','content'=>$user],
        ], ['temperature'=>0.3]);

        $data = json_decode($json, true);

        return [
            'subject' => $data['subject'] ?? 'Business Update',
            'body' => $data['body'] ?? 'Dear Customer,'
        ];
    }
}