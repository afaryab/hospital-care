<?php

namespace App\Services\Filament;

class FilamentThemeService
{
    public static function getCustomStyles(): string
    {
        return '
            <style>
                .fi-sidebar {
                    background-color: #1c398e !important;
                    border-color: rgba(28, 57, 142, 0.3) !important;
                }
                .fi-sidebar-nav {
                    background-color: #1c398e !important;
                }
                .fi-sidebar .fi-sidebar-item-label,
                .fi-sidebar .fi-sidebar-group-label,
                .fi-sidebar .fi-sidebar-item-button {
                    color: #06df72 !important; /* Green color for sidebar items */
                }
                /* More specific selectors for hover and active states */
                .fi-sidebar .fi-sidebar-item-button:hover,
                .fi-sidebar .fi-sidebar-item-button:focus,
                .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label,
                .fi-sidebar .fi-sidebar-item-button:focus .fi-sidebar-item-label {
                    background-color: #06df72 !important; /* Green background on hover */
                    color: #1c398e !important; /* Blue text on hover */
                }
                .fi-sidebar .fi-sidebar-item-button[aria-current="page"],
                .fi-sidebar .fi-sidebar-item-button.fi-active,
                .fi-sidebar .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-label,
                .fi-sidebar .fi-sidebar-item-button.fi-active .fi-sidebar-item-label {
                    background-color: #06df72 !important; /* Green background on active */
                    color: #1c398e !important; /* Blue text on active */
                }
                /* Logo styling - make it green */
                .fi-logo,
                .fi-logo svg,
                .fi-sidebar-header .fi-logo,
                .fi-sidebar-header .fi-logo svg,
                .fi-topbar .fi-logo,
                .fi-topbar .fi-logo svg {
                    color: #06df72 !important;
                    fill: #06df72 !important;
                }
                .fi-topbar {
                    background-color: #1c398e !important;
                    border-color: rgba(28, 57, 142, 0.3) !important;
                }
                .fi-topbar .fi-topbar-item,
                .fi-topbar .fi-breadcrumbs-item,
                .fi-topbar .fi-user-menu-trigger {
                    color: white !important;
                }
            </style>
        ';
    }

    public static function getBrandColors(): array
    {
        return [
            'primary' => [
                50 => '#f0fdf4',
                100 => '#dcfce7',
                200 => '#bbf7d0', 
                300 => '#86efac',
                400 => '#4ade80',
                500 => '#06df72',  // Your green color
                600 => '#05c565',
                700 => '#04a555',
                800 => '#048046',
                900 => '#065f46',
                950 => '#022c22',
            ],
        ];
    }

    public static function getPrimaryColor(): string
    {
        return '#06df72';
    }

    public static function getSecondaryColor(): string
    {
        return '#1c398e';
    }
}