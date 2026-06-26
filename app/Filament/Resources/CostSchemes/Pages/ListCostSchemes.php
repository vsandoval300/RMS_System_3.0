<?php

namespace App\Filament\Resources\CostSchemes\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Resources\CostSchemes\CostSchemeResource;
use App\Exports\CostSchemeExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ListCostSchemes extends ListRecords
{
    protected static string $resource = CostSchemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            // ✅ Nuevo botón Export (respeta filtros/búsqueda/sort del table)
            Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray') // opcional para que no compita con Create
                ->requiresConfirmation() // 👈 activa estilo tipo confirm modal
                ->modalIcon('heroicon-o-information-circle') // 👈 ícono superior
                ->modalIconColor('info') // azul suave
                ->modalHeading('Placement Schemes Report')
                ->modalDescription('This will generate an Excel file with the current filtered Placement Schemes and their related Cost Nodes. Only the records visible under the applied filters will be exported.')
                ->modalSubmitActionLabel('Generate')
                ->modalCancelActionLabel('Cancel')
                ->action(function () {

                    $query = $this->getFilteredTableQuery()
                        ->with([
                            'createdBy:id,name',
                            'costNodexes' => fn ($q) => $q->orderBy('index'),
                            'costNodexes.deduction:id,concept',
                            'costNodexes.partnerSource:id,short_name,name',
                            'costNodexes.partnerDestination:id,short_name,name',
                        ]);

                    $schemes = $query->get();

                    if ($schemes->isEmpty()) {
                        $this->notify('warning', 'No records to export with the current filters.');
                        return;
                    }

                    $flat = collect();

                    foreach ($schemes as $scheme) {
                        $nodes = $scheme->costNodexes ?? collect();

                        if ($nodes->isEmpty()) {
                            $flat->push((object)[
                                'scheme' => $scheme,
                                'node'   => null,
                            ]);
                            continue;
                        }

                        foreach ($nodes as $node) {
                            $flat->push((object)[
                                'scheme' => $scheme,
                                'node'   => $node,
                            ]);
                        }
                    }

                    $filename = 'CostSchemeRep_' . now()->format('Ymd') . '.xlsx';

                    return Excel::download(
                        new CostSchemeExport($flat),
                        $filename
                    );
                }),

            // ✅ Tu botón existente
            CreateAction::make()
                ->label('New Placement Scheme')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->createAnother(false)
                ->modalHeading('New Placement Scheme')
                ->modalSubmitActionLabel('Create')
                ->modalCancelActionLabel('Cancel'),

        ];
    }
}
