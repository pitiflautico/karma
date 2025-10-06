<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use App\Services\Currency\CurrencyService;
use App\Enums\Setting\CurrencyEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    // Hide from navigation since we'll access it directly
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Company Information')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('company_website')
                                    ->url()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\Textarea::make('company_address')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('company_phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('company_email')
                                    ->email()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Branding')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Company Logo')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('logos')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Financial Settings')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Select::make('default_currency')
                                    ->options(CurrencyEnum::toList())
                                    ->required()
                                    ->default('EUR')
                                    ->live(),
                                Forms\Components\TextInput::make('tax_iva')
                                    ->label('VAT Rate (%)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('tax_irpf')
                                    ->label('IRPF Rate (%)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('goal')
                                    ->label('Monthly Revenue Goal')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->live(onBlur: true)
                                    ->dehydrateStateUsing(fn($state) => $state ? app(CurrencyService::class)->toBigInt((float) $state) : null)
                                    ->formatStateUsing(fn($state) => $state ? app(CurrencyService::class)->fromBigInt((int) $state) : null),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Invoicing')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('vat_number')
                                    ->label('VAT Number')
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('invoice_prefix')
                                    ->label('Invoice Number Prefix')
                                    ->maxLength(255)
                                    ->default('INV-')
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('invoice_sequence')
                                    ->label('Next Invoice Number')
                                    ->numeric()
                                    ->default(1)
                                    ->live(onBlur: true),
                                Forms\Components\Textarea::make('legal_text_invoice')
                                    ->label('Legal Text for Invoices')
                                    ->columnSpanFull(),
                            ])->columns(3),

                    ])
                    ->columnSpanFull(),

                // Hidden fields for relationships
                Forms\Components\Hidden::make('user_id'),
                Forms\Components\Hidden::make('organization_id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'view' => Pages\ViewSetting::route('/{record}'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
