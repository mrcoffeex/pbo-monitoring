<?php

namespace App\Filament\Resources;

use App\Enums\CustomOptions;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Project;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment Details')
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
                                    ->reactive()
                                    ->afterStateUpdated(function (mixed $state, Set $set, ?Payment $record): void {
                                        static::syncPaymentBalanceFields($state, $set, $record);
                                    })
                                    ->columnSpan(9),

                                DatePicker::make('date')
                                    ->label('Date of Payment')
                                    ->required()
                                    ->columnSpan(3),
                            ]),
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                Select::make('type')
                                    ->label('Type of Payment')
                                    ->options(CustomOptions::PAYMENTS)
                                    ->required()
                                    ->columnSpan(3),

                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₱')
                                    ->minValue(0)
                                    ->placeholder('0.00')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->helperText(function (Get $get, ?Payment $record): ?string {
                                        $project = filled($get('project_id'))
                                            ? Project::query()->find($get('project_id'))
                                            : null;

                                        if ($project === null) {
                                            return 'Cannot exceed the remaining project balance.';
                                        }

                                        return 'Remaining balance: ₱'.number_format($project->remainingPaymentBalance($record), 2);
                                    })
                                    ->rules([
                                        fn (Get $get, ?Payment $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                            $project = filled($get('project_id'))
                                                ? Project::query()->find($get('project_id'))
                                                : null;

                                            if ($project === null) {
                                                return;
                                            }

                                            $amount = round((float) str_replace(',', '', (string) $value), 2);
                                            $remaining = $project->remainingPaymentBalance($record);

                                            if ($amount > $remaining) {
                                                $fail('The payment amount exceeds the remaining balance of ₱'.number_format($remaining, 2).'.');
                                            }
                                        },
                                    ])
                                    ->columnSpan(3),

                                TextInput::make('current_payments')
                                    ->label('Current Total Payments')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('₱')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->minValue(0)
                                    ->columnSpan(3),

                                TextInput::make('balance')
                                    ->label('Total Balance')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->prefix('₱')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->minValue(0)
                                    ->columnSpan(3),

                                TextInput::make('payable_reference')
                                    ->label('Payable Reference')
                                    ->placeholder('Enter payable reference')
                                    ->columnSpan(3),

                                TextInput::make('check_number')
                                    ->label('Check Number')
                                    ->placeholder('Enter check number (if applicable)')
                                    ->columnSpan(3),

                                DatePicker::make('check_date')
                                    ->label('Check Date')
                                    ->columnSpan(3),

                                TextInput::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->placeholder('Enter payment reference')
                                    ->columnSpan(3),
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
                    ->description(fn ($record) => $record->project?->year.' - '.$record->project?->code, position: 'above')
                    ->url(fn ($record): ?string => ProjectResource::monitoringUrl($record->project)),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => CustomOptions::PAYMENTS[$state] ?? $state)
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->money('PHP', true)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payable_reference')
                    ->label('Payable Ref.')
                    ->wrap()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->payable_reference)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('payment_reference')
                    ->label('Payment Ref.')
                    ->wrap()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->payment_reference)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('check_number')
                    ->label('Check No.')
                    ->wrap()
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->check_number)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('check_date')
                    ->label('Check Date')
                    ->date('M d, Y')
                    ->badge()
                    ->color('info')
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
                Tables\Filters\SelectFilter::make('project')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('type_filter')
                    ->label('Type')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->options(CustomOptions::PAYMENTS)
                            ->label('Payment Type'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q->when($data['type'] ?? null, fn ($qq, $v) => $qq->where('type', $v))),
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
                            $headers = ['ID', 'Project Code', 'Project Name', 'Type', 'Amount', 'Date', 'DV Number', 'Check Number', 'Created By', 'Created At'];
                            $csvData = collect([$headers]);

                            foreach ($records as $record) {
                                $csvData->push([
                                    $record->id,
                                    $record->project?->code ?? 'N/A',
                                    $record->project?->name ?? 'N/A',
                                    CustomOptions::PAYMENTS[$record->type] ?? $record->type,
                                    $record->amount ?? 'N/A',
                                    $record->date ? $record->date : 'N/A',
                                    $record->dv_number ?? 'N/A',
                                    $record->check_number ?? 'N/A',
                                    $record->user?->name ?? 'System',
                                    $record->created_at->format('Y-m-d H:i:s'),
                                ]);
                            }

                            $csv = $csvData->map(function ($row) {
                                return collect($row)->map(function ($value) {
                                    return '"'.str_replace('"', '""', $value ?? '').'"';
                                })->join(',');
                            })->join("\n");

                            $filename = 'payments_export_'.now()->format('Y-m-d_His').'.csv';

                            return response()->streamDownload(function () use ($csv) {
                                echo $csv;
                            }, $filename, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Export Payments to CSV')
                        ->modalDescription('This will export the selected payment records to a CSV file.')
                        ->modalSubmitActionLabel('Export')
                        ->color('success')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateHeading('No Payments')
            ->emptyStateDescription('Create your first payment record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function syncPaymentBalanceFields(mixed $projectId, Set $set, ?Payment $record = null): void
    {
        $project = filled($projectId) ? Project::query()->find($projectId) : null;

        $set('current_payments', number_format($project?->paidPaymentTotal($record) ?? 0, 2, '.', ''));
        $set('balance', number_format($project?->remainingPaymentBalance($record) ?? 0, 2, '.', ''));
    }

    public static function getLabel(): string
    {
        return 'Payment';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'danger';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Process';
    }

    public static function getNavigationSort(): int
    {
        return 8;
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
