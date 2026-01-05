<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CustomerApiService
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.customer_api.url');
    }

    //   เก็บ catch ทำให้ระบบเร็วขึ้น
    public function getCustomers(): array
    {
        // เก็บ Cache ไว้กี่นาที
        return Cache::remember('customers.all', 300, function () {
            return $this->fetchCustomersFromApi();
        });
    }
    //    ล้าง Cache ของข้อมูลลูกค้า เผื่อเข้าพร้อมกัน กดรีไม่ใช้ cach เดิม 
// ล้างอย่างเดียว
    public function clearCache(): void
    {
        Cache::forget('customers.all');
    }
// ล้างเเล้วดึงข้อมูลใหม่
    public function refreshCache(): array
    {
        $this->clearCache();
        return $this->getCustomers();
    }

    protected function fetchCustomersFromApi(): array
    {
        try {
            $url = $this->apiUrl . '?action=getCustomers';
// เช็คสำหรับ API ล่ม 
            \Log::info('Calling Customer API', [
                'url' => $url
            ]);

            $response = Http::timeout(10)
                ->withOptions(['allow_redirects' => true])
                ->get($url);

            \Log::info('Customer API Response', [
                'status' => $response->status(),
                'body_length' => strlen($response->body())
            ]);

            if ($response->failed()) {
                \Log::warning('Customer API failed', [
                    'status' => $response->status(),
                    'url' => $this->apiUrl
                ]);
                return [];
            }
// เช็คว่าเป็น array 
            $data = $response->json();

            \Log::info('Customer API Data', [
                'count' => is_array($data) ? count($data) : 0,
                'data_type' => gettype($data),
                'sample' => is_array($data) && count($data) > 0 ? $data[0] : null
            ]);


            if (!is_array($data)) {
                \Log::warning('Customer API returned non-array data', [
                    'data_type' => gettype($data)
                ]);
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            \Log::error('Customer API exception', [
                'message' => $e->getMessage(),
                'url' => $this->apiUrl
            ]);
            return [];
        }
    }
}
