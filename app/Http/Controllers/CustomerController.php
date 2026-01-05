<?php

namespace App\Http\Controllers;

use App\Services\CustomerApiService;
use App\Services\CustomerFilterService;
use App\Services\CustomerDocumentService;

class CustomerController extends Controller
{
    public function index(
        CustomerApiService $service,
        CustomerFilterService $filter
    ) {
        $customers = $service->getCustomers();
 // รับ type มาเพื่อส่งกรองข้อมูล
        $customers = $filter->apply(
            $customers,
            request('status'),
            request('type')
        );

        return view('customers', compact('customers'));
    }


    public function show($id, CustomerApiService $service, CustomerDocumentService $documentService)
    {
        $customers = $service->getCustomers();

        $customer = collect($customers)->firstWhere('id', (int)$id);

        if (!$customer) {
            abort(404, 'ไม่พบข้อมูลลูกค้า');
        }

        // ดึงประวัติการติดต่อจาก Database
        $contactHistories = \App\Models\ContactHistory::where('customer_id', $id)
            ->orderBy('contacted_at', 'desc')
            ->get();

        // ดึงเอกสารที่เกี่ยวข้องจาก Database
        $documents = $documentService->getDocuments((int)$id);

        return view('customer_detail', compact('customer', 'contactHistories', 'documents'));
    }

    public function clearCache(CustomerApiService $service)
    {
        $service->clearCache();

        return redirect('/customers')->with('success', 'รีเฟรชข้อมูลสำเร็จ! ดึงข้อมูลใหม่จาก Google Sheets แล้ว');
    }
}
