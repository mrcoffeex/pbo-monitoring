<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechnicalWorkingGroupResource\Pages;
use App\Filament\Resources\TechnicalWorkingGroupResource\RelationManagers;
use App\Models\Project;
use App\Models\TechnicalWorkingGroup;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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

class TechnicalWorkingGroupResource extends Resource
{
    protected static ?string $model = TechnicalWorkingGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('TWG Review Details')
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
                        DateTimePicker::make('review_date')
                            ->label('Review Date & Time')
                            ->required()
                            ->timezone('Asia/Manila')
                            ->default(now())
                            ->columnSpan(6),
                        Textarea::make('review_remarks')
                            ->label('Review Remarks')
                            ->rows(3)
                            ->placeholder('e.g. the document is awesome')
                            ->columnSpan(12),
                    ]),
                Section::make('TWG Control & Other Details')
                    ->columns(12)
                    ->schema([
                        DateTimePicker::make('controlled_date')
                            ->label('Controlled Date & Time')
                            ->timezone('Asia/Manila')
                            ->columnSpan(6),
                        TextInput::make('abc')
                            ->label('Approved Budget Contract (ABC)')
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(6),
                        Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpan(12)
                            ->placeholder('e.g. the document is awesome'),
                        Hidden::make('user_id')
                            ->default(fn () => Filament::auth()->id())
                            ->dehydrated(fn ($state, $context) => $context === 'create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('project.center.name', 'asc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('project.center.name')
                    ->label('Center')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('review_date')
                    ->label('Review Date')
                    ->badge()
                    ->color('primary')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('review_remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->review_remarks)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('controlled_date')
                    ->label('Controlled Date')
                    ->badge()
                    ->color('primary')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('abc')
                    ->label('ABC')
                    ->numeric()
                    ->prefix('₱ ')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
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
        return 'Technical Working Group';
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
        return 2;
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
            'index' => Pages\ListTechnicalWorkingGroups::route('/'),
            'create' => Pages\CreateTechnicalWorkingGroup::route('/create'),
            'edit' => Pages\EditTechnicalWorkingGroup::route('/{record}/edit'),
        ];
    }
}
