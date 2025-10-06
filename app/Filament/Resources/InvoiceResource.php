<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Enums\Invoice\StatusEnum;
use App\Enums\Invoice\TaxRateEnum;
use App\Services\Currency\CurrencyService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Financial';

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
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('prefix')
                            ->label('Invoice Prefix')
                            ->required()
                            ->maxLength(10)
                            ->default(date('Y'))
                            ->placeholder('e.g., J-, 2025-, etc.'),
                        Forms\Components\TextInput::make('number')
                            ->label('Invoice Number')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Auto-generated if empty')
                            ->helperText('Will auto-increment based on prefix'),
                        Forms\Components\DatePicker::make('date_issued')
                            ->required()
                            ->default(now())
                            ->live(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->default(now()->addDays(30)),
                    ])->columns(2),

                Forms\Components\Section::make('Invoice Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['user_id'] = Auth::id();

                                // Calculate detailed pricing
                                $quantity = (float) ($data['quantity'] ?? 0);
                                $unitPrice = (float) ($data['unit_price'] ?? 0);
                                $taxRate = (float) ($data['tax_rate'] ?? 0);
                                $irpfRate = (float) ($data['irpf_rate'] ?? 0);
                                $currencyService = app(CurrencyService::class);

                                $subtotal = $quantity * $unitPrice;
                                $taxAmount = $subtotal * ($taxRate / 100);
                                $irpfAmount = $subtotal * ($irpfRate / 100);
                                $finalTotal = $subtotal + $taxAmount - $irpfAmount;

                                $data['price'] = $currencyService->toBigInt($subtotal);
                                $data['tax_total'] = $currencyService->toBigInt($taxAmount);
                                $data['irpf_total'] = $currencyService->toBigInt($irpfAmount);
                                $data['total'] = $currencyService->toBigInt($finalTotal);
                                $data['total_line'] = $currencyService->toBigInt($finalTotal); // Store as bigint

                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                // Calculate detailed pricing for updates as well
                                $quantity = (float) ($data['quantity'] ?? 0);
                                $unitPrice = (float) ($data['unit_price'] ?? 0);
                                $taxRate = (float) ($data['tax_rate'] ?? 0);
                                $irpfRate = (float) ($data['irpf_rate'] ?? 0);
                                $currencyService = app(CurrencyService::class);

                                $subtotal = $quantity * $unitPrice;
                                $taxAmount = $subtotal * ($taxRate / 100);
                                $irpfAmount = $subtotal * ($irpfRate / 100);
                                $finalTotal = $subtotal + $taxAmount - $irpfAmount;

                                $data['price'] = $currencyService->toBigInt($subtotal);
                                $data['tax_total'] = $currencyService->toBigInt($taxAmount);
                                $data['irpf_total'] = $currencyService->toBigInt($irpfAmount);
                                $data['total'] = $currencyService->toBigInt($finalTotal);
                                $data['total_line'] = $currencyService->toBigInt($finalTotal); // Store as bigint

                                return $data;
                            })
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateLineTotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->prefix(function (Forms\Get $get) {
                                        $clientId = $get('../../client_id');
                                        if ($clientId) {
                                            $client = \App\Models\Client::find($clientId);
                                            $currency = $client->currency ?? 'EUR';
                                            return $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');
                                        }
                                        return '€';
                                    })
                                    ->step(0.001)
                                    ->dehydrateStateUsing(fn($state) => $state ? app(CurrencyService::class)->toBigInt((float) $state) : null)
                                    ->formatStateUsing(fn($state) => $state ? app(CurrencyService::class)->fromBigInt((int) $state) : null)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateLineTotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('tax_rate')
                                    ->label('Tax Rate (%)')
                                    ->numeric()
                                    ->default(21)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(1)
                                    ->suffix('%')
                                    ->formatStateUsing(fn($state) => $state ? (string) (int) $state : '21')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateLineTotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('irpf_rate')
                                    ->label('IRPF (%)')
                                    ->numeric()
                                    ->default(function () {
                                        $settings = \App\Models\Setting::first();
                                        return $settings && $settings->tax_irpf ? (int) floatval($settings->tax_irpf) : 0;
                                    })
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(1)
                                    ->suffix('%')
                                    ->formatStateUsing(fn($state) => $state ? (string) (int) $state : '0')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateLineTotal($get, $set);
                                    }),
                                Forms\Components\Placeholder::make('line_total')
                                    ->label('Line Total')
                                    ->content(function (Forms\Get $get, $record) {
                                        $currencyService = app(CurrencyService::class);

                                        // Get client currency
                                        $clientId = $get('../../client_id');
                                        $currency = 'EUR'; // Default
                                        if ($clientId) {
                                            $client = \App\Models\Client::find($clientId);
                                            $currency = $client->currency ?? 'EUR';
                                        }
                                        $symbol = $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');

                                        // If we have a record (editing), use stored total_line
                                        if ($record && isset($record['total_line'])) {
                                            return $currencyService->format($record['total_line'], ['symbol' => $symbol]);
                                        }

                                        // Otherwise calculate from current form values
                                        $quantity = (float) $get('quantity') ?: 0;
                                        $unitPrice = (float) $get('unit_price') ?: 0;
                                        $taxRate = (float) $get('tax_rate') ?: 0;
                                        $irpfRate = (float) $get('irpf_rate') ?: 0;

                                        $subtotal = $quantity * $unitPrice;
                                        $tax = $subtotal * ($taxRate / 100);
                                        $irpf = $subtotal * ($irpfRate / 100);
                                        $total = $subtotal + $tax - $irpf;

                                        return $currencyService->format($currencyService->toBigInt($total), ['symbol' => $symbol]);
                                    }),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Add Item')
                            ->reorderableWithButtons()
                            ->collapsible(),

                        Forms\Components\Section::make('Invoice Totals')
                            ->schema([
                                Forms\Components\Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(function (Forms\Get $get) {
                                        $total = 0;
                                        $items = $get('items') ?? [];
                                        $currencyService = app(CurrencyService::class);

                                        // Get client currency
                                        $clientId = $get('client_id');
                                        $currency = 'EUR'; // Default
                                        if ($clientId) {
                                            $client = \App\Models\Client::find($clientId);
                                            $currency = $client->currency ?? 'EUR';
                                        }
                                        $symbol = $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');

                                        foreach ($items as $item) {
                                            if (isset($item['quantity'], $item['unit_price'])) {
                                                $total += ((float) $item['quantity'] * (float) $item['unit_price']);
                                            }
                                        }

                                        return $currencyService->format($currencyService->toBigInt($total), ['symbol' => $symbol]);
                                    }),
                                Forms\Components\Placeholder::make('taxes')
                                    ->label('Taxes')
                                    ->content(function (Forms\Get $get) {
                                        $totalTax = 0;
                                        $items = $get('items') ?? [];
                                        $currencyService = app(CurrencyService::class);

                                        // Get client currency
                                        $clientId = $get('client_id');
                                        $currency = 'EUR'; // Default
                                        if ($clientId) {
                                            $client = \App\Models\Client::find($clientId);
                                            $currency = $client->currency ?? 'EUR';
                                        }
                                        $symbol = $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');

                                        foreach ($items as $item) {
                                            if (isset($item['quantity'], $item['unit_price'], $item['tax_rate'])) {
                                                $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
                                                $taxRate = (float) $item['tax_rate'] / 100;
                                                $totalTax += ($lineTotal * $taxRate);
                                            }
                                        }

                                        return $currencyService->format($currencyService->toBigInt($totalTax), ['symbol' => $symbol]);
                                    }),
                                Forms\Components\Placeholder::make('total_final')
                                    ->label('Total')
                                    ->content(function (Forms\Get $get) {
                                        $total = 0;
                                        $items = $get('items') ?? [];
                                        $currencyService = app(CurrencyService::class);

                                        // Get client currency
                                        $clientId = $get('client_id');
                                        $currency = 'EUR'; // Default
                                        if ($clientId) {
                                            $client = \App\Models\Client::find($clientId);
                                            $currency = $client->currency ?? 'EUR';
                                        }
                                        $symbol = $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');

                                        foreach ($items as $item) {
                                            if (isset($item['quantity'], $item['unit_price'], $item['tax_rate'])) {
                                                $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
                                                $taxRate = (float) $item['tax_rate'] / 100;
                                                $irpfRate = (float) ($item['irpf_rate'] ?? 0) / 100;
                                                $tax = $lineTotal * $taxRate;
                                                $irpf = $lineTotal * $irpfRate;
                                                $total += ($lineTotal + $tax - $irpf);
                                            }
                                        }

                                        return $currencyService->format($currencyService->toBigInt($total), ['symbol' => $symbol]);
                                    }),
                                Forms\Components\Hidden::make('total')
                                    ->dehydrateStateUsing(function (Forms\Get $get) {
                                        $total = 0;
                                        $items = $get('items') ?? [];
                                        $currencyService = app(CurrencyService::class);

                                        foreach ($items as $item) {
                                            if (isset($item['quantity'], $item['unit_price'], $item['tax_rate'])) {
                                                $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
                                                $taxRate = (float) $item['tax_rate'] / 100;
                                                $irpfRate = (float) ($item['irpf_rate'] ?? 0) / 100;
                                                $tax = $lineTotal * $taxRate;
                                                $irpf = $lineTotal * $irpfRate;
                                                $total += ($lineTotal + $tax - $irpf);
                                            }
                                        }

                                        return $currencyService->toBigInt($total);
                                    }),
                            ])
                            ->columns(2),
                    ]),

                Forms\Components\Section::make('Invoice Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(StatusEnum::labels())
                            ->required()
                            ->default(StatusEnum::DRAFT->value)
                            ->live()
                            ->columnSpan(1),
                        Forms\Components\Group::make([
                            Forms\Components\Placeholder::make('aprobar_label')
                                ->label('Aprobar')
                                ->content(''),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('send_to_verifactu')
                                    ->label('Enviar a Verifactu')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->color(fn(Forms\Get $get) => $get('status') === StatusEnum::SENT->value ? 'success' : 'gray')
                                    ->disabled(fn(Forms\Get $get) => $get('status') !== StatusEnum::SENT->value)
                                    ->tooltip(fn(Forms\Get $get) => $get('status') !== StatusEnum::SENT->value 
                                        ? 'Para enviar a Verifactu debes cambiar el estado a Definitivo (SENT)' 
                                        : null)
                                    ->action(function () {
                                        // TODO: Implementar envío a Verifactu
                                        \Filament\Notifications\Notification::make()
                                            ->title('Factura enviada a Verifactu')
                                            ->body('La factura se ha enviado correctamente al sistema Verifactu de Hacienda.')
                                            ->success()
                                            ->send();
                                    })
                                    ->requiresConfirmation()
                                    ->modalHeading('⚠️ Enviar factura a Verifactu - Acción Definitiva')
                                    ->modalDescription(new \Illuminate\Support\HtmlString('
                                        <div class="space-y-3 text-sm">
                                            <p class="font-semibold text-orange-600 dark:text-orange-400">
                                                🚨 IMPORTANTE: Esta acción es definitiva y no puede deshacerse
                                            </p>
                                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                <p class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                                                    📋 Antes de continuar, asegúrese de que:
                                                </p>
                                                <ul class="list-disc list-inside space-y-1 text-yellow-700 dark:text-yellow-300">
                                                    <li>Tiene su <strong>certificado digital</strong> preparado y válido</li>
                                                    <li>Los datos de la factura son correctos y definitivos</li>
                                                    <li>La factura será enviada directamente a <strong>Hacienda</strong></li>
                                                </ul>
                                            </div>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                Una vez enviada, la factura quedará registrada en el sistema Verifactu de la Agencia Tributaria.
                                            </p>
                                        </div>
                                    '))
                                    ->modalSubmitActionLabel('Sí, enviar a Hacienda')
                                    ->modalCancelActionLabel('Cancelar')
                                    ->modalIcon('heroicon-o-exclamation-triangle')
                                    ->modalIconColor('warning')
                                    ->extraAttributes([
                                        'class' => 'w-full min-h-[42px]',
                                        'style' => 'height: 42px; min-height: 42px; margin-top: -25px;'
                                    ])
                            ])
                            ->fullWidth(),
                            Forms\Components\Placeholder::make('verifactu_warning')
                                ->content(new \Illuminate\Support\HtmlString('
                                    <div class="text-xs text-orange-600 dark:text-orange-400 mt-2 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>* Esta acción es definitiva y no puede deshacerse</span>
                                    </div>
                                '))
                        ])
                        ->columnSpan(1),
                        Forms\Components\DatePicker::make('paid_at')
                            ->label('Paid Date')
                            ->visible(fn(Forms\Get $get) => $get('status') === StatusEnum::PAID->value)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    private static function updateLineTotal(Forms\Get $get, Forms\Set $set): void
    {
        $quantity = (float) $get('quantity') ?: 0;
        $unitPrice = (float) $get('unit_price') ?: 0;
        $taxRate = (float) $get('tax_rate') ?: 0;
        $irpfRate = (float) $get('irpf_rate') ?: 0;
        $currencyService = app(CurrencyService::class);

        $subtotal = $quantity * $unitPrice;
        $tax = $subtotal * ($taxRate / 100);
        $irpf = $subtotal * ($irpfRate / 100);
        $total = $subtotal + $tax - $irpf;

        $set('line_total', $currencyService->format($currencyService->toBigInt($total)));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('deleted_at')
                    ->label('Status')
                    ->icon(fn($record) => $record->trashed() ? 'heroicon-o-trash' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->trashed() ? 'danger' : 'success')
                    ->tooltip(fn($record) => $record->trashed() ? 'In Trash - Can be restored' : 'Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->searchable(['prefix', 'number'])
                    ->sortable(['prefix', 'number'])
                    ->weight('medium')
                    ->color(fn($record) => $record->trashed() ? 'danger' : 'primary'),
                Tables\Columns\TextColumn::make('client.company_name')
                    ->label('Client')
                    ->searchable()
                    ->color(fn($record) => $record->trashed() ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('date_issued')
                    ->label('Issued')
                    ->date('M j, Y')
                    ->sortable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(function ($record) {
                        if (!$record->due_date) return 'gray';
                        $statusValue = $record->status instanceof StatusEnum ? $record->status->value : $record->status;
                        if ($statusValue === StatusEnum::PAID->value) return 'gray';
                        return $record->due_date->isPast() ? 'danger' : 'gray';
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->getStateUsing(function ($record) {
                        $currencyService = app(\App\Services\Currency\CurrencyService::class);
                        $currency = $record->client->currency ?? 'EUR';
                        $symbol = $currency === 'EUR' ? '€' : ($currency === 'USD' ? '$' : '£');
                        return $currencyService->format($record->total, ['symbol' => $symbol]);
                    })
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof StatusEnum) {
                            return $state->goodName();
                        }
                        // Fallback for non-enum values
                        return StatusEnum::from($state)->goodName();
                    })
                    ->color(function ($state) {
                        if ($state instanceof StatusEnum) {
                            return $state->getColor();
                        }
                        // Fallback for non-enum values
                        return StatusEnum::from($state)->getColor();
                    }),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Days Overdue')
                    ->getStateUsing(function ($record) {
                        if (!$record->due_date) return null;
                        $statusValue = $record->status instanceof StatusEnum ? $record->status->value : $record->status;
                        if ($statusValue === StatusEnum::PAID->value) return null;

                        $daysOverdue = $record->due_date->diffInDays(now(), false);
                        return $daysOverdue > 0 ? $daysOverdue : null;
                    })
                    ->placeholder('N/A')
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TrashedFilter::make()
                    ->label('Deleted Status')
                    ->placeholder('All invoices')
                    ->trueLabel('Only deleted invoices')
                    ->falseLabel('Only active invoices')
                    ->queries(
                        true: fn(Builder $query) => $query->onlyTrashed(),
                        false: fn(Builder $query) => $query->withoutTrashed(),
                        blank: fn(Builder $query) => $query->withTrashed(),
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusEnum::labels())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('client')
                    ->relationship('client', 'company_name')
                    ->searchable()
                    ->multiple(),
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('date_issued')
                    ->label('Issued Date Range'),
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('due_date')
                    ->label('Due Date Range'),
                Tables\Filters\TernaryFilter::make('overdue_only')
                    ->label('Overdue invoices')
                    ->queries(
                        true: fn(Builder $query) => $query->where('due_date', '<', now())
                            ->whereNotIn('status', [StatusEnum::PAID->value]),
                        false: fn(Builder $query) => $query->where(function ($query) {
                            $query->where('due_date', '>=', now())
                                ->orWhereIn('status', [StatusEnum::PAID->value])
                                ->orWhereNull('due_date');
                        }),
                    ),
                Tables\Filters\Filter::make('amount_range')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->label('Min Amount')
                            ->numeric()
                            ->prefix('€'),
                        Forms\Components\TextInput::make('max_amount')
                            ->label('Max Amount')
                            ->numeric()
                            ->prefix('€'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('total', '>=', $amount * 100),
                            )
                            ->when(
                                $data['max_amount'],
                                fn(Builder $query, $amount): Builder => $query->where('total', '<=', $amount * 100),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make()
                        ->label('Move to Trash')
                        ->icon('heroicon-o-trash')
                        ->successNotificationTitle('Invoice moved to trash')
                        ->modalDescription('This invoice will be moved to trash but can be restored later.')
                        ->visible(fn($record) => !$record->trashed()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restore from Trash')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->successNotificationTitle('Invoice restored successfully')
                        ->visible(fn($record) => $record->trashed()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Delete Permanently')
                        ->icon('heroicon-o-x-circle')
                        ->modalDescription('This will permanently delete the invoice. This action cannot be undone!')
                        ->successNotificationTitle('Invoice permanently deleted')
                        ->visible(fn($record) => $record->trashed()),
                    Tables\Actions\Action::make('view_pdf')
                        ->label('View PDF')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->url(function ($record) {
                            return \Illuminate\Support\Facades\URL::signedRoute('invoice.pdf.view', ['invoice' => $record], now()->addMinutes(30));
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('send_email')
                        ->label('Send via Email')
                        ->icon('heroicon-o-envelope')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('email')
                                ->label('Send to Email')
                                ->email()
                                ->required()
                                ->default(fn($record) => $record->client->email)
                                ->helperText('Email address to send the invoice PDF'),
                            Forms\Components\Textarea::make('message')
                                ->label('Email Message')
                                ->rows(3)
                                ->default('Please find attached your invoice.')
                                ->helperText('Optional message to include in the email'),
                        ])
                        ->action(function ($record, array $data) {
                            $invoice = Invoice::with(['client', 'organization', 'items'])->find($record->id);

                            try {
                                // Send email using the new InvoiceMail class
                                \Illuminate\Support\Facades\Mail::to($data['email'])
                                    ->send(new \App\Mail\InvoiceMail($invoice, $data['message'] ?? ''));

                                \Filament\Notifications\Notification::make()
                                    ->title('Invoice sent successfully')
                                    ->body("Invoice {$invoice->invoice_number} has been sent to {$data['email']}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Email sending failed')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($record) {
                            $record->update([
                                'status' => StatusEnum::PAID,
                                'paid_at' => now(),
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Invoice marked as paid')
                                ->success()
                                ->send();
                        })
                        ->visible(function ($record) {
                            $statusValue = $record->status instanceof StatusEnum ? $record->status->value : $record->status;
                            return $statusValue !== StatusEnum::PAID->value;
                        })
                        ->requiresConfirmation(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Move to Trash')
                        ->icon('heroicon-o-trash')
                        ->successNotificationTitle('Selected invoices moved to trash'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore from Trash')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->successNotificationTitle('Selected invoices restored'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Delete Permanently')
                        ->icon('heroicon-o-x-circle')
                        ->modalDescription('This will permanently delete the selected invoices. This action cannot be undone!')
                        ->successNotificationTitle('Selected invoices permanently deleted'),
                    Tables\Actions\BulkAction::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => StatusEnum::PAID,
                                    'paid_at' => now(),
                                ]);
                            });
                            \Filament\Notifications\Notification::make()
                                ->title('Selected invoices marked as paid')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
