<?php

namespace App\Services\Filament;

class FilamentThemeService
{
    public static function getCustomStyles(): string
    {
        return '
            <style>
                .fi-sidebar {
                
                }
            </style>
        ';
    }
}