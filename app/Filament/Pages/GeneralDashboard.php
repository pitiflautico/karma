<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class GeneralDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.general-dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static string $routePath = '/';

    public function getWidgets(): array
    {
        return [
            // Main stats
            \App\Filament\Widgets\MainStatsWidget::class,

            // General stats
            \App\Filament\Widgets\GeneralStatsWidget::class,

            // Client stats
            \App\Filament\Widgets\ClientStatsOverviewWidget::class,

            // Stats overview
            \App\Filament\Widgets\StatsOverview::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
