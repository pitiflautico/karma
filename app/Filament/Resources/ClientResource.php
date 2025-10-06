<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Services\Currency\CurrencyService;
use App\Enums\Client\TypeEnum;
use App\Enums\Client\IndustryEnum;
use App\Enums\Client\CountryEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Business';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->withTrashed();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Client Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Contact Info')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('contact_person')
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Address')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('state')
                                    ->maxLength(255),
                                Forms\Components\Select::make('country')
                                    ->searchable()
                                    ->options(CountryEnum::toList()),
                                Forms\Components\TextInput::make('zip_code')
                                    ->maxLength(255),
                                Forms\Components\Select::make('currency')
                                    ->options([
                                        'EUR' => 'Euro (€)',
                                        'USD' => 'US Dollar ($)',
                                        'GBP' => 'British Pound (£)',
                                    ])
                                    ->default('EUR')
                                    ->required(),
                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('Business Details')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('tax_id')
                                    ->maxLength(255),
                                Forms\Components\Select::make('industry')
                                    ->options(IndustryEnum::toList())
                                    ->searchable(),
                                RadioDeck::make('client_type')
                                    ->options(TypeEnum::toList())
                                    ->descriptions([
                                        'individual' => 'Personal client',
                                        'company' => 'Business entity',
                                        'nonprofit' => 'Non-profit organization',
                                    ])
                                    ->icons([
                                        'individual' => 'heroicon-o-user',
                                        'company' => 'heroicon-o-building-office',
                                        'nonprofit' => 'heroicon-o-heart',
                                    ])
                                    ->required()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->color('primary'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Notes')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Forms\Components\RichEditor::make('notes')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-at-symbol')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_invoiced')
                    ->getStateUsing(function ($record) {
                        $currencyService = app(CurrencyService::class);
                        $total = $record->invoices()->sum('total');
                        return $currencyService->format($total, ['symbol' => '€']);
                    })
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Deleted')
                    ->placeholder('Active')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->options(CountryEnum::toList())
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('industry')
                    ->options(IndustryEnum::toList())
                    ->multiple(),
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('created_at')
                    ->label('Created Date Range'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
