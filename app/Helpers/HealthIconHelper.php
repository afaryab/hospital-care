<?php

namespace App\Helpers;

class HealthIconHelper
{
    /**
     * Returns all health icon names (without .svg extension).
     * Each name corresponds to a blade component: <x-healthicons-{name} />
     * and a Filament icon string: healthicons-{name}
     */
    public static function all(): array
    {
        $svgDir = base_path('vendor/troccoli/blade-health-icons/resources/svg');

        if (! is_dir($svgDir)) {
            return [];
        }

        return collect(scandir($svgDir))
            ->filter(fn ($file) => str_ends_with($file, '.svg'))
            ->map(fn ($file) => str_replace('.svg', '', $file))
            ->values()
            ->all();
    }

    /**
     * Returns options array for Filament Select components.
     * Key = icon name (e.g. "o-doctor"), Value = human-readable label.
     */
    public static function options(): array
    {
        return collect(static::all())
            ->mapWithKeys(function (string $name) {
                $label = ucwords(str_replace(['-', '_'], ' ', substr($name, 2)));

                return [$name => $label];
            })
            ->all();
    }

    /**
     * Returns the public URL for a given icon name.
     * Icons are published to public/vendor/blade-health-icons/
     */
    public static function url(string $name): string
    {
        return asset("vendor/blade-health-icons/{$name}.svg");
    }

    /**
     * Returns an HTML <img> tag for the icon (for Filament Placeholder preview).
     */
    public static function img(string $name, string $class = 'w-8 h-8'): string
    {
        $url = static::url($name);

        return "<img src=\"{$url}\" alt=\"{$name}\" class=\"{$class}\" style=\"display:inline-block;\" />";
    }
}
