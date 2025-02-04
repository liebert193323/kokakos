<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\Pages\ViewRoom;
use App\Models\Room;
use App\Models\User;
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
                    ->visible(fn($context) => $context === 'create'),

                Forms\Components\Select::make('user_id')
                    ->label('Penyewa')
                    ->options(
                        User::whereHas('roles', function ($query) {
                            $query->where('name', 'Penghuni'); // Filter berdasarkan role 'Penghuni'
                        })
                            ->whereDoesntHave('rooms', function ($query) {
                                $query->whereNotNull('user_id'); // Menggunakan user_id
                            })
                            ->pluck('name', 'id')
                    )
                    ->nullable()
                    ->reactive()
                    ->visible(fn($context) => $context === 'edit')
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $user = User::find($state);
                            $startDate = Carbon::now();
                            $endDate = $user->per_month == 1
                                ? $startDate->copy()->addMonths(6)
                                : $startDate->copy()->addYear();

                            $set('payment_category', $user->per_month == 1 ? 'semester' : 'year');
                            $set('price_per_semester', $user->price_per_semester);
                            $set('price_per_year', $user->price_per_year);
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
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                    ])
                    ->default('available')
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\Select::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\TextInput::make('price_per_semester')
                    ->label('Harga per Semester')
                    ->numeric()
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\TextInput::make('price_per_year')
                    ->label('Harga per Tahun')
                    ->numeric()
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\DatePicker::make('rent_start_date')
                    ->label('Tanggal Mulai Sewa')
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),

                Forms\Components\DatePicker::make('rent_end_date')
                    ->label('Tanggal Berakhir Sewa')
                    ->disabled()
                    ->visible(fn($context) => $context === 'edit'),
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

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        default => 'Tidak Diketahui',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
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

                Tables\Actions\Action::make('add_user')
                    ->label('Tambah Penghuni')
                    ->visible(fn (Room $record) => $record->status === 'available')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Penyewa')
                            ->options(
                                User::whereHas('roles', function ($query) {
                                    $query->where('name', 'Penghuni');
                                })
                                ->whereDoesntHave('rooms', function ($query) {
                                    $query->whereNotNull('user_id');
                                })
                                ->pluck('name', 'id')
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $user = User::find($state);
                                    $startDate = Carbon::now();
                                    $endDate = $user->per_month == 1
                                        ? $startDate->copy()->addMonths(6)
                                        : $startDate->copy()->addYear();
                
                                    $set('payment_category', $user->per_month == 1 ? 'semester' : 'year');
                                    $set('price', $user->per_month == 1 
                                        ? $user->price_per_semester 
                                        : $user->price_per_year);
                                    $set('rent_start_date', $startDate->format('Y-m-d'));
                                    $set('rent_end_date', $endDate->format('Y-m-d'));
                                }
                            }),

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
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('rent_end_date')
                            ->label('Tanggal Berakhir Sewa')
                            ->default(function() {
                                return now()->addMonths(6);
                            })
                            ->required(),
                    ])
                    ->action(function (Room $record, array $data) {
                        $user = User::find($data['user_id']);
                        if ($user) {
                            $startDate = $data['rent_start_date'] ?? now()->format('Y-m-d');
                            $endDate = $data['rent_end_date'] ?? now()->addMonths(6)->format('Y-m-d');
                
                            $record->update([
                                'user_id' => $user->id,
                                'status' => 'occupied',
                                'payment_category' => $user->per_month == 1 ? 'semester' : 'year',
                                'price_per_semester' => $user->price_per_semester,
                                'price_per_year' => $user->price_per_year,
                                'rent_start_date' => $startDate,
                                'rent_end_date' => $endDate,
                            ]);
                        }
                    })
                    ->modalHeading('Tambah Penghuni Baru')
                    ->color('primary'),

                    Tables\Actions\Action::make('remove_user')
                    ->label('Hapus Penghuni')
                    ->visible(fn (Room $record) => $record->status === 'occupied')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Penghuni')
                    ->modalDescription('Apakah Anda yakin ingin menghapus penghuni dari kamar ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->color('danger')
                    ->action(function (Room $record) {
                        $record->update([
                            'user_id' => null,
                            'status' => 'available',
                            'payment_category' => 'semester', // Set a default value instead of null
                            'price_per_semester' => 0,        // Set to 0 instead of null
                            'price_per_year' => 0,            // Set to 0 instead of null
                            'rent_start_date' => null,
                            'rent_end_date' => null,
                        ]);
                    }),

                Tables\Actions\Action::make('extend_period')
                    ->label('Perpanjang Masa Sewa')
                    ->visible(fn (Room $record) => $record->status === 'occupied')
                    ->form([
                        Forms\Components\DatePicker::make('rent_end_date')
                            ->label('Tanggal Berakhir Sewa Baru')
                            ->required()
                            ->default(fn (Room $record) => Carbon::parse($record->rent_end_date)->addMonths(6)),
                    ])
                    ->action(function (Room $record, array $data) {
                        $record->update([
                            'rent_end_date' => $data['rent_end_date'],
                        ]);
                    })
                    ->modalHeading('Perpanjang Masa Sewa')
                    ->color('success'),
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
                        TextEntry::make('user.name')
                            ->label('Penyewa')
                            ->default('-'),
                        TextEntry::make('capacity')
                            ->label('Kapasitas'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'available' => 'Tersedia',
                                'occupied' => 'Terisi',
                                default => 'Tidak Diketahui',
                            }),
                        TextEntry::make('payment_category')
                            ->label('Kategori Pembayaran')
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                                default => '-',
                            }),
                        TextEntry::make('price')
                            ->label('Harga')
                            ->state(function (Room $record): string {
                                if (!$record->payment_category) {
                                    return '-';
                                }
                                
                                $price = $record->payment_category === 'semester' 
                                    ? ($record->price_per_semester ?? 0)
                                    : ($record->price_per_year ?? 0);
                                
                                if (!$price) {
                                    return '-';
                                }
                                
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