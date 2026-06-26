<?php

namespace App\Jobs;

use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NotifyReportReady implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        //public string $path,
        public string $filename
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        Notification::make()
            ->title('Report ready')
            ->body("Your report {$this->filename} is ready.")
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->url(url("/reports/download/{$this->filename}"), true)
                    ->button()
                    ->color('success'),
            ])
            ->success()
            ->sendToDatabase($user);
    }
}
