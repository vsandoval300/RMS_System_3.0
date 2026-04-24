<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;

class RefreshBusinessLifecycleStatuses extends Command
{
    protected $signature = 'businesses:refresh-lifecycle-statuses';

    protected $description = 'Refresh business lifecycle statuses based on operative documents';

    public function handle(): int
    {
        Business::query()
            ->with('operativeDocs:id,business_code,index,operative_doc_type_id,inception_date,expiration_date')
            ->chunk(100, function ($businesses) {

                foreach ($businesses as $business) {
                    $business->refreshLifecycleStatus();
                }

            });

        $this->info('Business lifecycle statuses refreshed successfully.');

        return self::SUCCESS;
    }
}