<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $modelLabel = 'Kamar';
    protected static ?string $pluralModelLabel = 'Kamar';
    protected static ?string $navigationLabel = 'Kamar';

    /**
     * Form untuk Create/Edit Room.
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Field Nomor Kamar (hanya muncul saat create)
                Forms\Components\TextInput::make('number')
                    ->label('Nomor Kamar')
                    ->required()
                    ->maxLength(255)
                    ->visible(fn ($context) => $context === 'create'),

                // Field lainnya hanya muncul saat edit
                Forms\Components\Select::make('tenant_id')
                    ->label('Penyewa')
                    ->options(Tenant::all()->pluck('name', 'id'))
                    ->nullable()
                    ->reactive()
                    ->visible(fn ($context) => $context === 'edit')
                    ->afterStateUpdated(function (callable $set, $state) {
                        $tenant = Tenant::find($state);
                        if ($tenant) {
                            $startDate = now();
                            $endDate = $tenant->per_month == 1
                                ? $startDate->copy()->addMonths(6)
                                : $startDate->copy()->addYear();

                            $set('payment_category', $tenant->per_month == 1 ? 'semester' : 'year');
                            $set('price_per_semester', $tenant->price_per_semester);
                            $set('price_per_year', $tenant->price_per_year);
                            $set('rent_start_date', $startDate->toDateString());
                            $set('rent_end_date', $endDate->toDateString());
                            $set('status', 'occupied');
                        } else {
                            $set('payment_category', null);
                            $set('price_per_semester', null);
                            $set('price_per_year', null);
                            $set('rent_start_date', null);
                            $set('rent_end_date', null);
                            $set('status', 'available');
                        }
                    }),

                Forms\Components\TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->required()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                    ])
                    ->default('available')
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\Select::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\TextInput::make('price_per_semester')
                    ->label('Harga per Semester')
                    ->numeric()
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\TextInput::make('price_per_year')
                    ->label('Harga per Tahun')
                    ->numeric()
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\DatePicker::make('rent_start_date')
                    ->label('Tanggal Mulai Sewa')
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),

                Forms\Components\DatePicker::make('rent_end_date')
                    ->label('Tanggal Berakhir Sewa')
                    ->disabled()
                    ->visible(fn ($context) => $context === 'edit'),
            ]);
    }

    /**
     * Tabel untuk menampilkan daftar Room.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('status', 'desc') // Menampilkan kamar "occupied" di atas
            ->defaultGroup('status') // Grup berdasarkan status
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Nomor Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('rent_start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('rent_end_date')
                    ->label('Tanggal Berakhir')
                    ->date('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\Action::make('add_tenant')
                    ->label('Tambah Penghuni')
                    ->visible(fn (Room $record) => $record->status === 'available') // Hanya muncul jika status available
                    ->form(fn (Room $record) => [
                        Forms\Components\Select::make('tenant_id')
                            ->label('Penyewa')
                            ->options(Tenant::all()->pluck('name', 'id'))
                            ->required(),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->default($record->capacity)
                            ->disabled(),

                        Forms\Components\Select::make('payment_category')
                            ->label('Kategori Pembayaran')
                            ->options([
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                            ])
                            ->disabled(),

                        Forms\Components\DatePicker::make('rent_start_date')
                            ->label('Tanggal Mulai Sewa')
                            ->disabled(),

                        Forms\Components\DatePicker::make('rent_end_date')
                            ->label('Tanggal Berakhir Sewa')
                            ->disabled(),
                    ])
                    ->action(function (Room $record, array $data) {
                        $tenant = Tenant::find($data['tenant_id']);
                        if ($tenant) {
                            $startDate = now();
                            $endDate = $tenant->per_month == 1
                                ? $startDate->copy()->addMonths(6)
                                : $startDate->copy()->addYear();

                            $record->update([
                                'tenant_id' => $tenant->id,
                                'status' => 'occupied',
                                'payment_category' => $tenant->per_month == 1 ? 'semester' : 'year',
                                'rent_start_date' => $startDate->toDateString(),
                                'rent_end_date' => $endDate->toDateString(),
                            ]);
                        }
                    })
                    ->color('primary'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
