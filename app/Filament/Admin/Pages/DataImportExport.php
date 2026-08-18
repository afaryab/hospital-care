<?php

namespace App\Filament\Admin\Pages;

use App\Services\DataPortability\RecordTypeRegistry;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DataImportExport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsUpDown;

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Import / Export';

    protected static ?string $title = 'Data Import / Export';

    protected string $view = 'filament.admin.pages.data-import-export';

    public ?string $recordType = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /** @return array<string, string> */
    public function getRecordTypeOptions(): array
    {
        return RecordTypeRegistry::options();
    }

    public function getSelectedEntry(): ?array
    {
        return $this->recordType ? RecordTypeRegistry::find($this->recordType) : null;
    }

    /**
     * Header actions are cached once per request lifecycle — without this,
     * switching recordType via wire:model.live wouldn't refresh which
     * Import/Export action pair is shown until the next full page load.
     */
    public function updatedRecordType(): void
    {
        $this->cacheInteractsWithHeaderActions();
    }

    protected function getHeaderActions(): array
    {
        $entry = $this->getSelectedEntry();

        if (! $entry) {
            return [];
        }

        return [
            ImportAction::make()
                ->label("Import {$entry['label']}")
                ->importer($entry['importer']),
            ExportAction::make()
                ->label("Export {$entry['label']}")
                ->exporter($entry['exporter']),
        ];
    }
}
