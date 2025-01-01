<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\Pages\ViewRoom;
use App\Models\Room;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Carbon;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $modelLabel = 'Kamar';
    protected static ?string $pluralModelLabel = 'Kamar';
    protected static ?string $navigationLabel = 'Kamar';
    protected static ?string $navigationGroup = 'Master';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('number')
                    ->label('Nomor Kamar')
                    ->required()
                    ->maxLength(255)
                    ->visible(fn ($context) => $context === 'create'),

                Forms\Components\Select::make('tenant_id')
                    ->label('Penyewa')
                    ->options(Tenant::all()->pluck('name', 'id'))
                    ->nullable()
                    ->reactive()
                    ->visible(fn ($context) => $context === 'edit')
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $tenant = Tenant::find($state);
                            $startDate = Carbon::now();
                            $endDate = $tenant->per_month == 1
                                ? $startDate->copy()->addMonths(6)
                                : $startDate->copy()->addYear();

                            $set('payment_category', $tenant->per_month == 1 ? 'semester' : 'year');
                            $set('price_per_semester', $tenant->price_per_semester);
                            $set('price_per_year', $tenant->price_per_year);
                            $set('rent_start_date', $startDate);
                            $set('rent_end_date', $endDate);
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

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('status', 'desc')
            ->defaultGroup('status')
            ->recordUrl(fn(Room $record): string => static::getUrl('view', ['record' => $record]))
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        default => 'Tidak Diketahui',
                    })
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
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                
                Tables\Actions\EditAction::make()
                    ->label('Ubah'),

                    Tables\Actions\Action::make('add_tenant')
                    ->label('Tambah Penghuni')
                    ->visible(fn (Room $record) => $record->status === 'available')
                    ->form([
                        Forms\Components\Select::make('tenant_id')
                            ->label('Penyewa')
                            ->options(Tenant::all()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $tenant = Tenant::find($state);
                                    $startDate = Carbon::now();
                                    $endDate = $tenant->per_month == 1
                                        ? $startDate->copy()->addMonths(6)
                                        : $startDate->copy()->addYear();
                
                                    $set('payment_category', $tenant->per_month == 1 ? 'semester' : 'year');
                                    $set('price', $tenant->per_month == 1 
                                        ? $tenant->price_per_semester 
                                        : $tenant->price_per_year);
                                    $set('rent_start_date', $startDate->format('Y-m-d'));
                                    $set('rent_end_date', $endDate->format('Y-m-d'));
                                }
                            }),
                
                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->default(1)
                            ->disabled(),
                
                        Forms\Components\Select::make('payment_category')
                            ->label('Kategori Pembayaran')
                            ->options([
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                            ])
                            ->disabled(),
                
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->disabled()
                            ->prefix('Rp')
                            ->numeric(),
                
                        Forms\Components\DatePicker::make('rent_start_date')
                            ->label('Tanggal Mulai Sewa')
                            ->default(now()) // Tambahkan default value
                            ->required(), // Tambahkan required
                
                        Forms\Components\DatePicker::make('rent_end_date')
                            ->label('Tanggal Berakhir Sewa')
                            ->default(function() {
                                return now()->addMonths(6); // Default 6 bulan
                            })
                            ->required(), // Tambahkan required
                    ])
                    ->action(function (Room $record, array $data) {
                        $tenant = Tenant::find($data['tenant_id']);
                        if ($tenant) {
                            // Pastikan data yang dibutuhkan ada
                            $startDate = $data['rent_start_date'] ?? now()->format('Y-m-d');
                            $endDate = $data['rent_end_date'] ?? now()->addMonths(6)->format('Y-m-d');
                
                            $record->update([
                                'tenant_id' => $tenant->id,
                                'status' => 'occupied',
                                'payment_category' => $tenant->per_month == 1 ? 'semester' : 'year',
                                'price_per_semester' => $tenant->price_per_semester,
                                'price_per_year' => $tenant->price_per_year,
                                'rent_start_date' => $startDate,
                                'rent_end_date' => $endDate,
                            ]);
                        }
                    })
                    ->modalHeading('Tambah Penghuni Baru')
                    ->color('primary')
            ]);
    }

    public static function productInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('number')
                            ->label('Nomor Kamar'),
                        TextEntry::make('tenant.name')
                            ->label('Penyewa')
                            ->default('-'),
                        TextEntry::make('capacity')
                            ->label('Kapasitas'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'available' => 'Tersedia',
                                'occupied' => 'Terisi',
                                default => 'Tidak Diketahui',
                            }),
                        TextEntry::make('payment_category')
                            ->label('Kategori Pembayaran')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                                default => '-',
                            }),
                            TextEntry::make('price')
                            ->label('Harga')
                            ->state(function (Room $record): string {
                                // Cek payment category dan pastikan harga ada
                                if (!$record->payment_category) {
                                    return '-';
                                }
                                
                                // Ambil harga berdasarkan kategori pembayaran
                                $price = $record->payment_category === 'semester' 
                                    ? ($record->price_per_semester ?? 0)
                                    : ($record->price_per_year ?? 0);
                                
                                // Jika harga 0 atau null, tampilkan strip
                                if (!$price) {
                                    return '-';
                                }
                                
                                // Format harga dengan pemisah ribuan
                                return 'Rp ' . number_format($price, 0, ',', '.');
                            }),
                        TextEntry::make('rent_start_date')
                            ->label('Tanggal Mulai')
                            ->date('d/m/Y'),
                        TextEntry::make('rent_end_date')
                            ->label('Tanggal Berakhir')
                            ->date('d/m/Y'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
            'view' => ViewRoom::route('/{record}'),
        ];
    }
}