<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintHandlerResource\Pages;
use App\Models\Complaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintHandlerResource extends Resource
{
    protected static ?string $model = Complaint::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Pengaduan';
    protected static ?string $navigationLabel = 'Kelola Pengaduan';
    protected static ?string $modelLabel = 'Kelola Pengaduan';
    protected static ?string $pluralModelLabel = 'Kelola Pengaduan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('response')
                    ->required()
                    ->label('Tanggapan'),
            ]);
    }

    public static function table(Table $table): Table
    {
         return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul'),
                Tables\Columns\TextColumn::make('tenant_name')
                    ->label('Nama Penyewa'),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                    }),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('respond')
                    ->label('Tanggapi')
                    ->action(function (Complaint $record, array $data): void {
                        $record->update(['status' => 'processing']);
                        // Add response handling here if needed
                    })
                    ->visible(fn (Complaint $record): bool => $record->status === 'pending')
                    ->button()
                    ->color('primary'),
                Tables\Actions\Action::make('complete')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Complaint $record): bool => 
                        $record->status === 'processing' && 
                        !empty($record->response)
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Pengaduan')
                    ->modalDescription('Apakah Anda yakin ingin menyelesaikan pengaduan ini?')
                    ->modalSubmitActionLabel('Ya, Selesaikan')
                    ->modalCancelActionLabel('Batal'),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintHandlers::route('/'),
        ];
    }
}