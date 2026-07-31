<?php

namespace App\Filament\Resources\Businesses\Pages;

use App\Enums\ApprovalStatus;
use App\Filament\Resources\Businesses\BusinessResource;
use App\Notifications\BusinessReviewDecision;
use App\Notifications\BusinessSubmittedForReview;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;



class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;


   /*  protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */

    /*--------------------------------------------------------------
     | Approval workflow actions
     --------------------------------------------------------------*/
    protected function getHeaderActions(): array
    {
        return [
            // ── Subordinate: submit for review ────────────────────────────
            Action::make('submitForReview')
                ->label('Submit for Review')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Submit for Review')
                ->modalDescription('This will notify your manager to review this business. Are you sure?')
                ->modalSubmitActionLabel('Submit')
                ->authorize(fn () => Gate::allows('submitForReview', $this->record))
                ->visible(fn () =>
                    Auth::id() == $this->record->created_by_user &&
                    in_array($this->record->approval_status, [ApprovalStatus::DRAFT, ApprovalStatus::REJECTED])
                )
                ->action(function () {
                    $business = $this->record;
                    $manager  = $business->createdByUser?->manager;

                    $business->update([
                        'approval_status'            => ApprovalStatus::PENDING,
                        'approval_status_updated_at' => now(),
                    ]);

                    if ($manager) {
                        $manager->notify(new BusinessSubmittedForReview($business, Auth::user()->name));
                    }

                    Notification::make()
                        ->success()
                        ->title('Submitted for review')
                        ->body($manager ? "Your manager {$manager->name} has been notified." : 'Status updated to Pending.')
                        ->send();
                }),

            // ── Manager: approve ─────────────────────────────────────────
            Action::make('approveBusiness')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Business')
                ->modalDescription('Confirm approval of this business. The submitter will be notified.')
                ->modalSubmitActionLabel('Approve')
                ->authorize(fn () => Gate::allows('approveBusiness', $this->record))
                ->visible(fn () =>
                    Auth::id() == $this->record->createdByUser?->manager_id &&
                    $this->record->approval_status === ApprovalStatus::PENDING
                )
                ->action(function () {
                    $business  = $this->record;
                    $submitter = $business->createdByUser;

                    $business->update([
                        'approval_status'            => ApprovalStatus::APPROVED,
                        'approval_status_updated_at' => now(),
                        'reviewed_by_user_id'        => Auth::id(),
                        'revision_notes'             => null,
                    ]);

                    $submitter?->notify(new BusinessReviewDecision($business, 'approved', Auth::user()->name));

                    Notification::make()
                        ->success()
                        ->title('Business approved')
                        ->body('The submitter has been notified.')
                        ->send();
                }),

            // ── Manager: request revision ─────────────────────────────────
            Action::make('requestRevision')
                ->label('Request Revision')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->modalHeading('Request Revision')
                ->modalDescription('Provide your feedback. The submitter will be notified with your notes.')
                ->modalSubmitActionLabel('Send Revision Request')
                ->authorize(fn () => Gate::allows('requestRevision', $this->record))
                ->schema([
                    Textarea::make('revision_notes')
                        ->label('Revision Notes')
                        ->placeholder('Describe the changes required...')
                        ->required()
                        ->rows(4),
                ])
                ->visible(fn () =>
                    Auth::id() == $this->record->createdByUser?->manager_id &&
                    $this->record->approval_status === ApprovalStatus::PENDING
                )
                ->action(function (array $data) {
                    $business  = $this->record;
                    $submitter = $business->createdByUser;

                    $business->update([
                        'approval_status'            => ApprovalStatus::REJECTED,
                        'approval_status_updated_at' => now(),
                        'reviewed_by_user_id'        => Auth::id(),
                        'revision_notes'             => $data['revision_notes'],
                    ]);

                    $submitter?->notify(new BusinessReviewDecision(
                        $business, 'revision', Auth::user()->name, $data['revision_notes']
                    ));

                    Notification::make()
                        ->warning()
                        ->title('Revision requested')
                        ->body('The submitter has been notified.')
                        ->send();
                }),
        ];
    }

    /*--------------------------------------------------------------
     | 1. Ocultar el botón Delete
     --------------------------------------------------------------*/
    public static function canDelete(Model $record): bool
    {
        return false;           // ningún usuario verá “Delete”
    }

    public function getContentTabLabel(): ?string
    {
        return 'Business Details';
    }

    public function getContentTabIcon(): ?string
    {
        // icono del tab principal
        return 'heroicon-o-briefcase';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    /* protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */

    /*--------------------------------------------------------------
     | Dinamic Name 
     --------------------------------------------------------------*/
    public function getTitle(): string
    {
        return 'Edit – ' . ($this->record?->name ?? 'Business');
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
    }

    /**
     * 👉 Personalizamos SOLO el botón "Save"
     *     para que muestre un modal de confirmación.
     */
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->submit(null)
            // mismo label que Filament usa por defecto
            ->requiresConfirmation()
            ->modalHeading('Save Business')
            ->modalDescription('Are you sure you want to save these changes?')  
            ->modalSubmitActionLabel('Save') 
            // qué hacer al confirmar en el modal
            ->action(function () {
                try {
                    $this->save();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->unmountAction();
                    throw $e;
                }
            })
            ->keyBindings(['mod+s']); // ⌘+S / Ctrl+S
    }

    /**
     * 👉 Opcional: personalizar las acciones debajo del formulario
     *     (Save + Cancel).
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }


    /* protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index')), 


            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->outlined()
                ->url(static::getResource()::getUrl('index')),      
        ];
    }*/
    

}
