<?php

namespace App\Filament\Resources\CostSchemes\Pages;

use App\Filament\Resources\CostSchemes\CostSchemeResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\CostScheme;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateCostScheme extends CreateRecord
{
    protected static string $resource = CostSchemeResource::class;
    
    /**
     * A dónde redirige el botón “Create”
     */
    protected function getRedirectUrl(): string
    {
        // Vuelve al listado después de guardar
        return static::getResource()::getUrl('index');
    }

    /* public function mount(): void
    {
        parent::mount();

        $today = Carbon::now()->format('Ymd');
        $prefix = "SCHE-$today";

        $countToday = CostScheme::whereDate('created_at', now()->toDateString())->count();
        $nextIndex = $countToday + 1;
        $nextId = "$prefix-" . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);

        $this->form->fill([
            'index' => $nextIndex,
            'id' => $nextId,
        ]);
    } */

    public function mount(): void
    {
        parent::mount();

        // Solo para mostrar valores iniciales en el form (preview),
        // el valor definitivo lo volvemos a calcular al guardar.
        [$nextIndex, $nextId] = $this->computeNextForDate(now());
        $this->form->fill([
            'index' => $nextIndex,
            'id'    => $nextId,
        ]);
    }

    /**
     * Antes de crear, volvemos a calcular por si hubo otra inserción en paralelo.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Bloque corto para evitar condiciones de carrera
        return DB::transaction(function () use ($data) {
            [$nextIndex, $nextId] = $this->computeNextForDate(now());

            $data['index'] = $nextIndex;
            $data['id']    = $nextId;

            // ✅ NUEVO: usuario que crea el registro
            $data['created_by_user'] = Auth::id();

            return $data;
        }, 3);
    }

    /**
     * Calcula el siguiente index e id del día **incluyendo soft-deleted**.
     */
    private function computeNextForDate(Carbon $date): array
    {
        $prefix = 'SCHE-' . $date->format('Ymd');

        // Opción A (más simple): basarse en el campo index
        $maxIndex = (int) (CostScheme::withTrashed()
            ->whereDate('created_at', $date->toDateString())
            ->max('index') ?? 0);

        $nextIndex = $maxIndex + 1;
        $nextId    = sprintf('%s-%04d', $prefix, $nextIndex);

        // --- Opción B alternativa (si no te fías de index): parsear el sufijo del id ---
        // $lastId = CostScheme::withTrashed()
        //     ->where('id', 'like', $prefix.'-%')
        //     ->orderBy('id', 'desc')
        //     ->value('id');
        // $nextIndex = $lastId ? ((int) substr($lastId, -4)) + 1 : 1;
        // $nextId    = sprintf('%s-%04d', $prefix, $nextIndex);

        return [$nextIndex, $nextId];
    }

    protected function getCreatedNotification(): ?Notification
    {
        $id = $this->record?->id ?? '';

        return Notification::make()
            ->success()
            ->title('Placement Scheme created')
            ->body("The new Placement Scheme {$id} has been created successfully.");
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
            ->modalHeading('Create Placement Scheme')
            ->modalDescription('Are you sure you want to create this Placement Scheme?')
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

            Action::make('cancel')
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray')
                ->outlined(),
        ];
    }

}