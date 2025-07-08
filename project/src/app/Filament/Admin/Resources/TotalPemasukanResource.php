<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TotalPemasukanResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Exports\MonthlyIncomeExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TotalPemasukanResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Pemasukan';

    protected static ?string $modelLabel = 'Laporan Pemasukan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $slug = 'laporan-pemasukan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Filter form
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_booking', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_booking')
                    ->label('Tanggal Booking')
                    ->date('d F Y')
                    ->sortable()
                    ->description(fn (Booking $record) => $record->tanggal_booking->translatedFormat('l')),
                
                Tables\Columns\TextColumn::make('studio.nama_studio')
                    ->label('Studio')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('time_range')
                    ->label('Waktu Booking')
                    ->formatStateUsing(fn (Booking $record) => $record->jam_mulai.' - '.$record->jam_selesai),
                
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Total Bayar')
                    ->numeric()
                    ->money('IDR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total')
                    ]),
                
                Tables\Columns\TextColumn::make('payments.status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'waiting_verification' => 'warning',
                        'pending' => 'gray',
                        'failed' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('payments.metode')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('studio')
                    ->relationship('studio', 'nama_studio')
                    ->label('Filter Studio')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('tanggal_booking')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '<=', $date),
                            );
                    }),
                
                Tables\Filters\Filter::make('verified_payments')
                    ->label('Hanya Pembayaran Terverifikasi')
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->whereHas('payments', function($q) {
                        $q->where('status', 'verified');
                    })),
                
                Tables\Filters\Filter::make('bulan_tahun')
                    ->label('Filter Bulanan')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->options([
                                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->default(now()->month)
                            ->label('Bulan'),
                        Forms\Components\Select::make('tahun')
                            ->options(function() {
                                $years = [];
                                $startYear = 2020;
                                $endYear = now()->year;
                                
                                for ($i = $startYear; $i <= $endYear; $i++) {
                                    $years[$i] = $i;
                                }
                                
                                return $years;
                            })
                            ->default(now()->year)
                            ->label('Tahun'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['bulan'],
                                fn (Builder $query, $month): Builder => $query->whereMonth('tanggal_booking', $month),
                            )
                            ->when(
                                $data['tahun'],
                                fn (Builder $query, $year): Builder => $query->whereYear('tanggal_booking', $year),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('total_income')
                    ->label('Total Pemasukan')
                    ->modalHeading('Ringkasan Pemasukan')
                    ->modalDescription(function (Tables\Table $table) {
                        $query = static::getEloquentQuery();
                        foreach ($table->getFilters() as $filter) {
                            $filter->applyToBaseQuery($query);
                        }
                        
                        $total = $query->sum('total_bayar');
                        $count = $query->count();
                        $average = $count > 0 ? $total / $count : 0;
                        
                        return 'Total pemasukan dari booking confirmed dengan pembayaran terverifikasi: ' . Number::currency($total, 'IDR');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->color('success')
                    ->icon('heroicon-o-chart-bar'),
                
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options([
                                'monthly' => 'Bulanan',
                                'custom' => 'Periode Kustom',
                            ])
                            ->default('monthly')
                            ->live()
                            ->label('Jenis Export'),
                        
                        Forms\Components\Select::make('bulan')
                            ->options([
                                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->default(now()->month)
                            ->label('Bulan')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'monthly'),
                        
                        Forms\Components\Select::make('tahun')
                            ->options(function() {
                                $years = [];
                                for ($i = 2020; $i <= now()->year; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(now()->year)
                            ->label('Tahun')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'monthly'),
                        
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth())
                            ->visible(fn (Forms\Get $get) => $get('type') === 'custom'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth())
                            ->visible(fn (Forms\Get $get) => $get('type') === 'custom'),
                    ])
                    ->action(function (array $data): BinaryFileResponse {
                        $fileName = $data['type'] === 'monthly' 
                            ? 'laporan-pemasukan-' . $data['bulan'] . '-' . $data['tahun'] . '.xlsx'
                            : 'laporan-pemasukan-' . $data['start_date']->format('Y-m-d') . '-to-' . $data['end_date']->format('Y-m-d') . '.xlsx';

                        return Excel::download(
                            new MonthlyIncomeExport($data['bulan'], $data['tahun']),
                            $fileName
                        );
                    }),
            ])
            ->deferFilters();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'confirmed')
            ->whereHas('payments', function($query) {
                $query->where('status', 'verified');
            })
            ->with(['studio', 'user', 'payments'])
            ->orderBy('tanggal_booking', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTotalPemasukans::route('/'),
            'create' => Pages\CreateTotalPemasukan::route('/create'),
            'edit' => Pages\EditTotalPemasukan::route('/{record}/edit'),
        ];
    }
}