<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Services\Currency\CurrencyService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class MainStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3; // 3 stats in a row
    }

    protected function getStats(): array
    {
        $currencyService = app(CurrencyService::class);
        $currentYear = Carbon::now()->year;

        // Total Revenue from all paid invoices
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');

        // Total Clients
        $totalClients = \App\Models\Client::count();

        // Total Invoiced for current year
        $yearlyInvoiced = Invoice::where('status', 'paid')
            ->whereYear('date_issued', $currentYear)
            ->sum('total');

        return [
            Stat::make('Total Revenue', $currencyService->format($totalRevenue, ['symbol' => '€']))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Clients', $totalClients)
                ->description('Active clients in system')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Invoiced (' . $currentYear . ')', $currencyService->format($yearlyInvoiced, ['symbol' => '€']))
                ->description('Revenue this year')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
        ];
    }
}
