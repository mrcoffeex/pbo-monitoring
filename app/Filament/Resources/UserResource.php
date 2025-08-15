<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Symfony\Contracts\Service\Attribute\Required;

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
                        ->maxLength(17)
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
                        ->multiple()
                        ->preload()
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
                        ->columnSpan(6),
                    TextInput::make('passwordConfirmation')
                        ->password()
                        ->label('Confirm Password')
                        ->required(fn (string $context) => $context === 'create')
                        ->dehydrated(false)
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
                    ->badge()
                    ->color('primary')
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
                    ->color('info')
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
                Tables\Actions\ViewAction::make()->modalHeading('User Details'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = collect([
                                ['ID','Name','Email','Roles','Verified'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    $r->name,
                                    $r->email,
                                    $r->roles->pluck('name')->join('|'),
                                    $r->email_verified_at,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=users.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No Users')
            ->emptyStateDescription('Create your first user account.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([25,50,100])
            ->defaultPaginationPageOption(25);
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
        return 'System';
    }

    public static function getNavigationSort(): int
    {
        return 1;
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
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
