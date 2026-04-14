<?php

namespace App\Filament\Admin\Resources\Closings\Pages;

use App\Filament\Admin\Resources\Closings\ClosingResource;
use App\Models\Closing;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClosings extends ListRecords
{
    protected static string $resource = ClosingResource::class;

    public function getTabs(): array
    {
        $counts = Closing::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'CLOSED' => Tab::make('Closed ('.($counts['CLOSED'] ?? 0).')')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'CLOSED')),
            'OPEN' => Tab::make('Open ('.($counts['OPEN'] ?? 0).')')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'OPEN')),
            'REPORTED' => Tab::make('Received ('.($counts['REPORTED'] ?? 0).')')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REPORTED')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'CLOSED';
    }
}
