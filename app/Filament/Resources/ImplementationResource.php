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
                                Project::with('center')->get()->mapWithKeys(fn ($project) => [
                                    $project->id => ($project->center?->code ? $project->center->code . ' - ' : '') . ($project->center?->name ?? 'Unnamed Center')
                                ])
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
            ->poll('45s')
            ->striped()
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('project.center.code')
                    ->label('Center Code')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('project.center.name')
                    ->label('Project')
                    ->wrap()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->project?->year, position: 'above'),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('percentage')
                    ->label('% Complete')
                    ->suffix('%')
                    ->formatStateUsing(fn ($state) => is_numeric($state) ? rtrim(rtrim(number_format($state, 2, '.', ''), '0'), '.') : $state)
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 50 => 'warning',
                        default => 'gray'
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->since()
                    ->tooltip(fn ($record) => $record->updated_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('center')
                    ->label('Project')
                    ->relationship('project.center', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($qq, $d) => $qq->whereDate('date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading('Implementation Details'),
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
                                ['ID','Center','Date','Percentage'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    optional($r->project?->center)->name,
                                    $r->date,
                                    $r->percentage,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=implementations.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-arrow-right-end-on-rectangle')
            ->emptyStateHeading('No Implementations')
            ->emptyStateDescription('Create your first implementation record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([25,50,100])
            ->defaultPaginationPageOption(25);
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
        return static::getModel()::count() > 0 ? 'primary' : 'danger';
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
