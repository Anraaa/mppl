<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyIncomeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Booking::with(['studio', 'user', 'payments'])
            ->where('status', 'confirmed')
            ->whereHas('payments', function($query) {
                $query->where('status', 'verified');
            })
            ->whereMonth('tanggal_booking', $this->month)
            ->whereYear('tanggal_booking', $this->year)
            ->orderBy('tanggal_booking')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Booking',
            'Studio',
            'Nama Pelanggan',
            'Jam Mulai',
            'Jam Selesai',
            'Total Bayar (IDR)',
            'Status Pembayaran',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->tanggal_booking->format('d F Y'),
            $booking->studio->nama_studio,
            $booking->user->name,
            $booking->jam_mulai,
            $booking->jam_selesai,
            $booking->total_bayar,
            $booking->payments->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}