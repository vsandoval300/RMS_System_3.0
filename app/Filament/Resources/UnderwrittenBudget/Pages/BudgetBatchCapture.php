<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Exports\BudgetTemplateExport;
use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use App\Models\Reinsurer;
use App\Models\UnderwrittenBudget as UnderwrittenBudgetModel;
use App\Models\UnderwrittenBudgetItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class BudgetBatchCapture extends Page
{
    use WithFileUploads;

    protected static string $resource = UnderwrittenBudgetResource::class;

    protected string $view = 'filament.resources.underwritten-budget.budget-batch-capture';

    private const MONTHS = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];

    // ── Form state ─────────────────────────────────────────

    public int    $year;
    public string $versionLabel = '';
    public string $notes        = '';

    /** @var array<string, array{name: string, included: bool, m01: string, ..., m12: string, py_budget: float|null}> */
    public array $rows = [];

    /** Temporary uploaded file for import */
    public mixed $importFile = null;

    // ── Lifecycle ──────────────────────────────────────────

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->loadRows();
    }

    public function updatedYear(): void
    {
        $this->loadRows();
    }

    // ── Row helpers ────────────────────────────────────────

    private function loadRows(): void
    {
        $reinsurers = $this->getActiveReinsurers();

        $latestHeader = UnderwrittenBudgetModel::latestVersion($this->year)->first();
        $previous = $latestHeader
            ? UnderwrittenBudgetItem::where('budget_id', $latestHeader->id)
                ->get()
                ->keyBy('reinsurer_id')
            : collect();

        $this->rows = [];

        foreach ($reinsurers as $id => $data) {
            $prev = $previous->get($id);
            $row  = [
                'name'      => $data['name'],
                'cns_code'  => $data['cns_code'],
                'included'  => true,
                'py_budget' => $prev ? (float) $prev->premium_budget : null,
            ];
            foreach (self::MONTHS as $mk) {
                $row[$mk] = $prev ? number_format((float) $prev->$mk, 2, '.', ',') : '0.00';
            }
            $this->rows[(string) $id] = $row;
        }
    }

    private function getActiveReinsurers(): array
    {
        return Reinsurer::whereIn('operative_status_id', [1, 2, 7])
            ->orderBy('id')
            ->get(['id', 'name', 'cns_reinsurer'])
            ->mapWithKeys(fn ($r) => [
                (string) $r->id => [
                    'name'     => $r->name,
                    'cns_code' => $r->cns_reinsurer ?? (string) $r->id,
                ],
            ])
            ->toArray();
    }

    public function nextVersion(): int
    {
        return UnderwrittenBudgetModel::nextVersionForYear($this->year);
    }

    private function parseAmount(string $val): float
    {
        return (float) str_replace(',', '', $val);
    }

    // ── Template download ──────────────────────────────────

    public function downloadTemplate(): mixed
    {
        $filename = "budget_template_{$this->year}_v{$this->nextVersion()}.xlsx";

        return Excel::download(
            new BudgetTemplateExport($this->year, $this->nextVersion(), $this->rows),
            $filename
        );
    }

    // ── File import ────────────────────────────────────────

    public function importFromFile(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'importFile.required' => 'Select a file first.',
            'importFile.mimes'    => 'Only .xlsx, .xls or .csv files are accepted.',
            'importFile.max'      => 'File must be under 5 MB.',
        ]);

        try {
            $path = $this->importFile->getRealPath();

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            // $formatData = false → raw numeric values, avoids comma-formatted
            // strings ("16,827.80") that truncate when cast to float in PHP.
            $sheetData   = $sheet->toArray(null, true, false, false);

            // Row 0 = header, skip it
            $updated  = 0;
            $notFound = 0;

            // Work on a local copy — Livewire won't reliably track
            // mutations to deeply-nested array keys made inside a loop.
            $rows = $this->rows;

            foreach (array_slice($sheetData, 1) as $row) {
                // Col 0 = ID, skip blank/non-numeric rows
                if (empty($row[0]) || ! is_numeric($row[0])) {
                    continue;
                }

                $id = (string) (int) $row[0];

                if (! isset($rows[$id])) {
                    $notFound++;
                    continue;
                }

                // Col 3 = INC flag (D): any non-empty value = included, empty = excluded
                $incVal = $row[3] ?? null;
                $rows[$id]['included'] = $incVal !== null && $incVal !== '' && $incVal !== 0 && $incVal !== '0';

                foreach (self::MONTHS as $i => $mk) {
                    $val = $row[$i + 4] ?? 0;
                    $rows[$id][$mk] = number_format((float) $val, 2, '.', ',');
                }

                $updated++;
            }

            // Reassign all at once so Livewire sends the full updated state
            $this->rows = $rows;

            $this->importFile = null;

            $body = "{$updated} " . ($updated === 1 ? 'reinsurer' : 'reinsurers') . ' updated.';
            if ($notFound > 0) {
                $body .= " {$notFound} rows not matched (ID not found in current list).";
            }

            Notification::make()
                ->success()
                ->title('Import successful')
                ->body($body)
                ->send();

        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Import failed')
                ->body('Could not read file: ' . $e->getMessage())
                ->send();
        }
    }

    // ── Save ───────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'versionLabel' => 'required|string|max:100',
        ]);

        $included = collect($this->rows)->filter(fn ($r) => $r['included']);

        if ($included->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('No reinsurers selected')
                ->body('Check at least one reinsurer before saving.')
                ->send();
            return;
        }

        $version = UnderwrittenBudgetModel::nextVersionForYear($this->year);

        $budget = UnderwrittenBudgetModel::create([
            'year'       => $this->year,
            'version'    => $version,
            'label'      => trim($this->versionLabel),
            'notes'      => $this->notes !== '' ? $this->notes : null,
            'created_by' => Auth::id(),
        ]);

        foreach ($included as $reinsurerId => $row) {
            $monthValues = [];
            foreach (self::MONTHS as $mk) {
                $monthValues[$mk] = $this->parseAmount($row[$mk] ?? '0');
            }
            UnderwrittenBudgetItem::create(array_merge(
                [
                    'budget_id'      => $budget->id,
                    'reinsurer_id'   => (int) $reinsurerId,
                    'premium_budget' => array_sum($monthValues),
                ],
                $monthValues
            ));
        }

        Notification::make()
            ->success()
            ->title("Version {$version} saved")
            ->body("{$included->count()} budget " . ($included->count() === 1 ? 'entry' : 'entries') . " created for {$this->year}.")
            ->send();

        $this->redirect(UnderwrittenBudgetResource::getUrl('index'));
    }

    // ── Page meta ──────────────────────────────────────────

    public function getTitle(): string
    {
        return 'New Budget Version — ' . $this->year;
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        return $user instanceof \App\Models\User && $user->can('create_underwritten::budget');
    }
}
