<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea; 
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Pengelola Penghuni';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->maxLength(255)
                        ->hiddenOn('edit'),
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3)
                        ->nullable(),
                    Forms\Components\Toggle::make('per_month')
                        ->label('Pembayaran per Semester')
                        ->required(),
                    Forms\Components\TextInput::make('price_per_semester')
                        ->label('Harga per Semester')
                        ->required()
                        ->numeric()
                        ->default(2000000),
                    Forms\Components\TextInput::make('price_per_year')
                        ->label('Harga per Tahun')
                        ->required()
                        ->numeric()
                        ->default(8000000),
                ])->columns(2),

                Forms\Components\Section::make('User Photos')
                ->schema([
                    FileUpload::make('profile_photo')
                        ->label('Foto Penghuni')
                        ->image()
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('200')
                        ->imageResizeTargetHeight('200')
                        ->directory('profile-photos'),
                    FileUpload::make('ktp_photo')
                        ->label('Foto KTP')
                        ->image()
                        ->imageResizeTargetWidth('800')
                        ->directory('ktp-photos'),
                ])->columns(2),

                Forms\Components\Section::make('Roles')
                ->schema([
                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->searchable()
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('profile_photo')
                ->label('Photo')
                ->circular()
                ->width(50)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable()
                ->width(200)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('phone')
                ->searchable()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('address')
                ->label('Alamat')
                ->limit(30)
                ->searchable()
                ->width(200)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\IconColumn::make('per_month')
                ->label('Per Semester')
                ->boolean()
                ->width(100)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('price_per_semester')
                ->label('Harga/Semester')
                ->money('IDR')
                ->sortable()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('price_per_year')
                ->label('Harga/Tahun')
                ->money('IDR')
                ->sortable()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('roles.name')
                ->label('Roles')
                ->badge()
                ->separator(',')
                ->searchable()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created at')
                ->dateTime()
                ->width(150)
                ->verticallyAlignStart(), // Align content to the top
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('roles')
                ->relationship('roles', 'name')
                ->preload()
                ->multiple()
                ->searchable()
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])
        ->striped() // Optional: Add striped rows
        ->defaultSort('name', 'asc'); // Optional: Set default sorting
}
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}