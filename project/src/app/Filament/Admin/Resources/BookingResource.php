<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Filament\Admin\Resources\BookingResource\RelationManagers\PaymentRelationManager;
use App\Filament\Admin\Resources\BookingResource\RelationManagers\PaymentsRelationManager;
use App\Models\Booking;
use App\Models\Studio;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Bookings';
    protected static ?string $modelLabel = 'Booking';
    protected static ?string $slug = 'bookings';
    protected static ?string $navigationGroup = 'Studio Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->required()
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpanFull()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->minLength(8)
                                    ->confirmed(),
                            ]),
                            
                        Forms\Components\Select::make('studio_id')
                            ->label('Studio')
                            ->required()
                            ->options(Studio::query()->active()->pluck('nama_studio', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $studio = Studio::find($state);
                                    $set('total_bayar', $studio?->harga_per_jam ?? 0);
                                }
                            }),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_booking')
                                    ->label('Booking Date')
                                    ->required()
                                    ->native(false)
                                    ->weekStartsOnMonday()
                                    ->minDate(Carbon::today())
                                    ->closeOnDateSelection()
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('jam_mulai', null);
                                        $set('jam_selesai', null);
                                    }),
                                    
                                Forms\Components\TimePicker::make('jam_mulai')
                                    ->label('Start Time')
                                    ->required()
                                    ->native(false)
                                    ->minutesStep(30)
                                    ->seconds(false)
                                    ->suffixIcon('heroicon-o-clock')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $this->updateEndTimeAndTotal($state, $set, $get);
                                    }),
                                    
                                Forms\Components\TimePicker::make('jam_selesai')
                                    ->label('End Time')
                                    ->required()
                                    ->native(false)
                                    ->minutesStep(30)
                                    ->seconds(false)
                                    ->after('jam_mulai')
                                    ->suffixIcon('heroicon-o-clock')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $this->updateTotal($set, $get);
                                    }),
                            ]),
                            
                        Forms\Components\TextInput::make('total_bayar')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->hint(function (Forms\Get $get) {
                                $start = $get('jam_mulai');
                                $end = $get('jam_selesai');
                                $studioId = $get('studio_id');
                                
                                if ($start && $end && $studioId) {
                                    $startTime = Carbon::parse($start);
                                    $endTime = Carbon::parse($end);
                                    $hours = $endTime->floatDiffInHours($startTime);
                                    $studio = Studio::find($studioId);
                                    
                                    if ($studio) {
                                        $total = $hours * $studio->harga_per_jam;
                                        return new HtmlString(
                                            "{$hours} hours × Rp " . number_format($studio->harga_per_jam, 0, ',', '.') . 
                                            "/hour = <strong>Rp " . number_format($total, 0, ',', '.') . "</strong>"
                                        );
                                    }
                                }
                                return null;
                            }),
                            
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'canceled' => 'Canceled',
                            ])
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->live(),
                            
                        Forms\Components\Textarea::make('catatan')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->maxLength(500)
                            ->rows(3),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Placeholder::make('payment_status')
                            ->label('Current Payment Status')
                            ->content(function (?Booking $record) {
                                if (!$record) return 'No payment information';
                                
                                $payment = $record->payments()->latest()->first();
                                if (!$payment) return 'No payment record';
                                
                                $status = ucfirst($payment->status);
                                $method = $payment->metode_pembayaran ? "({$payment->metode_pembayaran})" : '';
                                $amount = $payment->jumlah ? " - Rp " . number_format($payment->jumlah, 0, ',', '.') : '';
                                
                                return "{$status} {$method} {$amount}";
                            }),
                            
                        Forms\Components\Placeholder::make('payment_actions')
                            ->label('Payment Actions')
                            ->visible(fn (?Booking $record) => $record && $record->payments()->exists())
                            ->content(function (?Booking $record) {
                                return new HtmlString(
                                    '<div class="flex space-x-2">'.
                                   // '<a href="'.route('filament.admin.resources.payments.edit', $record->payments()->latest()->first()->id).'" class="text-primary-600 hover:text-primary-800">View Payment</a>'.
                                    '</div>'
                                );
                            }),
                    ])
                    ->hiddenOn('create')
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->tooltip('Click to copy')
                    ->description(fn (Booking $record) => $record->created_at->format('M d, Y H:i')),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Booking $record) => $record->user->email)
                    ->url(fn (Booking $record) => route('filament.admin.resources.users.edit', $record->user_id)),
                    
                Tables\Columns\TextColumn::make('studio.nama_studio')
                    ->label('Studio')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Booking $record) => 'Rp ' . number_format($record->studio->harga_per_jam, 0, ',', '.') . '/hour')
                    ->url(fn (Booking $record) => route('filament.admin.resources.studios.edit', $record->studio_id)),
                    
                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Date & Time')
                    ->state(function (Booking $record) {
                        return Carbon::parse($record->tanggal_booking)->format('M d, Y') . ' • ' .
                               Carbon::parse($record->jam_mulai)->format('g:i A') . ' - ' .
                               Carbon::parse($record->jam_selesai)->format('g:i A');
                    })
                    ->description(fn (Booking $record) => $record->getDurationInHours() . ' hours')
                    ->sortable(['tanggal_booking', 'jam_mulai']),
                    
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Amount')
                    ->numeric()
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'canceled',
                        'info' => 'completed',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'confirmed',
                        'heroicon-o-x-circle' => 'canceled',
                        'heroicon-o-flag' => 'completed',
                    ])
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->state(function (Booking $record) {
                        $payment = $record->payments()->latest()->first();
                        return $payment ? $payment->status : 'unpaid';
                    })
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'waiting_verification',
                        'success' => 'verified',
                        'danger' => 'failed',
                        'gray' => 'unpaid',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-arrow-path' => 'waiting_verification',
                        'heroicon-o-check' => 'verified',
                        'heroicon-o-x-mark' => 'failed',
                        'heroicon-o-banknotes' => 'unpaid',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->multiple()
                    ->label('Booking Status'),
                    
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'verified' => 'Verified',
                        'failed' => 'Failed',
                        'unpaid' => 'Unpaid',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['values'])) {
                            return $query;
                        }
                        
                        return $query->whereHas('payments', function($q) use ($data) {
                            $q->whereIn('status', $data['values']);
                        })->orWhereDoesntHave('payments', function($q) use ($data) {
                            $q->whereIn('status', $data['values']);
                        }, 'and', in_array('unpaid', $data['values']));
                    })
                    ->label('Payment Status'),
                    
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('to')
                            ->label('To Date')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_booking', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['to'] ?? null) {
                            $indicators['to'] = 'Until ' . Carbon::parse($data['to'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Show Upcoming Bookings Only')
                    ->query(fn (Builder $query): Builder => $query->whereDate('tanggal_booking', '>=', now()))
                    ->default(),
                    
                Tables\Filters\SelectFilter::make('studio_id')
                    ->label('Filter by Studio')
                    ->options(fn () => Studio::active()->pluck('nama_studio', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('confirm')
                        ->action(fn (Booking $record) => $record->update(['status' => 'confirmed']))
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Booking')
                        ->modalDescription('Are you sure you want to confirm this booking?')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->hidden(fn (Booking $record) => $record->status !== 'pending'),
                        
                    Tables\Actions\Action::make('cancel')
                        ->action(fn (Booking $record) => $record->update(['status' => 'canceled']))
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Booking')
                        ->modalDescription('Are you sure you want to cancel this booking?')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->hidden(fn (Booking $record) => $record->status === 'canceled' || $record->status === 'completed'),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn (Booking $record) => $record->status === 'completed'),
                ])
                ->dropdown(false)
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm')
                        ->action(fn ($records) => $records->each->update(['status' => 'confirmed']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-check')
                        ->color('success'),
                        
                    Tables\Actions\BulkAction::make('cancel')
                        ->action(fn ($records) => $records->each->update(['status' => 'canceled']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-x-mark')
                        ->color('danger'),
                        
                    Tables\Actions\BulkAction::make('complete')
                        ->action(fn ($records) => $records->each->update(['status' => 'completed']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->icon('heroicon-o-flag')
                        ->color('info'),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('tanggal_booking', 'desc')
            ->persistFiltersInSession()
            ->deferLoading()
            ->groups([
                Tables\Grouping\Group::make('tanggal_booking')
                    ->label('Booking Date')
                    ->date()
                    ->collapsible(),
                    
                Tables\Grouping\Group::make('status')
                    ->label('Status')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            //'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }

    protected function updateEndTimeAndTotal($state, Forms\Set $set, Forms\Get $get): void
    {
        if ($state && $get('studio_id')) {
            $startTime = Carbon::parse($state);
            $defaultEndTime = $startTime->copy()->addHours(1);
            $set('jam_selesai', $defaultEndTime->format('H:i'));
            
            $this->updateTotal($set, $get);
        }
    }

    protected function updateTotal(Forms\Set $set, Forms\Get $get): void
    {
        $start = $get('jam_mulai');
        $end = $get('jam_selesai');
        $studioId = $get('studio_id');
        
        if ($start && $end && $studioId) {
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);
            $hours = $endTime->floatDiffInHours($startTime);
            $studio = Studio::find($studioId);
            
            if ($studio) {
                $set('total_bayar', $hours * $studio->harga_per_jam);
            }
        }
    }
}   