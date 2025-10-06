<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Services\Currency\CurrencyService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currencyService = app(CurrencyService::class);

        return [
            Stat::make('Total Revenue', $currencyService->format(Invoice::where('status', 'paid')->sum('total'), ['symbol' => '€']))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
