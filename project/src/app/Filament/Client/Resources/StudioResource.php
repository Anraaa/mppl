<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\StudioResource\Pages;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $modelLabel = 'Studio Foto';

    protected static ?string $navigationLabel = 'Daftar Studio Foto';

    protected static ?string $navigationGroup = 'Layanan Studio Foto';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form schema if needed for view page
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto Studio')
                    ->disk('public')
                    ->width(80)
                    ->height(60)
                    ->square()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('nama_studio')
                    ->label('Nama Studio')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Studio $record) => $record->deskripsi ? Str::limit($record->deskripsi, 140) : '')
                    ->tooltip(fn (Studio $record) => $record->deskripsi),
                    
                Tables\Columns\TextColumn::make('harga_per_jam')
                    ->label('Harga per Jam')
                    ->numeric()
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->extraAttributes(['class' => 'font-medium']),
                    
                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kapasitas')
                    ->numeric()
                    ->suffix(' orang')
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->native(false),
                    
                Tables\Filters\TernaryFilter::make('harga_per_jam')
                    ->label('Rentang Harga')
                    ->placeholder('Semua Harga')
                    ->trueLabel('Dibawah Rp 300.000')
                    ->falseLabel('Diatas Rp 300.000')
                    ->queries(
                        true: fn (Builder $query) => $query->where('harga_per_jam', '<', 300000),
                        false: fn (Builder $query) => $query->where('harga_per_jam', '>=', 300000),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->button()
                    ->size('sm'),
            ])
            ->bulkActions([])
            ->emptyStateActions([])
            ->defaultSort('nama_studio', 'asc')
            ->deferLoading()
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudios::route('/'),
            'view' => Pages\ViewStudio::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->when(!auth()->user()->isAdmin(), function ($query) {
    //             return $query->where('is_active', true);
    //         });
    // }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('is_active', true)->count() > 0 ? 'primary' : 'danger';
    }
}