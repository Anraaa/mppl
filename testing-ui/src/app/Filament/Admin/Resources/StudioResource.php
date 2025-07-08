<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudioResource\Pages;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $modelLabel = 'Studio Foto';

    protected static ?string $navigationLabel = 'Manajemen Studio';

    protected static ?string $navigationGroup = 'Studio Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Studio')
                    ->schema([
                        Forms\Components\TextInput::make('nama_studio')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                            
                        Forms\Components\Textarea::make('deskripsi')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(500),
                            
                        Forms\Components\TextInput::make('harga_per_jam')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(10000),
                            
                        Forms\Components\FileUpload::make('foto')
                            ->image()
                            ->directory('studios')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Detail Operasional')
                    ->schema([
                        Forms\Components\TextInput::make('kapasitas')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->suffix('orang'),
                            
                        Forms\Components\TextInput::make('jam_operasional')
                            ->required()
                            ->placeholder('Contoh: 09:00 - 21:00')
                            ->maxLength(50),
                            
                        Forms\Components\TextInput::make('hari_operasional')
                            ->required()
                            ->placeholder('Contoh: Senin-Minggu')
                            ->maxLength(50),
                            
                        Forms\Components\Textarea::make('fasilitas')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder("Masukkan fasilitas, pisahkan dengan koma\nContoh: AC, Lighting Profesional, Backdrop")
                            ->maxLength(1000),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Aktif'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto Studio')
                    ->circular(),
                    
                Tables\Columns\TextColumn::make('nama_studio')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('harga_per_jam')
                    ->numeric()
                    ->sortable()
                    ->money('IDR')
                    ->label('Harga/Jam'),
                    
                Tables\Columns\TextColumn::make('kapasitas')
                    ->numeric()
                    ->sortable()
                    ->suffix(' org'),
                    
                Tables\Columns\TextColumn::make('jam_operasional')
                    ->label('Jam Operasi')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Hanya yang aktif')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_studio', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relation managers jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
            //'view' => Pages\ViewStudio::route('/{record}'),
        ];
    }
}