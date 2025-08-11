<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Center;
use App\Models\Project;
use App\Enums\CustomOptions;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Project Details')
                    ->columns(12)
                    ->schema([
                        Grid::make()
                            ->columns(12)
                            ->schema([
                                Select::make('center_id')
                                    ->label('Responsibility Center')
                                    ->options(
                                        Center::all()->mapWithKeys(function ($center) {
                                            return [$center->id => "{$center->code} - {$center->name}"];
                                        })->toArray()
                                    )
                                    ->searchable()
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->columnSpan(6),
                                Select::make('year')
                                    ->label('Calendar Year')
                                    ->options(
                                        collect(range(now()->year, now()->year - 5))
                                            ->mapWithKeys(fn ($year) => [
                                                $year => $year
                                            ])
                                            ->toArray()
                                    )
                                    ->default(now()->year)
                                    ->required()
                                    ->columnSpan(3),
                                Select::make('status')
                                    ->label('Project Status')
                                    ->options([
                                        'approved' => 'approved',
                                        'pending' => 'pending',
                                        'canceled' => 'canceled'
                                    ])
                                    ->required()
                                    ->columnSpan(3),
                            ]),
                    ]),
                Section::make('Amounts')
                    ->columns(12)
                    ->schema([
                        TextInput::make('appropriation')
                            ->label('Appropriation Amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(6),
                        TextInput::make('allotment')
                            ->label('Allotment Amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(6),
                        Hidden::make('user_id')
                            ->default(fn () => Filament::auth()->id())
                            ->dehydrated(fn ($state, $context) => $context === 'create'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('center.code', 'asc')
            ->columns([
                TextColumn::make('id')
                    ->label('Monitoring')
                    ->formatStateUsing(fn ($state, $record) =>
                        '<div class="flex justify-center items-center w-full">
                            <a href="' . ProjectResource::getUrl('monitoring', ['record' => $record]) . '" class="filament-button bg-primary-600 text-white px-2 py-1 text-sm rounded hover:bg-primary-700 transition">
                                View
                            </a>
                        </div>'
                    )
                    ->html(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'canceled' => 'danger',
                        default => 'secondary',
                    })
                    ->extraAttributes(['style' => 'text-transform: uppercase;'])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('center.name')
                    ->label('Project')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->center?->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('center.code')
                    ->label('Res. Center')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('year')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('center.funds')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => CustomOptions::FUNDS[$state] ?? $state),
                TextColumn::make('appropriation')
                    ->numeric()
                    ->prefix('₱ ')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('allotment')
                    ->numeric()
                    ->prefix('₱ ')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('updated_at')
                    ->sortable()
                    ->searchable()
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Action::make('monitor')
                //     ->label('Monitor')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn (Project $record): string => ProjectResource::getUrl('monitoring', ['record' => $record]))
                //     ->color('primary'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'monitoring' => Pages\ProjectMonitoring::route('/{record}/monitoring'),
        ];
    }
}
