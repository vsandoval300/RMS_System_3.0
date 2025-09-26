<?php

/* namespace App\Observers;

use App\Models\OperativeDoc;
use App\Services\TransactionLogBuilder;

class OperativeDocObserver
{
    public function saved(OperativeDoc $doc): void
    {
        // Si no hay insureds, schemes o transactions, no hay nada que reconstruir
        if ($doc->insureds()->count() === 0 || 
            $doc->schemes()->count() === 0 || 
            $doc->transactions()->count() === 0) {
            return;
        }

        app(TransactionLogBuilder::class)->rebuildForOperativeDoc($doc);
    }
} */

