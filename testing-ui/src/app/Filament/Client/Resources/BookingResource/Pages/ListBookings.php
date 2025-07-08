<?php

namespace App\Filament\Client\Resources\BookingResource\Pages;

use App\Filament\Client\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Booking;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // app/Filament/Client/Resources/BookingResource/Pages/ListBookings.php
protected function getTableActions(): array
{
    return [
        Actions\Action::make('pay')
            ->label('Bayar')
            ->url(fn (Booking $record) => BookingResource::getUrl('payment', ['record' => $record->id]))
            ->visible(fn (Booking $record) => $record->payment_status === 'pending')
            ->icon('heroicon-o-credit-card'),
    ];
}
}
