<?php

namespace App\Filament\Resources;

use App\Enums\CustomOptions;
use App\Filament\Resources\CenterResource\Pages;
use App\Filament\Resources\CenterResource\RelationManagers;
use App\Models\Center;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CenterResource extends Resource
{
    protected static ?string $model = Center::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Responsibility Center Details')
                ->columns(12)
                ->schema([
                    Select::make('funds')
                        ->options(CustomOptions::FUNDS)
                        ->multiple()
                        ->searchable()
                        ->required()
                        ->columnSpan(6),
                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('e.g. 1000')
                        ->columnSpan(6),
                    TextInput::make('name')
                        ->label('Description')
                        ->required()
                        ->placeholder('e.g. Office of the Universe - Contruction of Galaxy Station')
                        ->columnSpan(12),
                ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code', 'asc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                TextColumn::make('funds')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => CustomOptions::FUNDS[$state] ?? $state),
                TextColumn::make('name')
                    ->label('Responsiblity Center')
                    ->limit(69)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable(),
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
        return 'Responsbility Center';
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
            'index' => Pages\ListCenters::route('/'),
            'create' => Pages\CreateCenter::route('/create'),
            'edit' => Pages\EditCenter::route('/{record}/edit'),
        ];
    }
}
