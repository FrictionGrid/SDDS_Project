<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CustomerFilterService
{
    public function apply(
        array $customers,
        ?string $status,
        ?string $type
    ): array {
        return collect($customers)
            ->when($status && $status !== 'all', function (Collection $c) use ($status) {
                return $c->where('latest_status', $status);
            })
            ->when($type && $type !== 'all', function (Collection $c) use ($type) {
                return $c->where('customer_type', $type);
            })
            // จัด Array ใหม่ 
            ->values()
            ->all();
    }
}
