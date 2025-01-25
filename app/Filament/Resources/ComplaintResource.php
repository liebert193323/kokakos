<?php
namespace App\Filament\Resources;

use App\Models\Complaint;
use App\Filament\Resources\ComplaintResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Manajemen Pengaduan';
    protected static ?string $pluralModelLabel = 'Pengaduan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('tenant_id')
                ->relationship('tenant', 'name')
                ->required()
                ->label('Penyewa'),
            Forms\Components\Select::make('complaint_manager_id')
                ->relationship('complaintManager', 'name')
                ->nullable()
                ->label('Pengelola Pengaduan'),
            Forms\Components\Textarea::make('complaint')
                ->required()
                ->columnSpanFull()
                ->label('Isi Pengaduan'),
            Forms\Components\Select::make('status')
                ->options([
                    'Pending' => 'Menunggu',
                    'In Progress' => 'Sedang Diproses', 
                    'Resolved' => 'Selesai'
                ])
                ->default('Pending')
                ->required()
                ->label('Status')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa'),
                Tables\Columns\TextColumn::make('complaintManager.name')
                    ->label('Pengelola'),
                Tables\Columns\TextColumn::make('complaint')
                    ->limit(50)
                    ->label('Pengaduan'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Pending',
                        'info' => 'In Progress',
                        'success' => 'Resolved',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}