<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\Page;

class IncomeDashboard extends Page
{
    protected static string $resource = IncomeResource::class;
    
    protected static string $view = 'filament.resources.income.pages.dashboard';
    
    public function getHeading(): string 
    {
        return 'Income Dashboard';
    }

    protected function getViewData(): array
    {
        return [
            'dashboardData' => IncomeResource::getDashboardData(),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Dashboard';
    }

    public static function getNavigationBadge(): ?string
    {
        return 'Income Stats';
    }
}