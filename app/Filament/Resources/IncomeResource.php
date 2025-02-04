<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Models\Income;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Filament\Actions\Exports\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Exports\IncomeExporter;
use Filament\Tables\Actions\ExportBulkAction;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Pendapatan';
    protected static ?string $pluralModelLabel = 'Pendapatan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Pengguna')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment.room_number')
                            ->label('Nomor Kamar')
                            ->disabled(),

                        Forms\Components\Select::make('payment_id')
                            ->relationship('payment', 'id')
                            ->disabled(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->disabled()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                            ])
                            ->disabled(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->disabled(),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->disabled()
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment.room_number')
                    ->label('Nomor Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment.id')
                    ->label('ID Pembayaran')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(IncomeExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'view' => Pages\ViewIncome::route('/{record}'),
            'dashboard' => Pages\IncomeDashboard::route('/dashboard'),
        ];
    }

    public static function getDashboardData()
    {
        $query = static::getModel()::query();

        $totalIncome = $query->sum('amount');
        $lastMonthIncome = $query->whereMonth('date', Carbon::now()->month)->sum('amount');

        $chartData = $query
            ->whereDate('date', '>=', Carbon::now()->subDays(30))
            ->orderBy('date')
            ->get()
            ->map(function ($income) {
                return [
                    'date' => $income->date,
                    'amount' => $income->amount,
                ];
            });

        return [
            'totalIncome' => $totalIncome,
            'lastMonthIncome' => $lastMonthIncome,
            'chartData' => $chartData,
        ];
    }

    public static function createFromPayment($payment)
    {
        return static::getModel()::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'type' => $payment->payment_category,
            'date' => $payment->payment_date,
            'description' => "Pembayaran dari tagihan #{$payment->bill_id}",
        ]);
        
    }
}
