<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObligationRequestResource\Pages;
use App\Filament\Resources\ObligationRequestResource\RelationManagers;
use App\Models\ObligationRequest;
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

class ObligationRequestResource extends Resource
{
    protected static ?string $model = ObligationRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('OBR Details')
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
                            ]),
                        Grid::make('')
                            ->columns(12)
                            ->schema([
                                DatePicker::make('controlled_date')
                                    ->label('OBR Date')
                                    ->required()
                                    ->columnSpan(4),
                                TextInput::make('number')
                                    ->label('OBR Number')
                                    ->minLength(1)
                                    ->maxLength(50)
                                    ->required()
                                    ->placeholder('e.g. 0000')
                                    ->columnSpan(4),
                                TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₱')
                                    ->minValue(0)
                                    ->placeholder('0.00')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->columnSpan(4),
                                Hidden::make('user_id')
                                    ->default(fn () => Filament::auth()->id())
                                    ->dehydrated(fn ($state, $context) => $context === 'create'),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('controlled_date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('project.center.name')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('controlled_date')
                    ->label('OBR Date')
                    ->badge()
                    ->color('primary')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('number')
                    ->label('OBR Number')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->prefix('₱ ')
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
        return 'Obligation Request';
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
            'index' => Pages\ListObligationRequests::route('/'),
            'create' => Pages\CreateObligationRequest::route('/create'),
            'edit' => Pages\EditObligationRequest::route('/{record}/edit'),
        ];
    }
}
