<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\Reinsurer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    /* protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Create')
                ->action('create')      // 👈 ejecuta el método create() de la página (submit real)
                ->color('primary'),
            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
        
    } */

    



    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $businessCode = $this->record?->business_code ?? '';

        return Notification::make()
            ->success()
            ->title('Business created')
            ->body("The new Business {$businessCode} has been created successfully.");
    }


    /**
     * 👉 Personalizamos SOLO el botón "Create"
     *     para que muestre un modal de confirmación.
     */
    protected function getCreateFormAction(): Action
    {
 
 
    return Action::make('create')
            // label por defecto de Filament
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->requiresConfirmation()
            ->modalHeading('Create Business')
            ->modalDescription('Are you sure you want to create this Business?')
            ->modalSubmitActionLabel('Create')
            // qué hacer cuando el usuario confirma en el modal
            ->action(fn () => $this->create())
            ->keyBindings(['mod+s']); // ⌘+S / Ctrl+S
    }

   
    protected function getFormActions(): array
    {
        return [
            // ⬅️ aquí USAMOS el botón definido arriba
            $this->getCreateFormAction(),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Usuario que crea el registro
        $data['created_by_user'] = Auth::id();

        // Generar business_code automáticamente antes de guardar
        if (! empty($data['reinsurer_id'])) {

            $generatedCode = $this->generateBusinessCode((int) $data['reinsurer_id']);

            if ($generatedCode) {
                $data['business_code'] = $generatedCode;
            }
        }

        return $data;
    }


    protected function generateBusinessCode(?int $reinsurerId): ?string
    {
        if (! $reinsurerId) {
            return null;
        }

        $reinsurer = Reinsurer::find($reinsurerId);

        if (! $reinsurer) {
            return null;
        }

        $year = Carbon::now()->format('Y');
        $acronym = Str::upper($reinsurer->acronym);
        $number = str_pad($reinsurer->cns_reinsurer ?? $reinsurer->id, 3, '0', STR_PAD_LEFT);

        $prefix = "{$year}-{$acronym}{$number}";

        $lastBusiness = Business::query()
            ->withTrashed()
            ->where('business_code', 'like', "$prefix-%")
            ->orderByDesc('business_code')
            ->first();

        $lastNumber = 0;

        if ($lastBusiness && preg_match('/-(\d{3})$/', $lastBusiness->business_code, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $consecutive = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$consecutive}";
    }







}
