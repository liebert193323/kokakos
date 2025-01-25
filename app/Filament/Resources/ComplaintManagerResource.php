<?php
namespace App\Filament\Resources;

use App\Models\ComplaintManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ComplaintManagerResource\Pages;

class ComplaintManagerResource extends Resource
{
    protected static ?string $model = ComplaintManager::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Pengaduan';
    protected static ?string $pluralModelLabel = 'Pengelola Pengaduan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->label('Pilih Pengguna'),
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nama Lengkap'),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->label('Email')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pengguna'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintManagers::route('/'),
            'create' => Pages\CreateComplaintManager::route('/create'),
            'edit' => Pages\EditComplaintManager::route('/{record}/edit'),
        ];
    }
}