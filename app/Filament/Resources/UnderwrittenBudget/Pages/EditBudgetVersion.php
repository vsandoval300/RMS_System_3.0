<?php

namespace App\Filament\Resources\UnderwrittenBudget\Pages;

use App\Filament\Resources\UnderwrittenBudget\UnderwrittenBudgetResource;
use App\Models\Reinsurer;
use App\Models\UnderwrittenBudget as UnderwrittenBudgetModel;
use App\Models\UnderwrittenBudgetItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class EditBudgetVersion extends Page
{
    protected static string $resource = UnderwrittenBudgetResource::class;

    protected string $view = 'filament.resources.underwritten-budget.edit-budget-version';

    private const MONTHS = ['m01','m02','m03','m04','m05','m06','m07','m08','m09','m10','m11','m12'];

    // ── State ──────────────────────────────────────────────

    public UnderwrittenBudgetModel $budget;

    public int    $year;
    public int    $version;
    public string $versionLabel = '';
    public string $notes        = '';

    /** @var array<string, array{name: string, included: bool, m01: string, ..., m12: string, py_budget: float|null}> */
    public array $rows = [];

    // ── Lifecycle ──────────────────────────────────────────

    public function mount(string $record): void
    {
        $this->budget = UnderwrittenBudgetModel::with(['items.reinsurer'])->findOrFail($record);

        $this->year         = $this->budget->year;
        $this->version      = $this->budget->version;
        $this->versionLabel = $this->budget->label;
        $this->notes        = $this->budget->notes ?? '';

        $this->loadRows();
    }

    // ── Helpers ────────────────────────────────────────────

    private function loadRows(): void
    {
        $reinsurers = $this->getActiveReinsurers();
        $existing   = $this->budget->items->keyBy('reinsurer_id');

        $this->rows = [];

        // Items already saved in this version
        foreach ($existing as $reinsurerId => $item) {
            $row = [
                'name'      => $item->reinsurer?->name ?? 'Unknown',
                'included'  => true,
                'py_budget' => (float) $item->premium_budget,
            ];
            foreach (self::MONTHS as $mk) {
                $row[$mk] = number_format((float) $item->$mk, 2, '.', '');
            }
            $this->rows[(string) $reinsurerId] = $row;
        }

        // Active reinsurers not yet in this version (unchecked by default)
        foreach ($reinsurers as $id => $name) {
            if (! isset($this->rows[(string) $id])) {
                $row = [
                    'name'      => $name,
                    'included'  => false,
                    'py_budget' => null,
                ];
                foreach (self::MONTHS as $mk) {
                    $row[$mk] = '0.00';
                }
                $this->rows[(string) $id] = $row;
            }
        }

        uksort($this->rows, fn ($a, $b) => (int) $a - (int) $b);
    }

    private function getActiveReinsurers(): array
    {
        return Reinsurer::whereIn('operative_status_id', [1, 2, 7])
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();
    }

    private function parseAmount(string $val): float
    {
        return (float) str_replace(',', '', $val);
    }

    // ── Actions ────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'versionLabel' => 'required|string|max:100',
        ]);

        $this->budget->update([
            'label' => trim($this->versionLabel),
            'notes' => $this->notes !== '' ? $this->notes : null,
        ]);

        foreach ($this->rows as $reinsurerId => $row) {
            if ($row['included']) {
                $monthValues = [];
                foreach (self::MONTHS as $mk) {
                    $monthValues[$mk] = $this->parseAmount($row[$mk] ?? '0');
                }
                UnderwrittenBudgetItem::updateOrCreate(
                    [
                        'budget_id'    => $this->budget->id,
                        'reinsurer_id' => (int) $reinsurerId,
                    ],
                    array_merge(
                        ['premium_budget' => array_sum($monthValues)],
                        $monthValues
                    )
                );
            } else {
                UnderwrittenBudgetItem::where('budget_id', $this->budget->id)
                    ->where('reinsurer_id', (int) $reinsurerId)
                    ->delete();
            }
        }

        Notification::make()
            ->success()
            ->title('Budget version updated')
            ->body("Version {$this->version} for {$this->year} has been saved.")
            ->send();

        $this->redirect(UnderwrittenBudgetResource::getUrl('index'));
    }

    // ── Page meta ──────────────────────────────────────────

    public function getTitle(): string
    {
        return "Edit Budget — {$this->year} v{$this->version}";
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user instanceof \App\Models\User && $user->can('update_underwritten::budget');
    }
}
