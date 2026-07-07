<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use App\Models\Reinsurer;
use App\Models\UnderwrittenBudget as UnderwrittenBudgetModel;
use App\Models\UnderwrittenBudgetItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class BudgetBatchCapture extends Page
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected string $view = 'filament.resources.underwritten-budget.budget-batch-capture';

    private const MONTHS = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];

    // ── Form state ─────────────────────────────────────────

    public int    $year;
    public string $versionLabel = '';
    public string $notes        = '';

    /** @var array<string, array{name: string, included: bool, m01: string, ..., m12: string, py_budget: float|null}> */
    public array $rows = [];

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

    // ── Helpers ────────────────────────────────────────────

    private function loadRows(): void
    {
        $reinsurers = $this->getActiveReinsurers();

        // Load items from the latest existing version for this year (as starting point)
        $latestHeader = UnderwrittenBudgetModel::latestVersion($this->year)->first();
        $previous = $latestHeader
            ? UnderwrittenBudgetItem::where('budget_id', $latestHeader->id)
                ->get()
                ->keyBy('reinsurer_id')
            : collect();

        $this->rows = [];

        foreach ($reinsurers as $id => $name) {
            $prev = $previous->get($id);
            $row  = [
                'name'      => $name,
                'included'  => true,
                'py_budget' => $prev ? (float) $prev->premium_budget : null,
            ];
            foreach (self::MONTHS as $mk) {
                $row[$mk] = $prev ? number_format((float) $prev->$mk, 2, '.', '') : '0.00';
            }
            $this->rows[(string) $id] = $row;
        }
    }

    private function getActiveReinsurers(): array
    {
        return Reinsurer::whereIn('operative_status_id', [1, 2, 7])
            ->orderBy('id')
            ->pluck('name', 'id')
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

    // ── Actions ────────────────────────────────────────────

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
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
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
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user instanceof \App\Models\User && $user->can('create_underwritten::budget');
    }
}
