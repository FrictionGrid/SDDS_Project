<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CustomerApiService
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.customer_api.url');
    }

    public function getCustomers(): array
    {
        $response = Http::timeout(10)->get($this->apiUrl);

        if ($response->failed()) {
            return [];
        }

        return $response->json();
    }
}
