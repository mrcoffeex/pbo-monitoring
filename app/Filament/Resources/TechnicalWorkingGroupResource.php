<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicalWorkingGroupResource\Pages;
use App\Filament\Resources\TechnicalWorkingGroupResource\RelationManagers;
use App\Models\Project;
use App\Models\TechnicalWorkingGroup;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TechnicalWorkingGroupResource extends Resource
{
    protected static ?string $model = TechnicalWorkingGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('TWG Review Details')
                    ->columns(1)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->options(
                                Project::get()->mapWithKeys(fn($project) => [
                                    $project->id => ($project->code) . (' - ' . $project->name ?? 'no projects')
                                ])
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('review_date')
                            ->label('Review Date')
                            ->required(),
                        Textarea::make('review_remarks')
                            ->label('Review Remarks')
                            ->rows(3)
                            ->placeholder('e.g. the document is awesome'),
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
                    ->tooltip(fn($record) => $record->project?->name)
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->project?->year . ' - ' . $record->project?->code, position: 'above'),
                TextColumn::make('review_date')
                    ->label('Review')
                    ->dateTime('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('review_remarks')
                    ->label('Review Remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->review_remarks)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->since()
                    ->tooltip(fn($record) => $record->updated_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('center')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date_range')
                    ->label('Review Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn($qq, $d) => $qq->whereDate('review_date', '>=', $d))
                            ->when($data['until'] ?? null, fn($qq, $d) => $qq->whereDate('review_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('info')
                    ->slideOver()
                    ->modalWidth('md'),
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
                            $headers = ['ID', 'Project Code', 'Project Name', 'Project Year', 'Review Date', 'Review Remarks', 'Created By', 'Created At'];

                            // Prepare CSV data
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    $record->project?->year ?? 'N/A',
                                    $record->review_date ? $record->review_date : 'N/A',
                                    $record->review_remarks ?? '',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            // Generate CSV content
                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    // Escape double quotes and wrap in quotes
                                    return '"' . str_replace('"', '""', $value ?? '') . '"';
                                })->join(',');
                            })->join("\n");

                            // Generate filename with timestamp
                            $filename = 'twg_reviews_export_' . now()->format('Y-m-d_His') . '.csv';

                            // Return download response
                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export TWG Reviews to CSV')
                        ->modalDescription('This will export the selected TWG review records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('No TWG Reviews')
            ->emptyStateDescription('Create your first TWG review record.')
            ->emptyStateActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth('md')
                    ->createAnother(false)
                    ->using(function (array $data): TechnicalWorkingGroup {

                        $data['user_id'] = Auth::id();

                        return TechnicalWorkingGroup::create($data);
                    }),
            ])
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function getLabel(): string
    {
        return 'Technical Working Group';
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
            'index' => Pages\ListTechnicalWorkingGroups::route('/'),
        ];
    }
}
