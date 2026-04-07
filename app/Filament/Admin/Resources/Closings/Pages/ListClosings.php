<?php

namespace App\Filament\Admin\Resources\Closings\Pages;

use App\Filament\Admin\Resources\Closings\ClosingResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
// Removed widget import

class ListClosings extends ListRecords
{
    protected static string $resource = ClosingResource::class;




    public function getTabs(): array
    {
        return [
            Tab::make('CLOSED')
                ->label(__('Closed') . ' (' . \App\Models\Closing::where('status', 'CLOSED')->count() . ')'),
            Tab::make('OPEN')
                ->label(__('Open') . ' (' . \App\Models\Closing::where('status', 'OPEN')->count() . ')'),
            Tab::make('REPORTED')
                ->label(__('Received') . ' (' . \App\Models\Closing::where('status', 'REPORTED')->count() . ')'),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();
        $status = $this->activeTab ?? 'CLOSED';
        return $query->where('status', $status);
    }
}
