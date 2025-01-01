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

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Pendapatan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('payment_id')
                            ->relationship('payment', 'id')
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
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
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
            'dashboard' => Pages\IncomeDashboard::route('/dashboard'),
        ];
    }

    // Method untuk mendapatkan data dashboard
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

    // Event listener untuk payment
    public static function createFromPayment($payment)
    {
        return static::getModel()::create([
            'tenant_id' => $payment->tenant_id,
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'type' => $payment->payment_type,
            'date' => $payment->payment_date,
            'description' => "Pembayaran dari tagihan #{$payment->bill_id}",
        ]);
    }
}