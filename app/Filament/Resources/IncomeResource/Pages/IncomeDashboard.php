<?php
namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Pages\Page;

class IncomeDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $resource = IncomeResource::class;

    protected static string $view = 'filament.resources.income-resource.pages.income-dashboard';

    public function getData(): array
    {
        return IncomeResource::getDashboardData();
    }
}
