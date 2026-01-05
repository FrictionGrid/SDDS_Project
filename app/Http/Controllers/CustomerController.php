<?php

namespace App\Http\Controllers;

use App\Services\CustomerApiService;

class CustomerController extends Controller
{
    public function index(CustomerApiService $service)
    {
        $customers = $service->getCustomers();

        // รับค่าจาก query parameters
        $statusFilter = request('status');
        $typeFilter = request('type');

        // กรองตามสถานะล่าสุด
        if ($statusFilter && $statusFilter !== 'all') {
            $customers = array_filter($customers, function($customer) use ($statusFilter) {
                $status = $customer['latest_status'] ?? '';
                return $status === $statusFilter;
            });
        }

        // กรองตามประเภทลูกค้า
        if ($typeFilter && $typeFilter !== 'all') {
            $customers = array_filter($customers, function($customer) use ($typeFilter) {
                $type = $customer['customer_type'] ?? '';
                return $type === $typeFilter;
            });
        }

        // รีเซ็ต array keys หลังจากกรอง
        $customers = array_values($customers);

        return view('customers', compact('customers'));
    }

    public function show($id, CustomerApiService $service)
    {
        $customers = $service->getCustomers();

        // หาลูกค้าจาก index (0-based)
        $customer = $customers[$id] ?? null;

        if (!$customer) {
            abort(404, 'ไม่พบข้อมูลลูกค้า');
        }

        return view('customer_detail', compact('customer'));
    }
}
