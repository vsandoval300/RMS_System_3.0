<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Exports\BudgetVersionExport;
use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use App\Models\UnderwrittenBudget as UnderwrittenBudgetModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewBudgetVersion extends Page
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected string $view = 'filament.resources.underwritten-budget.view-budget-version';

    public UnderwrittenBudgetModel $budget;

    public function mount(string $record): void
    {
        $this->budget = UnderwrittenBudgetModel::with(['items.reinsurer', 'creator'])
            ->findOrFail($record);
    }

    // ── Header actions ─────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('exportExcel')
                    ->label('Excel (.xlsx)')
                    ->icon('heroicon-o-table-cells')
                    ->color('gray')
                    ->action(fn () => $this->exportExcel()),
                Action::make('exportPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->action(fn () => $this->exportPdf()),
            ])
            ->label('Export Report')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->button(),
        ];
    }

    // ── Exports ────────────────────────────────────────────

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = "budget_{$this->budget->year}_v{$this->budget->version}.xlsx";

        return Excel::download(new BudgetVersionExport($this->budget), $filename);
    }

    public function exportPdf(): StreamedResponse
    {
        $html = view('filament.resources.underwritten-budget.budget-pdf', [
            'budget' => $this->budget,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $filename = "budget_{$this->budget->year}_v{$this->budget->version}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    // ── Page meta ──────────────────────────────────────────

    public function getTitle(): string
    {
        return "Budget {$this->budget->year} — v{$this->budget->version}";
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        return $user instanceof \App\Models\User && $user->can('view_underwritten::budget');
    }
}
