<?php

namespace App\Filament\Client\Resources\BookingResource\Pages;

use App\Filament\Client\Resources\BookingResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PrintReceipt extends Page
{
    use InteractsWithRecord;

    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.client.resources.booking-resource.pages.print-receipt';

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
