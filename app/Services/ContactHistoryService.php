<?php

namespace App\Services;

use App\Models\ContactHistory;
use Illuminate\Http\Request;

class ContactHistoryService
{
// เเปลงเพื่อมาเก็บใน DB เพราะเวลากับวันที่รวมกันใน database //
    public function storeContact(array $data): ContactHistory
    {
        if (!empty($data['contact_date'])) {
            $time = $data['contact_time'] ?? '00:00';
            $data['contacted_at'] = $data['contact_date'] . ' ' . $time;
        }

        unset($data['contact_date'], $data['contact_time']);

        return ContactHistory::create($data);
    }


    public function updateContact(int $contactId, array $data): ContactHistory
    {
        $contact = ContactHistory::findOrFail($contactId);
        $contact->update($data);

        return $contact;
    }


    public function deleteContact(int $contactId): bool
    {
        $contact = ContactHistory::findOrFail($contactId);
        return $contact->delete();
    }

    public function getContactHistories(int $customerId)
    {
        return ContactHistory::where('customer_id', $customerId)
            ->orderBy('contacted_at', 'desc')
            ->get();
    }


    public function getValidationRules(bool $isUpdate = false): array
    {
        return [
            'customer_id' => 'nullable|integer',
            'contact_type' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_date' => $isUpdate ? 'nullable' : 'nullable|date',
            'contact_time' => 'nullable',
            'contacted_at' => $isUpdate ? 'nullable|date' : 'nullable',
            'contacted_by' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ];
    }
}
