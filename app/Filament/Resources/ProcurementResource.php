<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcurementResource\Pages;
use App\Filament\Resources\ProcurementResource\RelationManagers;
use App\Models\Procurement;
use App\Models\Project;
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
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;

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
                                Project::with('center')->get()->pluck('center.name', 'id')
                            )
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(6),
                                TextInput::make('ib_number')
                                    ->label('IB Number')
                                    ->autofocus()
                                    ->required()
                                    ->minLength(1)
                                    ->maxLength(50)
                                    ->placeholder('e.g. 0000')
                                    ->columnSpan(4),
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
                            DatePicker::make('bid_opening')
                                ->label('Bid Opening')
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
                                ->placeholder('e.g. the document is awesome')
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
                            ->label('Notice of Award Date')
                            ->columnSpan(4),
                        TextInput::make('contract_duration')
                            ->label('Contract Duration - Days')
                            ->integer()
                            ->minValue(1)
                            ->placeholder('e.g. 100')
                            ->columnSpan(4),
                        Hidden::make('user_id')
                            ->default(fn () => Filament::auth()->id())
                            ->dehydrated(fn ($state, $context) => $context === 'create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('project.center.name')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ib_number')
                    ->label('IB Number')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pre_procurement_conference')
                    ->label('Pre-Procurement Conference')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pre_bid_conference')
                    ->label('Pre-Bid Conference')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('bid_opening')
                    ->label('Bid Opening')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ber')
                    ->label('BER')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('post_qua_date')
                    ->label('Post Qualification Date')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('remarks')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('noa_date_received')
                    ->label('NOA Date Received')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contract_amount')
                    ->label('Contract Amount')
                    ->numeric()
                    ->prefix('₱ ')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contractor')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->contractor)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ntp_number')
                    ->label('NTP Number')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ntp_date')
                    ->label('NTP Date')
                    ->badge()
                    ->color('warning')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('contract_duration')
                    ->label('Contract Duration')
                    ->suffix(' days')
                    ->formatStateUsing(fn ($state) =>
                        is_numeric($state) ? rtrim(rtrim(number_format($state, 2, '.', ''), '0'), '.') : $state
                    )
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
                //
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
            'index' => Pages\ListProcurements::route('/'),
            'create' => Pages\CreateProcurement::route('/create'),
            'edit' => Pages\EditProcurement::route('/{record}/edit'),
        ];
    }
}
