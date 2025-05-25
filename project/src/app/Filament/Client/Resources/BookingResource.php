<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Studio;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\BookingConflictService;
use Closure;
use App\Filament\Client\Resources\BookingResource\Pages\PrintReceipt;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $modelLabel = 'Booking';
    protected static ?string $slug = 'bookings';
    protected static ?string $navigationLabel = 'Booking Studio Foto';
    protected static ?string $navigationGroup = 'Layanan Studio Foto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Booking')
                    ->description('Isi informasi booking studio foto Anda')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                        
                        Forms\Components\Select::make('studio_id')
                            ->label('Studio')
                            ->options(function() {
                                return Studio::query()
                                    ->where('is_active', true)
                                    ->pluck('nama_studio', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->rules(['exists:studios,id'])
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $livewire) {
                                $studio = Studio::find($state);
                                if ($studio) {
                                    $set('harga_per_jam', $studio->harga_per_jam);
                                    $set('studio_name', $studio->nama_studio);
                                    $set('operational_hours', $studio->jam_operasional);
                                    $set('operational_days', $studio->hari_operasional);
                                }
                                $livewire->dispatch('studio-selected', studioId: $state);
                            }),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_booking')
                                    ->label('Tanggal Booking')
                                    ->required()
                                    ->native(false)
                                    ->minDate(carbon::today())
                                    ->maxDate(now()->addMonths(3))
                                    ->weekStartsOnMonday()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, $livewire) {
                                        $livewire->dispatch('date-selected', date: $state);
                                    })
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $selectedDate = Carbon::parse($value);
                                                $selectedDate->locale('id');
                                                $dayName = $selectedDate->isoFormat('dddd');
                                                $operationalDays = $get('operational_days') ?? 'Senin-Minggu';
                                                
                                                // Parse hari operasional studio
                                                $daysRange = explode('-', $operationalDays);
                                                $startDay = trim($daysRange[0]);
                                                $endDay = isset($daysRange[1]) ? trim($daysRange[1]) : $startDay;
                                                
                                                $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                                $startIndex = array_search($startDay, $allDays);
                                                $endIndex = array_search($endDay, $allDays);
                                                
                                                $operationalDaysList = [];
                                                if ($startIndex <= $endIndex) {
                                                    $operationalDaysList = array_slice($allDays, $startIndex, $endIndex - $startIndex + 1);
                                                } else {
                                                    // Jika range melewati minggu (e.g. Jumat-Senin)
                                                    $operationalDaysList = array_merge(
                                                        array_slice($allDays, $startIndex),
                                                        array_slice($allDays, 0, $endIndex + 1)
                                                    );
                                                }
                                                
                                                if (!in_array($dayName, $operationalDaysList)) {
                                                    $fail("Studio tidak beroperasi pada hari {$dayName}. Hari operasional: {$operationalDays}");
                                                }
                                            };
                                        },
                                    ])
                                    ->suffixIcon('heroicon-o-calendar'),
                                
                                Forms\Components\TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->minutesStep(60)
                                    ->format('H:00')
                                    ->native(false)
                                    ->displayFormat('H:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $total = static::calculateTotal($get);
                                        $set('total_bayar', $total);
                                        
                                        if ($get('studio_id') && $get('tanggal_booking') && $get('jam_selesai')) {
                                            if (BookingConflictService::checkConflicts(
                                                $get('studio_id'),
                                                $get('tanggal_booking'),
                                                $get('jam_mulai'),
                                                $get('jam_selesai')
                                            )) {
                                                $set('has_conflict', true);
                                            } else {
                                                $set('has_conflict', false);
                                            }
                                        }
                                    })
                                    ->rules([
                                        function (Forms\Get $get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $now = Carbon::now();
                                                $selectedDate = Carbon::parse($get('tanggal_booking'));
                                                $selectedTime = Carbon::parse($value);
                                                
                                                // Jika tanggal booking adalah hari ini
                                                if ($selectedDate->isToday()) {
                                                    $minStartTime = $now->addHours(2)->format('H:00');
                                                    
                                                    if ($selectedTime->lt($now->addHours(2))) {
                                                        $fail("Minimal booking 2 jam dari sekarang. Jam tersedia mulai {$minStartTime}");
                                                    }
                                                }
                                                
                                                // Validasi jam operasional studio
                                                $operationalHours = $get('operational_hours') ?? '09:00 - 21:00';
                                                [$openTime, $closeTime] = explode('-', $operationalHours);
                                                $openTime = trim($openTime);
                                                $closeTime = trim($closeTime);
                                                
                                                $openHour = Carbon::parse($openTime);
                                                $closeHour = Carbon::parse($closeTime);
                                                
                                                if ($selectedTime->lt($openHour)) {
                                                    $fail("Studio buka mulai pukul {$openTime}");
                                                }
                                                
                                                if ($selectedTime->gte($closeHour)) {
                                                    $fail("Studio tutup sebelum pukul {$closeTime}");
                                                }
                                                
                                                if ($get('has_conflict')) {
                                                    $fail('Studio sudah dibooking pada jam tersebut');
                                                }
                                            };
                                        },
                                    ]),

                                Forms\Components\Hidden::make('has_conflict')
                                    ->default(false),
                                    
                                Forms\Components\Hidden::make('operational_hours'),
                                Forms\Components\Hidden::make('operational_days'),

                                Forms\Components\TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->minutesStep(60)
                                    ->format('H:00')
                                    ->native(false)
                                    ->displayFormat('H:00')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $total = static::calculateTotal($get);
                                        $set('total_bayar', $total);
                                        
                                        if ($get('studio_id') && $get('tanggal_booking') && $get('jam_mulai')) {
                                            if (BookingConflictService::checkConflicts(
                                                $get('studio_id'),
                                                $get('tanggal_booking'),
                                                $get('jam_mulai'),
                                                $state
                                            )) {
                                                $set('has_conflict', true);
                                            } else {
                                                $set('has_conflict', false);
                                            }
                                        }
                                    })
                                    ->rules([
                                        'required',
                                        function (Forms\Get $get) {
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $now = Carbon::now();
                                                $selectedDate = Carbon::parse($get('tanggal_booking'));
                                                $selectedEndTime = Carbon::parse($value);
                                                
                                                // Jika tanggal booking adalah hari ini
                                                if ($selectedDate->isToday()) {
                                                    $minEndTime = $now->addHours(3)->format('H:00');
                                                    
                                                    if ($selectedEndTime->lt($now->addHours(3))) {
                                                        $fail("Untuk booking hari ini, minimal selesai pada {$minEndTime}");
                                                    }
                                                }
                                                
                                                // Validasi jam operasional studio
                                                $operationalHours = $get('operational_hours') ?? '09:00 - 21:00';
                                                [$openTime, $closeTime] = explode('-', $operationalHours);
                                                $openTime = trim($openTime);
                                                $closeTime = trim($closeTime);
                                                
                                                $closeHour = Carbon::parse($closeTime);
                                                
                                                if ($selectedEndTime->gt($closeHour)) {
                                                    $fail("Studio tutup pada pukul {$closeTime}");
                                                }
                                                
                                                if (!$get('studio_id') || !$get('tanggal_booking') || !$get('jam_mulai')) {
                                                    return;
                                                }

                                                Static::validateBookingTime($get, $fail);
                                            };
                                        },
                                    ]),
                            ]),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('harga_per_jam')
                                    ->label('Harga Per Jam')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->default(0)
                                    ->suffixIcon('heroicon-o-currency-dollar'),
                                
                                Forms\Components\TextInput::make('total_bayar')
                                    ->label('Total Bayar')
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->default(0)
                                    ->suffixIcon('heroicon-o-banknotes'),
                            ]),
                        
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan khusus atau permintaan...')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
                
                        Forms\Components\Section::make('Pembayaran')
                            ->visible(fn ($record) => $record && $record->status === 'pending')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('payNow')
                                        ->label('Bayar Sekarang')
                                        ->url(fn ($record) => static::getUrl('payment', ['record' => $record->id]))
                                        ->button()
                                        ->color('success')
                                        ->icon('heroicon-o-credit-card'),
                                ]),
                            ]),
                        
                        Forms\Components\Placeholder::make('info_booking')
                        ->content(function () {
                            $now = Carbon::now();
                            $minTime = $now->addHours(2)->format('H:i');
                            return "Untuk booking hari ini, minimal 2 jam dari sekarang (Mulai {$minTime})";
                        })
                        ->visible(fn (Forms\Get $get) => 
                            $get('tanggal_booking') && Carbon::parse($get('tanggal_booking'))->isToday()
                        ),
                        
                        
                        Forms\Components\Section::make('Konfirmasi Booking')
                            ->label('Konfirmasi Booking')
                            ->description('Tolong periksa kembali informasi booking Anda')
                            ->visible(fn ($operation) => $operation === 'create' || $operation === 'edit')
                            ->schema([
                                Forms\Components\Placeholder::make('Konfirmasi')
                                    ->content(function (Forms\Get $get) {
                                        $studioId = $get('studio_id');
                                        $studioName = $studioId ? Studio::find($studioId)?->nama_studio : 'Studio belum dipilih';
                                        $date = $get('tanggal_booking') ? Carbon::parse($get('tanggal_booking'))->format('l, j F Y') : 'Tanggal belum dipilih';
                                        $start = $get('jam_mulai') ? Carbon::parse($get('jam_mulai'))->format('H:i') : '--';
                                        $end = $get('jam_selesai') ? Carbon::parse($get('jam_selesai'))->format('H:i') : '--';
                                        $total = $get('total_bayar') ? 'Rp' . number_format($get('total_bayar'), 0, ',', '.') : 'Rp0';
                                        
                                        return "Anda akan membooking: {$studioName} Pada: {$date}\nJam: {$start} - {$end}\nTotal Pembayaran: {$total}";
                                    }),
                            ]),
                    ]);
    }

    protected static function validateBookingTime(Forms\Get $get, Closure $fail): void
    {
        $jamMulai = Carbon::parse($get('jam_mulai'));
        $jamSelesai = Carbon::parse($get('jam_selesai'));
        $now = Carbon::now();
        $selectedDate = Carbon::parse($get('tanggal_booking'));

        // Validasi untuk booking hari ini
        if ($selectedDate->isToday()) {
            $minStartTime = $now->addHours(2)->format('H:00');
            
            if ($jamMulai->lt($now->addHours(2))) {
                $fail("Minimal booking 2 jam dari sekarang. Jam tersedia mulai {$minStartTime}");
                return;
            }
        }
    
        if ($jamMulai >= $jamSelesai) {
            $fail('Jam selesai harus setelah jam mulai.');
            return;
        }
        
        if ($jamMulai->eq($jamSelesai)) {
            $fail('Jam mulai dan jam selesai tidak boleh sama.');
            return;
        }
    
        if ($jamMulai->diffInMinutes($jamSelesai) < 60) {
            $fail('Minimal booking adalah 1 jam.');
            return;
        }
    
        // Validasi jam operasional studio
        $operationalHours = $get('operational_hours') ?? '09:00 - 21:00';
        [$openTime, $closeTime] = explode('-', $operationalHours);
        $openTime = trim($openTime);
        $closeTime = trim($closeTime);
        
        $openHour = Carbon::parse($openTime);
        $closeHour = Carbon::parse($closeTime);
        
        if ($jamMulai->format('H:i') < $openTime || $jamSelesai->format('H:i') > $closeTime) {
            $fail("Jam booking hanya tersedia antara {$openTime} - {$closeTime}");
            return;
        }
    
        if ($get('studio_id') && $get('tanggal_booking')) {
            $conflicts = Booking::where('studio_id', $get('studio_id'))
                ->whereDate('tanggal_booking', $get('tanggal_booking'))
                ->where(function ($query) use ($jamMulai, $jamSelesai) {
                    $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                        $q->where('jam_mulai', '<', $jamSelesai->format('H:i:s'))
                          ->where('jam_selesai', '>', $jamMulai->format('H:i:s'));
                    });
                })
                ->where('id', '!=', $get('id') ?? 0)
                ->exists();

            if ($conflicts) {
                $fail('Studio sudah dibooking pada jam tersebut. Silakan pilih jam lain.');
            }
        }
    }
    
        protected static function calculateTotal(Forms\Get $get): float
    {
        $hargaPerJam = $get('harga_per_jam');
        $jamMulai = Carbon::parse($get('jam_mulai'));
        $jamSelesai = Carbon::parse($get('jam_selesai'));
        
        if ($hargaPerJam && $jamMulai && $jamSelesai) {
            $totalJam = $jamMulai->diffInHours($jamSelesai);
            return $totalJam * $hargaPerJam;
        }
        
        return 0;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('studio.nama_studio')
                    ->label('Studio')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->status)
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('tanggal_booking')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn ($record) => 
                        Carbon::parse($record->jam_mulai)->format('H:i') . ' - ' . 
                        Carbon::parse($record->jam_selesai)->format('H:i')
                    ),
                
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->color(fn ($record) => match ($record->status) {
                        'confirmed' => 'success',
                        'canceled' => 'danger',
                        default => 'warning',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'canceled' => 'Canceled',
                    ]),
                Tables\Filters\Filter::make('tanggal_booking')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('tanggal_booking', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('tanggal_booking', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil'),
                
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash'),
                
                // Add print receipt action for confirmed bookings
                Tables\Actions\Action::make('printReceipt')
                    ->label('Cetak Struk')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'confirmed')
                    ->url(fn ($record) => BookingResource::getUrl('print-receipt', ['record' => $record->getKey()]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['studio']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'payment' => Pages\PaymentBooking::route('/{record}/payment'),
            'print-receipt' => PrintReceipt::route('/{record}/print-receipt'), // Add receipt route
        ];
    }

}