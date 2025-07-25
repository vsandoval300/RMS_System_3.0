<?php

namespace App\Filament\Resources\CostSchemeResource\Pages;

use App\Filament\Resources\CostSchemeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use App\Models\CostScheme;

class CreateCostScheme extends CreateRecord
{
    protected static string $resource = CostSchemeResource::class;

    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();

        $today = Carbon::now()->format('Ymd');
        $prefix = "SCHE-$today";

        $countToday = CostScheme::whereDate('created_at', now()->toDateString())->count();
        $nextIndex = $countToday + 1;
        $nextId = "$prefix-" . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);

        $this->form->fill([
            'index' => $nextIndex,
            'id' => $nextId,
        ]);
    }
}