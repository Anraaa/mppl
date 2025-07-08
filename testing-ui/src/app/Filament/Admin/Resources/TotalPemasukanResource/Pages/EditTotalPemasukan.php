<?php

namespace App\Filament\Admin\Resources\TotalPemasukanResource\Pages;

use App\Filament\Admin\Resources\TotalPemasukanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTotalPemasukan extends EditRecord
{
    protected static string $resource = TotalPemasukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
