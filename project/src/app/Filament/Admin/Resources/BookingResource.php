<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Studio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Bookings';
    protected static ?string $modelLabel = 'Booking';
    protected static ?string $slug = 'bookings';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(User::all()->pluck('name', 'id'))
                            ->native(false),
                            
                        Forms\Components\Select::make('studio_id')
                            ->label('Studio')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(Studio::all()->pluck('nama_studio', 'id'))
                            ->native(false),
                            
                        Forms\Components\DatePicker::make('tanggal_booking')
                            ->label('Booking Date')
                            ->required()
                            ->native(false)
                            ->weekStartsOnMonday(),
                            
                        Forms\Components\TimePicker::make('jam_mulai')
                            ->label('Start Time')
                            ->required()
                            ->native(false)
                            ->minutesStep(30),
                            
                        Forms\Components\TimePicker::make('jam_selesai')
                            ->label('End Time')
                            ->required()
                            ->native(false)
                            ->minutesStep(30),
                            
                        Forms\Components\TextInput::make('total_bayar')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                            
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'canceled' => 'Canceled',
                            ]),
                            
                        Forms\Components\Textarea::make('catatan')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('studio.nama_studio')
                    ->label('Studio')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tanggal_booking')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->state(function (Booking $record) {
                        return Carbon::parse($record->jam_mulai)->format('g:i A') . ' - ' . 
                               Carbon::parse($record->jam_selesai)->format('g:i A');
                    }),
                    
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Amount')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'canceled' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked At')
                    ->dateTime('M d, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                        'completed' => 'Completed',
                    ]),
                    
                Tables\Filters\Filter::make('tanggal_booking')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm')
                        ->action(fn ($records) => $records->each->update(['status' => 'confirmed']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('tanggal_booking', 'desc')
            ->persistFiltersInSession();
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}