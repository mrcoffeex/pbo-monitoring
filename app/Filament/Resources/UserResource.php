<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Details')
                    ->columns(12)
                    ->schema([
                        TextInput::make('name')
                            ->autofocus()
                            ->required()
                            ->minLength(4)
                            ->maxLength(125)
                            ->columnSpan(6),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->minLength(4)
                            ->maxLength(125)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(6),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->required()
                            ->preload()
                            ->multiple()
                            ->searchable()
                            ->columnSpan(6),
                    ]),
                Section::make('Security Details')
                    ->columns(12)
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->label('Password')
                            ->required(fn (string $context) => $context === 'create')
                            ->same('passwordConfirmation') // must match confirmation field
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable()
                            ->columnSpan(6),
                        TextInput::make('passwordConfirmation')
                            ->password()
                            ->label('Confirm Password')
                            ->required(fn (string $context) => $context === 'create')
                            ->dehydrated(false)
                            ->revealable()
                            ->columnSpan(6),
                    ]),
                Hidden::make('email_verified_at')
                    ->default(now())
                    ->dehydrated(fn (string $context) => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->striped()
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->since()
                    ->tooltip(fn ($record) => $record->email_verified_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->multiple(),
                Tables\Filters\Filter::make('verified')
                    ->label('Verified Only')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('info')
                    ->slideOver()
                    ->modalWidth('2xl'),
                Tables\Actions\DeleteAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            // Prepare CSV headers
                            $headers = ['ID', 'Name', 'Email', 'Roles', 'Email Verified At', 'Created At'];

                            // Prepare CSV data
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->name,
                                    $record->email,
                                    $record->roles->pluck('name')->join(', '),
                                    $record->email_verified_at ? $record->email_verified_at->format('Y-m-d H:i:s') : 'Not Verified',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            // Generate CSV content
                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    // Escape double quotes and wrap in quotes
                                    return '"'.str_replace('"', '""', $value ?? '').'"';
                                })->join(',');
                            })->join("\n");

                            // Generate filename with timestamp
                            $filename = 'users_export_'.now()->format('Y-m-d_His').'.csv';

                            // Return download response
                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Users to CSV')
                        ->modalDescription('This will export the selected users to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No Users')
            ->emptyStateDescription('Create your first user account.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->modalWidth('2xl')
                    ->createAnother(false),
            ])
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'primary' : 'danger';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
