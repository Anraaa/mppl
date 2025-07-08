<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.client.pages.dashboard';
    
    protected function getHeaderWidgets(): array
    {
        return [
            // Tambahkan widget khusus client lainnya
        ];
    }
    
    // protected function getColumns(): int|string|array
    // {
    //     return 2; // Atur layout grid
    // }
    
    public function getTitle(): string
    {
        return 'Client Dashboard'; // Judul custom
    }
}