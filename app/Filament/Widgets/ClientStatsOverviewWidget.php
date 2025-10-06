<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Services\Currency\CurrencyService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsOverviewWidget extends BaseWidget
{
    public ?Client $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $currencyService = app(CurrencyService::class);

        // Revenue calculations
        $totalRevenue = $this->record->invoices()->sum('total');
        $paidRevenue = $this->record->invoices()->where('status', 'paid')->sum('total');
        $pendingRevenue = $this->record->invoices()->whereIn('status', ['sent', 'draft'])->sum('total');

        // Invoice statistics
        $totalInvoices = $this->record->invoices()->count();
        $paidInvoices = $this->record->invoices()->where('status', 'paid')->count();

        return [
            Stat::make('Total Revenue', $currencyService->format($totalRevenue, ['symbol' => '€']))
                ->description('All-time revenue from this client')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Paid Revenue', $currencyService->format($paidRevenue, ['symbol' => '€']))
                ->description('Revenue from paid invoices')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending Revenue', $currencyService->format($pendingRevenue, ['symbol' => '€']))
                ->description('Revenue from unpaid invoices')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Invoices', $totalInvoices)
                ->description($paidInvoices . ' paid invoices')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Average Ticket', $totalInvoices > 0 ? $currencyService->format($totalRevenue / $totalInvoices, ['symbol' => '€']) : '€0')
                ->description('Average invoice amount')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray'),
        ];
    }
}
