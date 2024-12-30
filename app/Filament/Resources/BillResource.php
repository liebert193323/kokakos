<?php
namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(Tenant::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $tenant = Tenant::find($state);
                            if ($tenant) {
                                Log::info('Tenant Selected:', [
                                    'id' => $tenant->id,
                                    'name' => $tenant->name,
                                    'per_month' => $tenant->per_month,
                                    'price_semester' => $tenant->price_per_semester,
                                    'price_year' => $tenant->price_per_year
                                ]);

                                $amount = $tenant->per_month ? 
                                    $tenant->price_per_semester : 
                                    $tenant->price_per_year;

                                $payment_category = $tenant->per_month ? 'semester' : 'year';
                                
                                Log::info('Setting values:', [
                                    'amount' => $amount,
                                    'payment_category' => $payment_category
                                ]);

                                $set('amount', $amount);
                                $set('payment_category', $payment_category);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->default(0),

                Forms\Components\Select::make('payment_category')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                    ])
                    ->default('unpaid')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}