<?php

namespace App\Filament\Client\Resources\BookingResource\Pages;

use App\Filament\Client\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('payment', ['record' => $this->record->id]);
}
}
