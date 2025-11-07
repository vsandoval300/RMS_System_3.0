<?php

namespace App\Filament\Resources\ReinsurersResource\Pages;

use App\Filament\Resources\ReinsurersResource;
use App\Models\OperativeStatus;
use App\Models\Reinsurer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListReinsurers extends ListRecords
{
    protected static string $resource = ReinsurersResource::class;

    /* ────────────────────────── HEADER ACTIONS ───────────────────────── */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Reinsurer')          // 👈 el texto que tú quieras
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Reinsurer')   // título del modal
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),
        ];
    }

    /* ─────────────────────── MAPA «acrónimo → id» ────────────────────── */
    /* protected array $statusMap = [];              // ex: ['OP'=>2,'DV'=>4…]

    public function mount(): void
    {
        parent::mount();

        // Carga los IDs de la tabla operative_statuses
        $this->statusMap = OperativeStatus::pluck('id', 'acronym')->toArray();
    } */

    /* ────────── CONTEOS POR ESTADO (1 sola consulta GROUP BY) ────────── */
    /* private function getCounts(array $m): array
    {
        return Reinsurer::query()
            ->selectRaw('operative_status_id, COUNT(*) AS total')
            ->groupBy('operative_status_id')
            ->pluck('total', 'operative_status_id')
            ->toArray();
    } */

    public function getTabs(): array
    {
        /* $statusMap = OperativeStatus::pluck('id', 'acronym')->toArray();

        return [
            null => Tab::make('All'),
            'operative' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['OP'] ?? 0)),
            'dissolved' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['DV'] ?? 0)),
            'run_off' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['RO'] ?? 0)),
            'transferred' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['TR'] ?? 0)),
            'pending_license' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['PL'] ?? 0)),
            'dormant' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['DS'] ?? 0)),
            'pending_incorp' => Tab::make()->query(fn ($query) => $query->where('operative_status_id', $statusMap['PI'] ?? 0)),
        ]; */
        // Mapa: acrónimo → nombre legible
            $names = [
                'OP' => 'Operative',
                'DV' => 'Dissolved',
                'RO' => 'Run off',
                'TR' => 'Transferred',
                'PL' => 'Pending license',
                'DM' => 'Dormant',
                'PI' => 'Pending incorp',
            ];

            // ID por acrónimo
            $statusMap = OperativeStatus::pluck('id', 'acronym')->toArray();

            // Conteo por ID
            $counts = Reinsurer::query()
                ->selectRaw('operative_status_id, COUNT(*) AS total')
                ->groupBy('operative_status_id')
                ->pluck('total', 'operative_status_id')
                ->toArray();

            // Armar tabs
            $tabs = [
                null => Tab::make('All (' . Reinsurer::count() . ')'),
            ];

            foreach ($names as $acronym => $label) {
                $id = $statusMap[$acronym] ?? null;
                if ($id) {
                    $count = $counts[$id] ?? 0;
                    $tabs[$acronym] = Tab::make("{$acronym} - {$label} ({$count})")
                        ->query(fn ($query) => $query->where('operative_status_id', $id));
                }
            }

            return $tabs;
    }


}

     /* ───────────────────────────── TABS ──────────────────────────────── */
    /* public function getTabs(): array
    {
        /* 1️⃣  Asegúrate de tener el mapa cargado */
       /*  if (empty($this->statusMap)) {
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
        ]; */ 
    







