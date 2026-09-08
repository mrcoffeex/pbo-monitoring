<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcurementResource\Pages;
use App\Models\Procurement;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcurementResource extends Resource
{
    protected static ?string $model = Procurement::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Procurement Details')
                    ->columns(12)
                    ->schema([
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                Select::make('project_id')
                                    ->label('Project')
                                    ->options(
                                        Project::get()->mapWithKeys(fn ($project) => [
                                            $project->id => ($project->code).(' - '.$project->name ?? 'no projects'),
                                        ])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(9),
                                TextInput::make('ib_number')
                                    ->label('IB Number')
                                    ->required()
                                    ->maxLength(50)
                                    ->default('C-')
                                    ->columnSpan(3),
                            ]),
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                DatePicker::make('pre_procurement_conference')
                                    ->label('Pre Procurement Conference Date')
                                    ->columnSpan(4),
                                DatePicker::make('pre_bid_conference')
                                    ->label('Pre Bid Conference Date')
                                    ->columnSpan(4),
                                TagsInput::make('bid_opening')
                                    ->label('Bid Opening')
                                    ->placeholder('e.g. 2024-01-01')
                                    ->helperText('You can add up to 3 dates')
                                    ->columnSpan(4),
                            ]),
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                DatePicker::make('ber')
                                    ->label('Bid Evaluation Report (BER)')
                                    ->columnSpan(4),
                                DatePicker::make('post_qua_date')
                                    ->label('Post Qualification Date')
                                    ->columnSpan(4),
                            ]),
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                Textarea::make('remarks')
                                    ->label('Remarks')
                                    ->rows(3)
                                    ->columnSpan(12)
                                    ->placeholder('e.g. the document is awesome'),
                            ]),
                    ]),

                Section::make('Notice of Award Details')
                    ->columns(12)
                    ->schema([
                        DatePicker::make('noa_date_received')
                            ->label('Notice of Award Date Received')
                            ->columnSpan(4),
                        TextInput::make('contract_amount')
                            ->label('Contract Amount')
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(4),
                        TextInput::make('contractor')
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('e.g. John Doe')
                            ->columnSpan(4),
                        TextInput::make('ntp_number')
                            ->label('NTP Number')
                            ->minLength(2)
                            ->maxLength(50)
                            ->placeholder('e.g. 0000')
                            ->columnSpan(4),
                        DatePicker::make('ntp_date')
                            ->label('NTP Date')
                            ->columnSpan(4),
                        TextInput::make('contract_duration')
                            ->label('Contract Duration - Days')
                            ->integer()
                            ->minValue(1)
                            ->placeholder('e.g. 100')
                            ->columnSpan(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('30s') // auto refresh
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
                    ->description(fn ($record) => $record->project?->year.' - '.$record->project?->code, position: 'above')
                    ->url(fn ($record): ?string => ProjectResource::monitoringUrl($record->project)),

                TextColumn::make('ib_number')
                    ->label('IB')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pre_procurement_conference')
                    ->label('Pre-Proc')
                    ->date('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('pre_bid_conference')
                    ->label('Pre-Bid')
                    ->date('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('bid_opening')
                    ->label('Bid Opening')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('noa_date_received')
                    ->label('NOA')
                    ->date('M d, Y')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ntp_number')
                    ->label('NTP No.')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ntp_date')
                    ->label('NTP Date')
                    ->date('M d, Y')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('contract_amount')
                    ->label('Contract Amount')
                    ->numeric(2)
                    ->money('PHP', true)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('contractor')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->contractor)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('contract_duration')
                    ->label('Duration')
                    ->suffix('d')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Progress')
                    ->state(function ($record) {
                        if ($record->ntp_date) {
                            return 'NTP Issued';
                        }
                        if ($record->noa_date_received) {
                            return 'NOA';
                        }
                        if ($record->bid_opening) {
                            return 'Bidding';
                        }
                        if ($record->pre_bid_conference) {
                            return 'Pre-Bid';
                        }
                        if ($record->pre_procurement_conference) {
                            return 'Pre-Proc';
                        }

                        return 'Draft';
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'NTP Issued' => 'success',
                        'NOA' => 'success',
                        'Bidding' => 'warning',
                        'Pre-Bid' => 'info',
                        'Pre-Proc' => 'gray',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Filters\Filter::make('has_ntp')
                    ->label('With NTP')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereNotNull('ntp_number')),

                Tables\Filters\Filter::make('date_range')
                    ->label('NOA Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('noa_date_received', '>=', $d))
                            ->when($data['until'] ?? null, fn ($qq, $d) => $qq->whereDate('noa_date_received', '<=', $d));
                    }),

                Tables\Filters\TrashedFilter::make()
                    ->label('Trashed')
                    ->visible(fn () => in_array(SoftDeletingScope::class, class_uses_recursive(Procurement::class))),
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
                            $headers = ['ID', 'Project Code', 'Project Name', 'IB Number', 'NOA Date', 'NTP Number', 'NTP Date', 'Contract Amount', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    $record->ib_number ?? 'N/A',
                                    $record->noa_date_received ? $record->noa_date_received : 'N/A',
                                    $record->ntp_number ?? 'N/A',
                                    $record->ntp_date ? $record->ntp_date : 'N/A',
                                    $record->contract_amount ?? 'N/A',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"'.str_replace('"', '""', $value ?? '').'"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'procurements_export_'.now()->format('Y-m-d_His').'.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Procurements to CSV')
                        ->modalDescription('This will export the selected procurement records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No Procurements')
            ->emptyStateDescription('Create your first procurement record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    public static function getLabel(): string
    {
        return 'Procurement';
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
        return 5;
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
            'index' => Pages\ListProcurements::route('/'),
            'create' => Pages\CreateProcurement::route('/create'),
            'edit' => Pages\EditProcurement::route('/{record}/edit'),
        ];
    }
}
