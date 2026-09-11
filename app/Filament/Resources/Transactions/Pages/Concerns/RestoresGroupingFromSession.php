<?php

namespace App\Filament\Resources\Transactions\Pages\Concerns;

/**
 * Restores the table's grouping selection from the session on mount, since
 * Filament has no native session-persistence hook for grouping (unlike
 * filters/search/sort). Implemented as a Livewire trait hook (mountX) rather
 * than overriding the page's own mount() method, so it doesn't interfere
 * with InteractsWithTable's own mount/booted lifecycle.
 */
trait RestoresGroupingFromSession
{
    protected string $groupingSessionKey = 'transactions_table_grouping';

    public function mountRestoresGroupingFromSession(): void
    {
        if (blank($this->tableGrouping) && session()->has($this->groupingSessionKey)) {
            $this->tableGrouping = session($this->groupingSessionKey);
        }
    }

    public function updatedTableGrouping(): void
    {
        session([$this->groupingSessionKey => $this->tableGrouping]);
    }
}
