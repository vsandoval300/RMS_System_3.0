<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReinsurerBankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerBankAccounts';
    protected static ?string $title       = 'Bank Accounts';

    /* ─────────────────────────  FORM  ───────────────────────── */
    public function form(Form $form): Form
    {
        return $form->schema([
            /* pivote: bank_account_id -------------------------------- */
            Forms\Components\Select::make('bank_account_id')
                ->label('Bank account')
                ->relationship(
                    name: 'bankAccount',
                    titleAttribute: 'beneficiary_acct_name',
                    modifyQueryUsing: fn (Builder $q) =>
                        $q->orderBy('beneficiary_acct_name')
                )
                ->getOptionLabelFromRecordUsing(
                    fn ($r) => $r->beneficiary_acct_name ?: '—'
                )
                ->searchable()
                ->preload()
                ->createOptionForm($this->bankAccountForm())
                ->required(),
        ]);
    }

    /* sub-form para crear BankAccount al vuelo */
    protected function bankAccountForm(): array
    {
        return [
            Forms\Components\TextInput::make('beneficiary_acct_name')->required(),
            Forms\Components\TextInput::make('beneficiary_address'),
            Forms\Components\TextInput::make('beneficiary_swift')->label('SWIFT'),
            Forms\Components\TextInput::make('beneficiary_acct_no')->label('Account #'),
            Forms\Components\TextInput::make('ffc_acct_name')->label('FFC name'),
            Forms\Components\TextInput::make('ffc_acct_no')->label('FFC #'),
            Forms\Components\TextInput::make('ffc_acct_address'),
            Forms\Components\TextInput::make('status_account')->label('Status'),

            /* ----- relaciones ----- */
            Forms\Components\Select::make('currency_id')
                ->relationship('currency', 'acronym')
                ->getOptionLabelFromRecordUsing(fn ($r) => $r->acronym ?: '—')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('bank_id')
                ->relationship('bank', 'name')
                ->getOptionLabelFromRecordUsing(fn ($r) => $r->name ?: '—')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('intermediary_bank')
                ->relationship('bank_inter', 'name')
                ->getOptionLabelFromRecordUsing(fn ($r) => $r->name ?: '—')
                ->label('Intermediary Bank')
                ->searchable()
                ->preload(),
        ];
    }

    /* ──────────────────────────  TABLE  ───────────────────────── */
    public function table(Table $table): Table
    {
        return $table
            ->persistSortInSession(false)     // evita re-usar un sort inválido
            ->persistFiltersInSession(false)  // idem filtros
            ->defaultSort('id', 'asc')        // sort seguro
            ->columns([
                Tables\Columns\TextColumn::make('beneficiary')
                    ->label('Beneficiary')
                    ->state(fn ($record) =>
                        $record->bankAccount?->beneficiary_acct_name ?? '—'
                    ),

                Tables\Columns\TextColumn::make('swift')
                    ->label('SWIFT')
                    ->state(fn ($record) =>
                        $record->bankAccount?->beneficiary_swift ?? '—'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('acct')
                    ->label('Account #')
                    ->state(fn ($record) =>
                        $record->bankAccount?->beneficiary_acct_no ?? '—'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cur')
                    ->label('Cur.')
                    ->state(fn ($record) =>
                        $record->bankAccount?->currency?->acronym ?? '—'
                    ),

                Tables\Columns\TextColumn::make('bank')
                    ->label('Bank')
                    ->state(fn ($record) =>
                        $record->bankAccount?->bank?->name ?? '—'
                    )
                    ->limit(25),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(fn ($record) =>
                        $record->bankAccount?->status_account ?? '—'
                    )
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'Active' ? 'success' : 'secondary'
                    )
                    ->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New reinsurer bank account'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
