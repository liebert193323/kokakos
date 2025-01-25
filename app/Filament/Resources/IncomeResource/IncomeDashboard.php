<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action; // Update namespace
use Illuminate\Support\Number;

class IncomeDashboard extends Page
{
    protected static string $resource = IncomeResource::class;

    protected static string $view = 'filament.resources.income-resource.pages.income-dashboard';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(IncomeResource::getUrl())
                ->color('secondary')
        ];
    }

    public function getViewData(): array
    {
        $dashboardData = IncomeResource::getDashboardData();

        return [
            'totalIncome' => Number::currency($dashboardData['totalIncome'], 'IDR'),
            'lastMonthIncome' => Number::currency($dashboardData['lastMonthIncome'], 'IDR'),
            'chartData' => $dashboardData['chartData'],
        ];
    }
}