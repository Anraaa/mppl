<?php

namespace App\Filament\Admin\Resources\TotalPemasukanResource\Pages;

use App\Filament\Admin\Resources\TotalPemasukanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTotalPemasukans extends ListRecords
{
    protected static string $resource = TotalPemasukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
