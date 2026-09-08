<?php

namespace App\Filament\Resources;

use App\Enums\ProcessStage;
use App\Filament\Resources\PurchaseRequestResource\Pages;
use App\Filament\Support\WorkflowProjectSelect;
use App\Models\Project;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('PR Details')
                    ->columns(12)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->options(fn (): array => Project::optionsForStage(ProcessStage::PurchaseRequest))
                            ->preload()
                            ->searchable()
                            ->required()
                            ->rules([WorkflowProjectSelect::rule(ProcessStage::PurchaseRequest)])
                            ->columnSpanFull(),
                        DatePicker::make('received_date')
                            ->label('Received Date & Time')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('pr_number')
                            ->label('PR Number')
                            ->minLength(2)
                            ->maxlength(50)
                            ->placeholder('e.g. PR00000000')
                            ->columnSpanFull(),
                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('e.g. the document is awesome'),
                    ]),
                Section::make('TWG - Technical Working Group')
                    ->columns(12)
                    ->schema([
                        DatePicker::make('forward_twg_date')
                            ->label('Forwarded to TWG')
                            ->columnSpanFull(),
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
                    ->description(fn ($record) => $record->project?->year.' - '.$record->project?->code, position: 'above')
                    ->url(fn ($record): ?string => ProjectResource::monitoringUrl($record->project)),
                TextColumn::make('received_date')
                    ->label('Received')
                    ->dateTime('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('pr_number')
                    ->label('PR Number')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
                    ->toggleable(),
                TextColumn::make('forward_twg_date')
                    ->label('Fwd TWG')
                    ->dateTime('M d, Y')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
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
                SelectFilter::make('center')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('date_range')
                    ->label('Received Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('received_date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($qq, $d) => $qq->whereDate('received_date', '<=', $d));
                    }),
                Filter::make('forwarded')
                    ->label('Forwarded to TWG')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereNotNull('forward_twg_date')),
            ])
            ->actions([
                EditAction::make()
                    ->button()
                    ->color('info')
                    ->slideOver()
                    ->modalWidth('md'),
                DeleteAction::make()
                    ->button(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_csv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $headers = ['ID', 'Project Code', 'Project Name', 'PR Number', 'Received Date', 'Forward TWG Date', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    $record->pr_number ?? 'N/A',
                                    $record->received_date ? $record->received_date : 'N/A',
                                    $record->forward_twg_date ? $record->forward_twg_date : 'N/A',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"'.str_replace('"', '""', $value ?? '').'"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'purchase_requests_export_'.now()->format('Y-m-d_His').'.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Purchase Requests to CSV')
                        ->modalDescription('This will export the selected purchase request records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document')
            ->emptyStateHeading('No Purchase Requests')
            ->emptyStateDescription('Create your first purchase request record.')
            ->emptyStateActions([
                CreateAction::make()
                    ->slideOver()
                    ->modalWidth('md')
                    ->createAnother(false)
                    ->using(function (array $data): PurchaseRequest {

                        $data['user_id'] = Auth::id();

                        return PurchaseRequest::create($data);
                    }),
            ])
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function getLabel(): string
    {
        return 'Purchase Request';
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
            'index' => Pages\ListPurchaseRequests::route('/'),
        ];
    }
}
