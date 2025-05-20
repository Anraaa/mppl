<?php

namespace App\Filament\Client\Pages;

use App\Models\Booking;
use App\Models\Studio;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class StudioAvailability extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.client.pages.studio-availability';
    protected static ?string $slug = 'cek-ketersediaan';
    protected static ?string $navigationLabel = 'Cek Ketersediaan Studio';
    protected static ?string $navigationGroup = 'Layanan Studio Foto';
    protected static ?string $title = 'Cek Ketersediaan Studio';

    public ?int $studioId = null;
    public ?string $selectedDate = null;
    public array $availabilityData = [];
    public array $bookedSlots = [];
    public array $availableSlots = [];

    public function mount()
    {
        $this->form->fill([
            'selectedDate' => now()->format('Y-m-d'),
            'studioId' => null,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('studioId')
                ->label('Pilih Studio')
                ->options(fn () => Studio::query()->pluck('nama_studio', 'id'))
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->handleStudioOrDateChange()),

            DatePicker::make('selectedDate')
                ->label('Pilih Tanggal')
                ->minDate(Carbon::today())
                ->maxDate(now()->addMonths(3))
                ->default(now())
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->handleStudioOrDateChange()),
        ];
    }

    protected function handleStudioOrDateChange(): void
    {
        $formData = $this->form->getState();

        $this->studioId = $formData['studioId'] ?? null;
        $this->selectedDate = $formData['selectedDate'] ?? now()->format('Y-m-d');

        $this->updateAvailability();
    }

    public function updateAvailability(): void
    {
        $this->availabilityData = [];
        $this->bookedSlots = [];
        $this->availableSlots = [];

        if (!$this->studioId || !$this->selectedDate) {
            return;
        }

        $studio = Studio::find($this->studioId);
        if (!$studio) return;

        $bookings = Booking::where('studio_id', $this->studioId)
            ->whereDate('tanggal_booking', $this->selectedDate)
            ->where('status', '!=', 'canceled')
            ->get();

        $openingTime = Carbon::createFromFormat('H:i:s', $studio->jam_buka ?? '09:00:00');
        $closingTime = Carbon::createFromFormat('H:i:s', $studio->jam_tutup ?? '21:00:00');
        $currentSlot = $openingTime->copy();

        while ($currentSlot < $closingTime) {
            $slotEnd = $currentSlot->copy()->addHour();
            $slotKey = $currentSlot->format('H:i') . '-' . $slotEnd->format('H:i');

            $isBooked = false;
            $bookingInfo = null;

            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->jam_mulai);
                $bookingEnd = Carbon::parse($booking->jam_selesai);

                if ($currentSlot < $bookingEnd && $slotEnd > $bookingStart) {
                    $isBooked = true;
                    $bookingInfo = [
                        'booking_id' => $booking->id,
                        'user' => $booking->user->name ?? 'Tidak diketahui',
                        'status' => $booking->status,
                        'jam_mulai' => $bookingStart->format('H:i'),
                        'jam_selesai' => $bookingEnd->format('H:i'),
                    ];
                    break;
                }
            }

            $this->availabilityData[$slotKey] = [
                'start' => $currentSlot->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'status' => $isBooked ? 'booked' : 'available',
                'booking' => $bookingInfo,
            ];

            $currentSlot->addHour();
        }

        $this->bookedSlots = array_filter($this->availabilityData, fn($slot) => $slot['status'] === 'booked');
        $this->availableSlots = array_filter($this->availabilityData, fn($slot) => $slot['status'] === 'available');
    }
}
