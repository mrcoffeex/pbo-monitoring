<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestControlResource\Pages;
use App\Filament\Resources\PurchaseRequestControlResource\RelationManagers;
use App\Models\Project;
use App\Models\PurchaseRequestControl;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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

class PurchaseRequestControlResource extends Resource
{
    protected static ?string $model = PurchaseRequestControl::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('PR Control Details')
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
                        DateTimePicker::make('controlled_date')
                            ->label('Controlled Date & Time')
                            ->required()
                            ->columnSpan(6),
                        TextInput::make('control_number')
                            ->label('PR Control Number')
                            ->minLength(2)
                            ->maxlength(50)
                            ->required()
                            ->placeholder('e.g. 0000')
                            ->columnSpan(6),
                        TextInput::make('amount')
                            ->label('PR Amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(6),
                        Hidden::make('user_id')
                            ->default(fn () => Filament::auth()->id())
                            ->dehydrated(fn ($state, $context) => $context === 'create'),
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
                    ->label('PR Controlled')
                    ->badge()
                    ->color('primary')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('control_number')
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
        return 3;
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
            'create' => Pages\CreatePurchaseRequestControl::route('/create'),
            'edit' => Pages\EditPurchaseRequestControl::route('/{record}/edit'),
        ];
    }
}
