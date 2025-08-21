<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero


   public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_id', '=', 'businesses.id')
            ->join('reinsurers', 'businesses.reinsurer_id', '=', 'reinsurers.id')
            ->select('transactions.*') // 👈 importante para evitar conflictos de columnas
            ->orderBy('reinsurers.name')
            ->orderBy('transactions.op_document_id');
    }


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Transaction Information')
                 ->description("Overview of the transaction's primary details.")
            ->schema([
                TextInput::make('index')
                    ->required()
                    ->numeric(),
                TextInput::make('proportion')
                    ->required()
                    ->numeric(),
                TextInput::make('exch_rate')
                    ->required()
                    ->numeric(),
                DatePicker::make('due_date'),

                TextInput::make('remmitance_code')
                    ->maxLength(28),
                TextInput::make('op_document_id')
                    ->required()
                    ->maxLength(38),
                Select::make('transaction_type_id')
                    ->label('Type')
                    ->relationship('type', 'description')
                    ->required(),
                Select::make('transaction_status_id')
                    ->label('Status')
                    ->relationship('status', 'transaction_status')
                    ->required(),
            ])
             ->columns(2),
            
        ]);
       
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('operativeDoc.business.reinsurer.name')
                    ->label('Reinsurer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('op_document_id')
                    ->label('Document')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('index')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('id')
                    ->label('Id transaction')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('proportion')
                    ->label('Proportion')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2) . '%' : null)
                    ->sortable(),

                TextColumn::make('exch_rate')
                    ->numeric(decimalPlaces: 5)
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('remmitance_code')
                    ->label('Remittance')
                    ->searchable(),

                TextColumn::make('type.description')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status.transaction_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pending'   => 'gray',
                        'In process'   => 'warning',
                        'Completed'  => 'success',
                        default     => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('operative_docs.business_id') 
            ->defaultSort('transactions.op_document_id')
            ->groups([
                Tables\Grouping\Group::make('operativeDoc.business.reinsurer.name')
                    ->label('Reinsurer'),
            ])


            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
