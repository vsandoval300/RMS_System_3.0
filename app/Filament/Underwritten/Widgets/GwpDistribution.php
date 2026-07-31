<?php

namespace App\Filament\Underwritten\Widgets;

use Filament\Widgets\Widget;

class GwpDistribution extends Widget
{
    protected string $view = 'filament.widgets.gwp-distribution';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
}
