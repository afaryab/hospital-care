<?php

namespace App\Filament\Components\UI;

use Illuminate\View\Component;

class Tabs extends Component
{
    /**
     * @var array<string, array{label: string, count?: int, active?: bool}>
     */
    public array $tabs;
    public string $active;

    /**
     * @param array<string, array{label: string, count?: int, active?: bool}> $tabs
     * @param string $active
     */
    public function __construct(array $tabs, string $active)
    {
        $this->tabs = $tabs;
        $this->active = $active;
    }

    public function render()
    {
        return view('filament.components.ui.tabs');
    }
}
