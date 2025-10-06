<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImplementationResource\Pages;
use App\Filament\Resources\ImplementationResource\RelationManagers;
use App\Models\Implementation;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
                                    Project::get()->mapWithKeys(fn ($project) => [
                                        $project->id => ($project->code) . (' - ' . $project->name ?? 'no projects')
                                    ])
                                )
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {

                                    if (!$state) {
                                        $set('current_percentage', null);
                                        $set('start_date', null);
                                        $set('end_date', null);
                                        return;
                                    }

                                    $latest = Implementation::where('project_id', $state)
                                        ->orderBy('date', 'desc')
                                        ->orderBy('id', 'desc')
                                        ->first();

                                    $set('current_percentage', $latest?->percentage ?? 0);

                                    if ($latest?->start_date) {
                                        $set('start_date', Carbon::parse($latest->start_date)->toDateString());
                                    } else {
                                        $set('start_date', null);
                                    }

                                    if ($latest?->end_date) {
                                        $set('end_date', Carbon::parse($latest->end_date)->toDateString());
                                    } else {
                                        $set('end_date', null);
                                    }

                                    if ($latest?->coordinates) {
                                        $set('coordinates', $latest->coordinates);
                                    } else {
                                        $set('coordinates', null);
                                    }
                                })
                                ->columnSpan(6),
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->required()
                                ->columnSpan(3),
                            DatePicker::make('end_date')
                                ->label('Completion Date')
                                ->required()
                                ->columnSpan(3),
                    ]),
                    Grid::make('')
                        ->columns(12)
                        ->schema([
                            DatePicker::make('date')
                                ->label('Date')
                                ->required()
                                ->columnSpan(3),
                            TextInput::make('percentage')
                                ->label('% Complete')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100)
                                ->rules(['nullable', 'numeric', 'between:1,100'])
                                ->suffix('%')
                                ->placeholder('e.g. 50')
                                ->columnSpan(3),
                            TextInput::make('current_percentage')
                                ->label('Current % Complete')
                                ->disabled()
                                ->dehydrated(false)
                                ->suffix('%')
                                ->columnSpan(3),
                            TextInput::make('coordinates')
                                ->label('Coordinates')
                                ->placeholder('e.g. 14.5995, 120.9842')
                                ->helperText('Format: latitude, longitude')
                                ->columnSpan(3),
                            Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(3)
                                ->columnSpan(12)
                                ->placeholder('e.g. the document is awesome'),
                        ]),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('45s')
            ->striped()
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('project.code')
                    ->label('Res. Center')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->wrap()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->project?->name)
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->project?->year . ' - ' . $record->project?->code, position: 'above'),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('M d, Y')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('end_date')
                    ->label('Completion Date')
                    ->date('M d, Y')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
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
                TextColumn::make('coordinates')
                    ->label('Coordinates')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
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
                Tables\Filters\SelectFilter::make('project')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('info'),
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
                            $headers = ['ID', 'Project Code', 'Project Name', 'Date', 'Percentage', 'Remarks', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    $record->date ? $record->date : 'N/A',
                                    $record->percentage ?? 'N/A',
                                    $record->remarks ?? '',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"' . str_replace('"', '""', $value ?? '') . '"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'implementations_export_' . now()->format('Y-m-d_His') . '.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Implementations to CSV')
                        ->modalDescription('This will export the selected implementation records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-arrow-right-end-on-rectangle')
            ->emptyStateHeading('No Implementations')
            ->emptyStateDescription('Create your first implementation record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([15,25,50,100])
            ->defaultPaginationPageOption(15);
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
        return 7;
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
