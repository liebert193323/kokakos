<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone'),

                Tables\Columns\IconColumn::make('per_month')
                    ->label('Per Semester')
                    ->boolean(),

                Tables\Columns\TextColumn::make('price_per_semester')
                    ->label('Harga/Semester')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_year')
                    ->label('Harga/Tahun')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
