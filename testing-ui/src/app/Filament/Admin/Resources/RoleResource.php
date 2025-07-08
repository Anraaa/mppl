<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z\.\-\_]+$/')
                            ->label('Role Name')
                            ->helperText('Use lowercase with underscores (e.g., "admin", "content_manager")'),
                            
                        Forms\Components\Select::make('guard_name')
                            ->required()
                            ->options([
                                'web' => 'Web',
                                'api' => 'API',
                            ])
                            ->default('web')
                            ->label('Guard Name'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Permissions')
                    ->schema([
                        Forms\Components\Select::make('permissions')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Assigned Permissions')
                            ->helperText('Select permissions to assign to this role')
                            ->options(
                                Permission::all()->pluck('name', 'id')
                            )
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('guard_name')
                                    ->default('web')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->description(fn (Role $record) => $record->guard_name),
                    
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('success')
                    ->limitList(3)
                    ->expandableLimitedList(),
                    
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
                    
                Tables\Filters\Filter::make('has_permissions')
                    ->query(fn ($query) => $query->whereHas('permissions'))
                    ->label('Has Permissions Assigned'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit Role'),
                    
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Role')
                    ->before(function ($record) {
                        // Prevent deletion of admin role
                        if ($record->name === 'admin') {
                            throw new \Exception('The admin role cannot be deleted.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($action, $records) {
                        foreach ($records as $record) {
                            if ($record->name === 'admin') {
                                throw new \Exception('The admin role cannot be deleted.');
                            }
                        }
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->reorderable('name');
    }

    public static function getRelations(): array
    {
        return [
            // You could add relation managers here if needed
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_role');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_role');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_role');
    }

    public static function canDelete($record): bool
    {
        // Prevent deletion of admin role
        if ($record->name === 'super_admin') {
            return false;
        }
        return auth()->user()->can('delete_role');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}