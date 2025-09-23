<?php

namespace App\Filament\Resources;

use App\Enums\CustomOptions;
use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Procurement;
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
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                                        $project->id => ($project->code) . (' - ' . $project->name ?? 'no projects')
                                    ])
                                )
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        $set('current_payments', 0);
                                        return;
                                    }

                                    $total = Payment::where('project_id', $state)->sum('amount');
                                    $contract_amount = Procurement::where('project_id', $state)->value('contract_amount');

                                    $total = (float) $total;
                                    $contract_amount = (float) $contract_amount;

                                    $balance = $contract_amount - $total;

                                    $set('current_payments', number_format($total, 2));
                                    $set('balance', number_format($balance, 2));
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

                                TextInput::make('payment_reference')
                                    ->label('Payment Reference')
                                    ->placeholder('Enter payment reference')
                                    ->columnSpan(3),

                                TextInput::make('check_number')
                                    ->label('Check Number')
                                    ->placeholder('Enter check number (if applicable)')
                                    ->columnSpan(3),

                                DatePicker::make('check_date')
                                    ->label('Check Date')
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
                    ->description(fn ($record) => $record->project?->year . ' - ' . $record->project?->code, position: 'above'),
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
                    ->query(fn (Builder $q, array $data) => $q->when($data['type'] ?? null, fn ($qq,$v) => $qq->where('type', $v))),
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
                        ->action(function ($records) {
                            $csv = collect([
                                ['ID','Project','Type','Amount','Date'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    optional($r->project)->name,
                                    CustomOptions::PAYMENTS[$r->type] ?? $r->type,
                                    $r->amount,
                                    $r->date,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=payments.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateHeading('No Payments')
            ->emptyStateDescription('Create your first payment record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([15,25,50,100])
            ->defaultPaginationPageOption(15);
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
