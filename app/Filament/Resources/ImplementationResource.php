<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImplementationResource\Pages;
use App\Filament\Resources\ImplementationResource\RelationManagers;
use App\Models\Implementation;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
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

class ImplementationResource extends Resource
{
    protected static ?string $model = Implementation::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Implementation Details')
                    ->columns(12)
                    ->schema([
                    Grid::make('')
                        ->columns(12)
                        ->schema([
                            Select::make('project_id')
                                ->label('Project')
                                ->options(
                                Project::with('center')->get()->pluck('center.name', 'id')
                            )
                                ->searchable()
                                ->required()
                                ->columnSpan(6),
                        ]),
                    Grid::make('')
                        ->columns(12)
                        ->schema([
                            DatePicker::make('date')
                                ->required()
                                ->columnSpan(4),
                            TextInput::make('percentage')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100)
                                ->rules(['nullable', 'numeric', 'between:1,100'])
                                ->suffix('%')
                                ->placeholder('e.g. 50')
                                ->columnSpan(4),
                            Hidden::make('user_id')
                                ->default(fn () => Filament::auth()->id())
                                ->dehydrated(fn ($state, $context) => $context === 'create'),
                        ]),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('project.center.name')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('percentage')
                    ->suffix('%')
                    ->formatStateUsing(fn ($state) =>
                        is_numeric($state) ? rtrim(rtrim(number_format($state, 2, '.', ''), '0'), '.') : $state
                    )
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color('info')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getLabel(): string
    {
        return 'Implementation';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'warning' : 'danger';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Process';
    }

    public static function getNavigationSort(): int
    {
        return 6;
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
            'index' => Pages\ListImplementations::route('/'),
            'create' => Pages\CreateImplementation::route('/create'),
            'edit' => Pages\EditImplementation::route('/{record}/edit'),
        ];
    }
}
