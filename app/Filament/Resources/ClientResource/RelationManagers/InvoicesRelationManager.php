<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Services\Currency\CurrencyService;
use App\Enums\Invoice\StatusEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected function modifyQueryUsing($query)
    {
        return $query->withTrashed();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->weight('medium')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('date_issued')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->color(fn ($record) => $record->due_date < now() ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(function ($record) {
                        $currencyService = app(CurrencyService::class);
                        return $currencyService->format($record->total, ['symbol' => '€']);
                    })
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        $statusValue = $state instanceof \App\Enums\Invoice\StatusEnum ? $state->value : $state;
                        return match ($statusValue) {
                            'draft' => 'gray',
                            'sent' => 'warning',
                            'paid' => 'success',
                            'overdue' => 'danger',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusEnum::toList()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_issued', 'desc');
    }
}
