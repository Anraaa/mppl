<?php

namespace App\Filament\Client\Resources\StudioResource\Pages;

use App\Filament\Client\Resources\StudioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudios extends ListRecords
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
