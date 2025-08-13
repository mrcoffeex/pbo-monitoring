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
use Filament\Tables\Filters\SelectFilter;
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
                    ->label('Opt')
                    ->formatStateUsing(function ($state, $record) {
                        return view('partials.project-options', compact('record'))->render();
                    })
                    ->html(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'pending' => 'primary',
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
                    ->color('primary')
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
                SelectFilter::make('purchase_requests')
                    ->label('Purchase Requests')
                    ->options([
                        'with' => 'With Purchase Requests',
                        'without' => 'No Purchase Requests',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'with',
                            fn (Builder $query): Builder => $query->whereHas('purchase_requests')
                        )->when(
                            $data['value'] === 'without',
                            fn (Builder $query): Builder => $query->whereDoesntHave('purchase_requests')
                        );
                    }),
                SelectFilter::make('noa_received')
                    ->label('NOA Status')
                    ->options([
                        'received' => 'NOA Received',
                        'not_received' => 'NOA Not Received',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'received',
                            fn (Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNotNull('noa_date_received');
                            })
                        )->when(
                            $data['value'] === 'not_received',
                            fn (Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNull('noa_date_received');
                            })
                        );
                    }),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'info' : 'danger';
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
            'pdf-viewer' => Pages\ProjectPdfViewer::route('/pdf-viewer'),
        ];
    }
}
