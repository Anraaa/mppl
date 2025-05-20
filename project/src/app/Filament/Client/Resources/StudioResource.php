<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\StudioResource\Pages;
use App\Filament\Client\Resources\StudioResource\RelationManagers;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $modelLabel = 'Studio Foto';

    protected static ?string $navigationLabel = 'Daftar Studio Foto';

    protected static ?string $navigationGroup = 'Layanan Studio Foto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_studio')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('harga_per_jam')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('foto')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('fasilitas')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_studio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_per_jam')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('foto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye') 
                    ->color('info')         
                    ->visible('view_studios'),
            ])
            ->bulkActions([
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function canCreate(): bool
{
    return false; // Nonaktifkan sama sekali
    // Atau lebih dinamis:
    // return auth()->user()->hasRole('admin');
}
    

    /* public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_active', true);
    } */
    public static function canViewAny(): bool
{
    return true; // paksa tampil
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudios::route('/'),
            //'create' => Pages\CreateStudio::route('/create'),
            //'edit' => Pages\EditStudio::route('/{record}/edit'),
            'view' => Pages\ViewStudio::route('/{record}'),
        ];
    }
}
