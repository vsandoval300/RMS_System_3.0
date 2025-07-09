<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use App\Models\OperativeStatus;
use App\Models\Reinsurer;
use Filament\Actions;
use Filament\Infolists\Components\Tabs\Tab as TabsTab;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReinsurers extends ListRecords
{
    protected static string $resource = ReinsurersResource::class;

    /* ────────────────────────── HEADER ACTIONS ───────────────────────── */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /* ─────────────────────── MAPA «acrónimo → id» ────────────────────── */
    protected array $statusMap = [];              // ex: ['OP'=>2,'DV'=>4…]

    public function mount(): void
    {
        parent::mount();

        // Carga los IDs de la tabla operative_statuses
        $this->statusMap = OperativeStatus::pluck('id', 'acronym')->toArray();
    }

    /* ────────── CONTEOS POR ESTADO (1 sola consulta GROUP BY) ────────── */
    private function getCounts(array $m): array
    {
        return Reinsurer::query()
            ->selectRaw('operative_status_id, COUNT(*) AS total')
            ->groupBy('operative_status_id')
            ->pluck('total', 'operative_status_id')
            ->toArray();
    }

    /* ───────────────────────────── TABS ──────────────────────────────── */
    public function getTabs(): array
    {
        /* 1️⃣  Asegúrate de tener el mapa cargado */
        if (empty($this->statusMap)) {
            $this->statusMap = OperativeStatus::pluck('id', 'acronym')->toArray();
        }

        $m = $this->statusMap;          // alias
        $counts = $this->getCounts($m); // 2️⃣  le pasamos el mapa

        return [
            'all' => Tab::make('All')
                ->badge(array_sum($counts)),

            'operative' => Tab::make('Operative')
                ->badge($counts[$m['OP']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['OP'])
                ),

            'dissolved' => Tab::make('Dissolved')
                ->badge($counts[$m['DV']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['DV'])
                ),

            'run_off' => Tab::make('Run-off')
                ->badge($counts[$m['RO']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['RO'])
                ),

            'transferred' => Tab::make('Transferred')
                ->badge($counts[$m['TR']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['TR'])
                ),
            
            'pending_license' => Tab::make('Pending Lic.')
                ->badge($counts[$m['PL']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['TR'])
                ),
            
            'dormant' => Tab::make('Dormant')
                ->badge($counts[$m['DS']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['TR'])
                ),
            
            'pending_incorp' => Tab::make('Pending Inc.')
                ->badge($counts[$m['PI']] ?? 0)
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->where('operative_status_id', $m['TR'])
                ),    
        ];
    }

    

}
