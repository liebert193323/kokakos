<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintHandlerResource\Pages;
use App\Models\ComplaintResponse;
use App\Models\Complaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintHandlerResource extends Resource
{
    protected static ?string $model = ComplaintResponse::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Pengaduan';
    protected static ?string $navigationLabel = 'Kelola Pengaduan';
    protected static ?string $modelLabel = 'Kelola Pengaduan';
    protected static ?string $pluralModelLabel = 'Kelola Pengaduan';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol "Create"
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                // This ensures we start from complaints, not responses
                Complaint::query()
            )
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
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(), // Menambahkan action "View"
                Tables\Actions\Action::make('respond')
                    ->label('Tanggapi')
                    ->form([
                        Forms\Components\Textarea::make('response')
                            ->required()
                            ->label('Tanggapan')
                    ])
                    ->action(function (Complaint $record, array $data): void {
                        // Create response record
                        ComplaintResponse::create([
                            'complaint_id' => $record->id,
                            'response' => $data['response'],
                            'responded_at' => now(),
                        ]);
                        
                        // Update complaint status
                        $record->update([
                            'status' => 'processing',
                        ]);
                    })
                    ->visible(fn (Complaint $record): bool => $record->status === 'pending')
                    ->button()
                    ->color('primary'),
                    
                Tables\Actions\Action::make('complete')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Complaint $record): bool => 
                        $record->status === 'processing'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Pengaduan')
                    ->modalDescription('Apakah Anda yakin ingin menyelesaikan pengaduan ini?')
                    ->modalSubmitActionLabel('Ya, Selesaikan')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (Complaint $record) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintHandlers::route('/'),
        ];
    }
}