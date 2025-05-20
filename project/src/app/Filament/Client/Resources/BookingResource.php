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
use App\Services\BookingConflictService; // Ensure this is the correct namespace for BookingConflictService
use Closure;


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
                                                if ($get('has_conflict')) {
                                                    $fail('Studio sudah dibooking pada jam tersebut');
                                                }
                                            };
                                        },
                                    ]),

                                Forms\Components\Hidden::make('has_conflict')
                                    ->default(false),

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
                                                $state // jam_selesai yang baru
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
                                    //->nustatic::checkBookingConflicts($get, $livewire);meric()
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
                ->visible(fn ($record) => $record && $record->payment_status === 'pending')
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
            
                // Validasi dasar waktu
                if ($jamMulai >= $jamSelesai) {
                    $fail('Jam selesai harus setelah jam mulai.');
                    return;
                }
                
                // Tambahkan validasi jika jam_mulai dan jam_selesai sama
                if ($jamMulai->eq($jamSelesai)) {
                    $fail('Jam mulai dan jam selesai tidak boleh sama.');
                    return;
                }
            
                // Validasi minimal booking 1 jam
                if ($jamMulai->diffInMinutes($jamSelesai) < 60) {
                    $fail('Minimal booking adalah 1 jam.');
                    return;
                }
            
                // Validasi jam operasional studio (contoh: 08:00-22:00)
                if ($jamMulai->format('H:i') < '09:00' || $jamSelesai->format('H:i') > '21:00') {
                    $fail('Jam booking hanya tersedia antara 09:00 - 21:00.');
                    return;
                }
            
                if ($get('studio_id') && $get('tanggal_booking')) {
                    // Pastikan jamMulai dan jamSelesai adalah objek Carbon
                    $jamMulai = Carbon::parse($get('jam_mulai'));
                    $jamSelesai = Carbon::parse($get('jam_selesai'));
                
                    // Debug log untuk memeriksa waktu yang dimasukkan
                    \Log::info('Jam Mulai: ' . $jamMulai);
                    \Log::info('Jam Selesai: ' . $jamSelesai);
                
                    // Cek apakah ada booking yang tumpang tindih
                    $conflicts = Booking::where('studio_id', $get('studio_id'))
                    ->whereDate('tanggal_booking', $get('tanggal_booking')) // Gunakan whereDate untuk tanggal saja
                    ->where(function ($query) use ($jamMulai, $jamSelesai) {
                        $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                            $q->where('jam_mulai', '<', $jamSelesai->format('H:i:s'))
                              ->where('jam_selesai', '>', $jamMulai->format('H:i:s'));
                        });
                    })
                    ->where('id', '!=', $get('id') ?? 0)
                    ->exists();

                    \Log::info('Checking conflicts for:', [
                        'studio_id' => $get('studio_id'),
                        'tanggal_booking' => $get('tanggal_booking'),
                        'jam_mulai' => $jamMulai->format('Y-m-d H:i:s'),
                        'jam_selesai' => $jamSelesai->format('Y-m-d H:i:s'),
                        'existing_bookings' => Booking::where('studio_id', $get('studio_id'))
                            ->whereDate('tanggal_booking', $get('tanggal_booking'))
                            ->get(['jam_mulai', 'jam_selesai'])
                            ->toArray()
                    ]);


// Log hasil query
\Log::info('Conflicts found: ', ['conflicts' => $conflicts]);

if ($conflicts) {
    $fail('Studio sudah dibooking pada jam tersebut. Silakan pilih jam lain.');
}

                }


            }
            
            
    protected static function checkBookingConflicts(Forms\Get $get, $livewire): void
    {
        if (!$get('studio_id') || !$get('tanggal_booking') || !$get('jam_mulai') || !$get('jam_selesai')) {
            return;
        }
    
        $jamMulai = Carbon::parse($get('jam_mulai'));
        $jamSelesai = Carbon::parse($get('jam_selesai'));
    
        // Validasi waktu dasar
        if ($jamMulai >= $jamSelesai) {
            $livewire->dispatch('notify', [
                'type' => 'error',
                'message' => 'Jam selesai harus setelah jam mulai.'
            ]);
            return;
        }
        
        // Cek jika jam_mulai == jam_selesai
        if ($jamMulai == $jamSelesai) {
            $livewire->dispatch('notify', [
                'type' => 'error',
                'message' => 'Jam mulai dan jam selesai tidak boleh sama.'
            ]);
            return;
        }
    
        // Cek konflik booking di studio yang sama pada tanggal yang sama
        $conflicts = Booking::where('studio_id', $get('studio_id'))
            ->where('tanggal_booking', $get('tanggal_booking'))
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    // Kondisi 1: Booking baru dimulai selama booking yang ada
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->where('id', '!=', $get('id') ?? 0)
            ->get();
    
        if ($conflicts->isNotEmpty()) {
            $conflictTimes = $conflicts->map(function ($booking) {
                return Carbon::parse($booking->jam_mulai)->format('H:i') . ' - ' . 
                       Carbon::parse($booking->jam_selesai)->format('H:i');
            })->join(', ');
            
            $livewire->dispatch('notify', [
                'type' => 'error',
                'message' => "Studio tidak tersedia pada jam: {$conflictTimes}"
            ]);
        }
    }
    
    // Calculates the total price for booking based on studio pricing
// Ubah fungsi calculateTotal menjadi:
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



    // Define pages like create, edit, list etc.
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
                    ->icon('heroicon-o-trash')
                    //->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    //->visible(fn ($records) => $records->where('status', 'pending')->count() > 0),
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
        return [
            //
        ];
    }

    public static function canViewAny(): bool
{
    return true; // paksa tampil
}

public static function canCreate(): bool
{
    return true;
}


public static function shouldRegisterNavigation(): bool
{
    return true; // paksa tampil
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'payment' => Pages\PaymentBooking::route('/{record}/payment'),
        ];
    }
}
