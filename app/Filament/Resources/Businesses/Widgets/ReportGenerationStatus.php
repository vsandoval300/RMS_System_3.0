<?php

namespace App\Filament\Resources\Businesses\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class ReportGenerationStatus extends Widget
{
    protected string $view = 'filament.resources.business.report-generation-status';
    protected int|string|array $columnSpan = 'full';

    public function getGeneratingReportLabel(): ?string
    {
        return Cache::get('report_generating_' . auth()->id());
    }
}
