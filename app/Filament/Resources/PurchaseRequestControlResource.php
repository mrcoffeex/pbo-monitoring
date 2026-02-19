<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestControlResource\Pages;
use App\Filament\Resources\PurchaseRequestControlResource\RelationManagers;
use App\Models\Project;
use App\Models\PurchaseRequestControl;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

class PurchaseRequestControlResource extends Resource
{
    protected static ?string $model = PurchaseRequestControl::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('PR Control Details')
                    ->columns(1)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->options(
                                Project::get()->mapWithKeys(fn ($project) => [
                                    $project->id => ($project->code) . (' - ' . $project->name ?? 'no projects')
                                ])
                            )
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('amount', null);
                                    return;
                                }

                                $project = Project::find($state);
                                $allotment = $project?->allotment;

                                $value = is_numeric($allotment) ? (float) $allotment : (float) preg_replace('/[^0-9\.\-]/', '', (string) $allotment ?: 0);

                                $set('amount', number_format($value, 2));
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('controlled_date')
                            ->label('Controlled Date')
                            ->required(),
                        TextInput::make('control_number')
                            ->label('PR Control Number')
                            ->minLength(2)
                            ->maxlength(50)
                            ->required()
                            ->placeholder('e.g. 0000'),
                        TextInput::make('amount')
                            ->label('PR Amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
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
                    ->color('gray')
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
                TextColumn::make('controlled_date')
                    ->label('Controlled')
                    ->dateTime('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('control_number')
                    ->label('Control #')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->money('PHP', true)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
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
                Tables\Filters\Filter::make('date_range')
                    ->label('Controlled Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('controlled_date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($qq, $d) => $qq->whereDate('controlled_date', '<=', $d));
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
                            $headers = ['ID', 'Project Code', 'Project Name', 'Control Number', 'Controlled Date', 'Amount', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    $record->control_number ?? 'N/A',
                                    $record->controlled_date ? $record->controlled_date : 'N/A',
                                    $record->amount ?? 'N/A',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"' . str_replace('"', '""', $value ?? '') . '"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'pr_controls_export_' . now()->format('Y-m-d_His') . '.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export PR Controls to CSV')
                        ->modalDescription('This will export the selected purchase request control records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateHeading('No PR Controls')
            ->emptyStateDescription('Create your first purchase request control record.')
            ->emptyStateActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth('md')
                    ->createAnother(false)
                    ->using(function (array $data): PurchaseRequestControl {

                        $data['user_id'] = Auth::id();

                        return PurchaseRequestControl::create($data);
                    }),
            ])
            ->paginated([15,25,50,100])
            ->defaultPaginationPageOption(15);
    }

    public static function getLabel(): string
    {
        return 'Purchase Request Control';
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
        return 4;
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
            'index' => Pages\ListPurchaseRequestControls::route('/'),
        ];
    }
}
