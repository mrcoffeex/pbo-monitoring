<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcurementControlResource\Pages;
use App\Filament\Resources\ProcurementControlResource\RelationManagers;
use App\Models\ProcurementControl;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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

class ProcurementControlResource extends Resource
{
    protected static ?string $model = ProcurementControl::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('TWG Control & Other Details')
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
                            ->columnSpan(9),
                        DatePicker::make('controlled_date')
                            ->label('Controlled Date')
                            ->columnSpan(3),
                        TextInput::make('abc')
                            ->label('Approved Budget Contract (ABC)')
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0)
                            ->placeholder('0.00')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->columnSpan(4),
                        Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpan(8)
                            ->placeholder('e.g. the document is awesome'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->description(fn ($record) => $record->project?->year . ' - ' . $record->project?->code, position: 'above'),
                TextColumn::make('controlled_date')
                    ->label('Controlled')
                    ->dateTime('M d, Y')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('abc')
                    ->label('ABC')
                    ->numeric(2)
                    ->money('PHP', true)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->remarks)
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
                //
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
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document-check')
            ->emptyStateHeading('No PMO Controls')
            ->emptyStateDescription('Create your first PMO Control record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([15,25,50,100])
            ->defaultPaginationPageOption(15);
    }

    public static function getLabel(): string
    {
        return 'PMO Control';
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
            'index' => Pages\ListProcurementControls::route('/'),
            'create' => Pages\CreateProcurementControl::route('/create'),
            'edit' => Pages\EditProcurementControl::route('/{record}/edit'),
        ];
    }
}
