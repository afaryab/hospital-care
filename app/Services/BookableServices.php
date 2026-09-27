<?php

namespace App\Services;

use App\Models\Service;

class BookableServices
{
    /**
     * Services a member of the public may request, grouped by department.
     *
     * @return array<int, array{department: string, services: array<int, array{id: int, name: string}>}>
     */
    public static function grouped(): array
    {
        return Service::query()
            ->with('department:id,name')
            ->where('generate_service_order', true)
            ->where('is_composit_service', false)
            ->orderBy('name')
            ->get(['id', 'name', 'service_department_id'])
            ->groupBy(fn (Service $service): string => $service->department?->name ?? 'Other')
            ->sortKeys()
            ->map(fn ($services, string $department): array => [
                'department' => $department,
                'services' => $services->map(fn (Service $service): array => ['id' => $service->id, 'name' => $service->name])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
