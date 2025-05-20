<?php

namespace App\Filament\Client\Resources\StudioResource\Pages;

use App\Filament\Client\Resources\StudioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Tabs;

class ViewStudio extends ViewRecord
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('book')
                ->label('Booking Sekarang')
                ->icon('heroicon-s-calendar')
                ->color('primary')
               /*  ->url(route('filament.client.resources.bookings.create', [
                    'studio_id' => $this->record->id
                ])) */
                ->visible(fn () => auth()->user()->can('book_studio')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Studio Details')
                    ->tabs([
                        Tabs\Tab::make('Informasi Utama')
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\ImageEntry::make('foto')
                                            ->label('')
                                            ->disk('public')
                                            ->height('300px')
                                            ->extraImgAttributes([
                                                'class' => 'rounded-lg object-cover shadow-md'
                                            ]),
                                    ]),
                                    
                                Components\Grid::make(2)
                                    ->schema([
                                        Components\TextEntry::make('nama_studio')
                                            ->label('Nama Studio')
                                            ->weight('bold')
                                            ->size('lg'),
                                            
                                        Components\TextEntry::make('harga_per_jam')
                                            ->label('Harga')
                                            ->money('IDR', locale: 'id')
                                            ->color('primary')
                                            ->weight('bold')
                                            ->size('lg'),
                                            
                                        Components\TextEntry::make('kapasitas')
                                            ->label('Kapasitas Orang')
                                            ->badge()
                                            ->color('gray'),
                                            
                                    ]),
                            ]),
                            
                        Tabs\Tab::make('Detail Lengkap')
                            ->schema([
                                Components\TextEntry::make('deskripsi')
                                    ->label('')
                                    ->markdown()
                                    ->columnSpanFull(),
                                    
                                Components\TextEntry::make('fasilitas')
                                    ->label('Fasilitas')
                                    //->bulletList()
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),
                            
                        Tabs\Tab::make('Jadwal')
                            ->schema([
                                Components\TextEntry::make('jam_operasional')
                                    ->label('Jam Buka')
                                    ->icon('heroicon-s-clock'),
                                    
                                Components\TextEntry::make('hari_operasional')
                                    ->label('Hari Buka')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getHeading(): string
    {
        return $this->record->nama_studio;
    }

    public function getSubheading(): string
    {
        return 'Detail Lengkap Studio Foto';
    }
}