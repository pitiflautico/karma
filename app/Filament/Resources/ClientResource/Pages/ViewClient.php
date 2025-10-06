<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Widgets\ClientStatsOverviewWidget;
use App\Services\Currency\CurrencyService;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Client Details')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Overview')
                            ->icon('heroicon-m-eye')
                            ->schema([
                                Infolists\Components\Section::make('Client Information')
                                    ->schema([
                                        Infolists\Components\Grid::make(2)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('company_name')
                                                    ->weight(FontWeight::Bold),
                                                Infolists\Components\TextEntry::make('contact_person'),
                                                Infolists\Components\TextEntry::make('email')
                                                    ->copyable()
                                                    ->icon('heroicon-m-at-symbol'),
                                                Infolists\Components\TextEntry::make('phone')
                                                    ->icon('heroicon-m-phone'),
                                                Infolists\Components\TextEntry::make('client_type')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'individual' => 'info',
                                                        'company' => 'success',
                                                        'nonprofit' => 'warning',
                                                        default => 'gray',
                                                    }),
                                                Infolists\Components\TextEntry::make('industry'),
                                            ]),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Address')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Infolists\Components\Section::make('Address Information')
                                    ->schema([
                                        Infolists\Components\Grid::make(2)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('address')
                                                    ->columnSpanFull(),
                                                Infolists\Components\TextEntry::make('city'),
                                                Infolists\Components\TextEntry::make('state'),
                                                Infolists\Components\TextEntry::make('country'),
                                                Infolists\Components\TextEntry::make('zip_code'),
                                            ]),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Business')
                            ->icon('heroicon-m-building-office')
                            ->schema([
                                Infolists\Components\Section::make('Business Details')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('tax_id'),
                                        Infolists\Components\TextEntry::make('notes')
                                            ->html()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->slideOver(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientStatsOverviewWidget::class,
        ];
    }

    public function getRelationManagers(): array
    {
        $record = $this->getRecord();
        $relations = [];

        if ($record->invoices()->exists()) {
            $relations[] = \App\Filament\Resources\ClientResource\RelationManagers\InvoicesRelationManager::class;
        }

        return $relations;
    }
}
