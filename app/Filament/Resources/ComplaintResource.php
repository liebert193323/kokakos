<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use illuminate\Support\Facades\Auth;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $pluralModelLabel = 'Pengaduan';
    protected static ?string $navigationGroup = 'pengaduan penghuni';
    protected static ?string $modelLabel = 'Pengaduan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Judul')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tenant_name')
                    ->required()
                    ->label('Nama Penyewa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('room_number')
                    ->required()
                    ->label('Nomor Kamar')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->directory('complaints-photos')
                    ->label('Foto')
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->label('Deskripsi')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (Auth::user()->hasRole('penghuni')) {
                    $query->where('user_id', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenant_name')
                    ->label('Nama Penyewa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')  // Newest first
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'view' => Pages\ViewComplaint::route('/{record}'),
        ];
    }

    // Auto-set status to pending when creating new complaint
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        return $data;
    }
}