<?php

namespace App\Filament\Admin\Pages;

use App\Services\Cache\CacheRegistry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Throwable;

class CacheSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Cache Settings';

    protected static ?string $title = 'Cache Settings';

    protected string $view = 'filament.admin.pages.cache-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * @return list<array{key: string, label: string, is_cached: bool, cached_count: int, total_count: int}>
     */
    public function getEntries(): array
    {
        return collect(CacheRegistry::entries())
            ->map(fn (array $entry) => CacheRegistry::status($entry))
            ->all();
    }

    /**
     * @return array{driver: string, connected: bool, models_cached: int, models_total: int, records_cached: int, memory_used: ?string}
     */
    public function getCollectiveStats(): array
    {
        $entries = $this->getEntries();
        $driver = config('cache.default');

        return [
            'driver' => $driver,
            'connected' => $this->isCacheReachable(),
            'models_cached' => collect($entries)->where('is_cached', true)->count(),
            'models_total' => count($entries),
            'records_cached' => collect($entries)->sum('cached_count'),
            'memory_used' => $this->redisMemoryUsed(),
        ];
    }

    protected function isCacheReachable(): bool
    {
        try {
            Cache::put('cache-settings:ping', true, 5);

            return Cache::get('cache-settings:ping') === true;
        } catch (Throwable) {
            return false;
        }
    }

    protected function redisMemoryUsed(): ?string
    {
        if (config('cache.default') !== 'redis') {
            return null;
        }

        try {
            $info = Redis::connection('cache')->info('memory');

            return $info['used_memory_human'] ?? null;
        } catch (Throwable) {
            return null;
        }
    }

    public function clearModelCache(string $key): void
    {
        $entry = CacheRegistry::find($key);

        if (! $entry) {
            return;
        }

        $entry['model']::flushCache();

        Notification::make()
            ->title("{$entry['label']} cache cleared.")
            ->success()
            ->send();
    }

    public function warmModelCache(string $key): void
    {
        $entry = CacheRegistry::find($key);

        if (! $entry) {
            return;
        }

        ($entry['warm'])();

        Notification::make()
            ->title("{$entry['label']} cache warmed.")
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearAllCaches')
                ->label('Clear All Caches')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('This clears every cached model listing below. They will be rebuilt automatically the next time each is queried.')
                ->action(function (): void {
                    foreach (CacheRegistry::entries() as $entry) {
                        $entry['model']::flushCache();
                    }

                    Notification::make()
                        ->title('All caches cleared.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
