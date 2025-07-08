<?php

namespace App\Filament\Admin\Resources\BookingResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\HtmlString;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $recordTitleAttribute = 'metode';
    protected static ?string $title = 'Payment History';
    protected static ?string $icon = 'heroicon-o-credit-card';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('metode')
                    ->label('Payment Method')
                    ->required()
                    ->options([
                        'gopay' => 'Gopay',
                        'va_bca' => 'VA BCA',
                        'va_bri' => 'VA BRI',
                        'va_bni' => 'VA BNI',
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                    ])
                    ->native(false)
                    ->live(),
                    
                Forms\Components\TextInput::make('amount')
                    ->label('Amount Paid')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default(fn ($record) => $record?->booking?->total_bayar),
                    
                Forms\Components\FileUpload::make('bukti_transfer')
                    ->label('Payment Proof')
                    ->image()
                    ->directory('payments/proofs')
                    ->maxSize(2048)
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->helperText('Max 2MB. JPG, PNG, or PDF')
                    ->columnSpanFull(),
                    
                Forms\Components\Select::make('status')
                    ->label('Payment Status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'verified' => 'Verified',
                        'failed' => 'Failed',
                    ])
                    ->native(false),
                    
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Paid At')
                    ->default(now())
                    ->native(false),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Admin Notes')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('metode')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gopay' => 'primary',
                        'va_bca', 'va_bri', 'va_bni' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                    
                Tables\Columns\ImageColumn::make('bukti_transfer')
                    ->label('Proof')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(fn ($record) => asset('images/payment-methods/' . $record->metode . '.png')),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'waiting_verification' => 'primary',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'verified' => 'Verified',
                        'failed' => 'Failed',
                    ])
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('metode')
                    ->options([
                        'gopay' => 'Gopay',
                        'va_bca' => 'VA BCA',
                        'va_bri' => 'VA BRI',
                        'va_bni' => 'VA BNI',
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                    ])
                    ->multiple()
                    ->label('Payment Method'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Payment')
                    ->modalHeading('Add Payment for Booking #' . $this->getOwnerRecord()->id),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('verify')
                        ->action(fn ($record) => $record->update(['status' => 'verified']))
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(fn ($record) => $record->status !== 'waiting_verification'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Payment')
                    ->modalHeading('Add Payment for Booking #' . $this->getOwnerRecord()->id),
            ])
            ->defaultSort('paid_at', 'desc');
    }
}