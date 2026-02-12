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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
                            ->columns(5)
                            ->schema([
                                Select::make('type')
                                    ->label('Project Type')
                                    ->options(CustomOptions::PROJECT_TYPES)
                                    ->required()
                                    ->autofocus(),
                                TextInput::make('code')
                                    ->label('Responsibility Center')
                                    ->maxLength(12)
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->autofocus(),
                                Select::make('funds')
                                    ->label('Source of Funds')
                                    ->options(CustomOptions::FUNDS)
                                    ->multiple()
                                    ->required(),
                                Select::make('year')
                                    ->label('Calendar Year')
                                    ->options(
                                        collect(range(now()->year, now()->year - 5))
                                            ->mapWithKeys(fn($year) => [
                                                $year => $year
                                            ])
                                            ->toArray()
                                    )
                                    ->default(now()->year)
                                    ->required(),
                                Select::make('status')
                                    ->label('Project Status')
                                    ->options(CustomOptions::PROJECT_STATUS)
                                    ->default('approved')
                                    ->required(),

                                Textarea::make('name')
                                    ->label('Project Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->rows(3)
                                    ->columnSpanFull(),
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
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Opt')
                    ->formatStateUsing(function ($state, $record) {
                        return view('partials.project-options', compact('record'))->render();
                    })
                    ->alignCenter()
                    ->html(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'released' => 'success',
                        'unreleased' => 'primary',
                        'canceled' => 'danger',
                        default => 'secondary',
                    })
                    ->extraAttributes(['style' => 'text-transform: uppercase;'])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Project')
                    ->wrap()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->name)
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->year, position: 'above'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => CustomOptions::PROJECT_TYPES[$state] ?? $state)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Res. Center')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('funds')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn($state) => CustomOptions::FUNDS[$state] ?? $state),
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
                TextColumn::make('balance')
                    ->label('Balance')
                    ->numeric()
                    ->prefix('₱ ')
                    ->sortable(false)
                    ->searchable(false),
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
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->sortable()
                    ->searchable()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('type')
                    ->label('Project Type')
                    ->form([
                        Select::make('type')
                            ->label('Select Project Type')
                            ->options(CustomOptions::PROJECT_TYPES)
                            ->placeholder('All Project Types')
                            ->multiple()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['type'])) {
                            $query->where(function (Builder $subQuery) use ($data) {
                                foreach ($data['type'] as $type) {
                                    $subQuery->orWhereJsonContains('type', $type);
                                }
                            });
                        }
                        return $query;
                    }),
                SelectFilter::make('purchase_requests')
                    ->label('Purchase Requests')
                    ->options([
                        'with' => 'With PRs',
                        'without' => 'No PRs',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'with',
                            fn(Builder $query): Builder => $query->whereHas('purchase_requests')
                        )->when(
                            $data['value'] === 'without',
                            fn(Builder $query): Builder => $query->whereDoesntHave('purchase_requests')
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
                            fn(Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNotNull('noa_date_received');
                            })
                        )->when(
                            $data['value'] === 'not_received',
                            fn(Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNull('noa_date_received');
                            })
                        );
                    }),
                SelectFilter::make('ntp')
                    ->label('NTP Status')
                    ->options([
                        'issued' => 'NTP Issued',
                        'not_issued' => 'NTP Not Issued',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'issued',
                            fn(Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNotNull('ntp_number');
                            })
                        )->when(
                            $data['value'] === 'not_issued',
                            fn(Builder $query): Builder => $query->whereHas('procurements', function (Builder $query) {
                                $query->whereNull('ntp_number');
                            })
                        );
                    }),
                SelectFilter::make('year')
                    ->label('Calendar Year')
                    ->options(
                        collect(range(now()->year, now()->year - 5))
                            ->mapWithKeys(fn($year) => [
                                $year => $year
                            ])
                            ->toArray()
                    ),
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'no_contract' => 'No Contract Amount',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'paid',
                            function (Builder $query): Builder {
                                return $query->whereRaw('(SELECT COALESCE(SUM(contract_amount), 0) FROM procurements WHERE procurements.project_id = projects.id) > 0 AND (SELECT COALESCE(SUM(contract_amount), 0) FROM procurements WHERE procurements.project_id = projects.id) <= (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.project_id = projects.id)');
                            }
                        )->when(
                            $data['value'] === 'unpaid',
                            function (Builder $query): Builder {
                                return $query->whereRaw('(SELECT COALESCE(SUM(contract_amount), 0) FROM procurements WHERE procurements.project_id = projects.id) > 0 AND (SELECT COALESCE(SUM(contract_amount), 0) FROM procurements WHERE procurements.project_id = projects.id) > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.project_id = projects.id)');
                            }
                        )->when(
                            $data['value'] === 'no_contract',
                            function (Builder $query): Builder {
                                return $query->whereRaw('(SELECT COALESCE(SUM(contract_amount), 0) FROM procurements WHERE procurements.project_id = projects.id) = 0');
                            }
                        );
                    }),
                SelectFilter::make('obligation')
                    ->label('Obligation Status')
                    ->options([
                        'obligated' => 'Obligated',
                        'unobligated' => 'Unobligated',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'obligated',
                            fn(Builder $query): Builder => $query->whereHas('obligation_requests')
                        )->when(
                            $data['value'] === 'unobligated',
                            fn(Builder $query): Builder => $query->whereDoesntHave('obligation_requests')
                        );
                    }),
                Filter::make('funds')
                    ->label('Fund Source')
                    ->form([
                        Select::make('funds')
                            ->label('Select Fund')
                            ->options(CustomOptions::FUNDS)
                            ->placeholder('All Funds')
                            ->multiple()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            !empty($data['funds']),
                            function (Builder $query) use ($data) {
                                return $query->whereJsonContains('funds', $data['funds'], 'or');
                            }
                        );
                    }),
                Filter::make('latest_implementation_percentage')
                    ->label('Latest Implementation Percentage')
                    ->form([
                        Select::make('percentage_range')
                            ->label('Range')
                            ->options([
                                '0-25' => '0% - 25%',
                                '25-50' => '25% - 50%',
                                '50-75' => '50% - 75%',
                                '75-100' => '75% - 100%',
                            ])
                            ->placeholder('All Percentages')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['percentage_range'])) {
                            return $query;
                        }

                        return $query->whereHas('implementations', function (Builder $query) use ($data) {
                            $query->whereRaw('implementations.id = (SELECT id FROM implementations WHERE project_id = projects.id ORDER BY date DESC LIMIT 1)');

                            match ($data['percentage_range']) {
                                '0-25' => $query->whereRaw('percentage BETWEEN 0 AND 25'),
                                '25-50' => $query->whereRaw('percentage BETWEEN 25 AND 50'),
                                '50-75' => $query->whereRaw('percentage BETWEEN 50 AND 75'),
                                '75-100' => $query->whereRaw('percentage BETWEEN 75 AND 100'),
                                default => null,
                            };

                            return $query;
                        });
                    }),
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
                            $headers = ['ID', 'Year', 'Code', 'Project Name', 'Appropriation', 'Allotment', 'Balance', 'Status', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->year ?? 'N/A',
                                    $record->code ?? 'N/A',
                                    $record->name ?? 'N/A',
                                    $record->appropriation ?? 'N/A',
                                    $record->allotment ?? 'N/A',
                                    $record->balance ?? 'N/A',
                                    $record->status ?? 'N/A',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"' . str_replace('"', '""', $value ?? '') . '"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'projects_export_' . now()->format('Y-m-d_His') . '.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Projects to CSV')
                        ->modalDescription('This will export the selected project records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateHeading('No Projects')
            ->emptyStateDescription('Create your first project record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
