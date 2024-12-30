<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $modelLabel = 'Kamar';
    protected static ?string $pluralModelLabel = 'Kamar';
    protected static ?string $navigationLabel = 'Kamar';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Penyewa')
                    ->options(Tenant::all()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $tenant = Tenant::find($state);
                        if ($tenant) {
                            $set('price_per_semester', $tenant->price_per_semester);
                            $set('price_per_year', $tenant->price_per_year);
                            $set('payment_category', $tenant->per_month == 1 ? 'semester' : 'year');
                        }
                    }),

                Forms\Components\TextInput::make('number')
                    ->label('Nomor Kamar')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('capacity')
                    ->label('Kapasitas')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                    ])
                    ->required()
                    ->default('available'),

                Forms\Components\Select::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->options([
                        'semester' => 'Perbulan',
                        'year' => 'Pertaun',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state === 'semester') {
                            $set('price_per_semester', 2000000);
                            $set('price_per_year', 0);
                        } else {
                            $set('price_per_semester', 0);
                            $set('price_per_year', 8000000);
                        }
                    }),

                Forms\Components\TextInput::make('price_per_semester')
                    ->label('Harga per Bulan')
                    ->numeric()
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('price_per_year')
                    ->label('Harga per Tahun')
                    ->numeric()
                    ->required()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->columns([
                Tables\Columns\Layout\Grid::make([
                    'default' => 3,
                    'sm' => 3,
                    'md' => 4,
                    'lg' => 6,
                    'xl' => 8,
                ])
                ->schema([
                    Tables\Columns\TextColumn::make('number')
                        ->label('')
                        ->formatStateUsing(function ($state, $record) {
                            $statusColor = $record->status === 'available' ? 'bg-green-100' : 'bg-red-100';
                            $textColor = $record->status === 'available' ? 'text-green-800' : 'text-red-800';
                            $price = $record->payment_category === 'semester' 
                                ? number_format($record->price_per_semester, 0, ',', '.') 
                                : number_format($record->price_per_year, 0, ',', '.');
                            
                            return view('components.room-card', [
                                'number' => $state,
                                'statusColor' => $statusColor,
                                'textColor' => $textColor,
                                'status' => $record->status === 'available' ? 'Tersedia' : 'Terisi',
                                'price' => $price,
                                'payment_category' => $record->payment_category === 'semester' ? '/bulan' : '/tahun'
                            ]);
                        })
                        ->html(),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                    ]),
                Tables\Filters\SelectFilter::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->options([
                        'semester' => 'Perbulan',
                        'year' => 'Pertaun',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
    
}