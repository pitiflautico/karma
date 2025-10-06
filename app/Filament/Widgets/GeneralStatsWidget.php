<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class GeneralStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Client statistics
        $totalClients = Client::count();
        $clientsWithInvoices = Client::whereHas('invoices')->count();

        // Invoice statistics
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $pendingInvoices = Invoice::where('status', 'sent')->count();

        // Revenue statistics
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');

        return [
            Stat::make('Total Clients', $totalClients)
                ->description($clientsWithInvoices . ' with invoices')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Invoices', $totalInvoices)
                ->description($paidInvoices . ' paid, ' . $pendingInvoices . ' pending')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Revenue', '€' . number_format($totalRevenue / 100, 2))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
