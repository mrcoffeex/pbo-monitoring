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
                            ->unique(ignoreRecord: true)
                            ->options(
                                Project::with('center')->get()->mapWithKeys(fn ($project) => [
                                    $project->id => ($project->center?->code ? $project->center->code . ' - ' : '') . ($project->center?->name ?? 'Unnamed Center')
                                ])
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
            ->poll('45s')
            ->striped()
            ->defaultSort('controlled_date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('project.center.code')
                    ->label('Center Code')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('project.center.name')
                    ->label('Project')
                    ->wrap()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->project?->center?->name)
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->project?->year, position: 'above'),
                TextColumn::make('controlled_date')
                    ->label('Controlled')
                    ->dateTime('Y-m-d H:i')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('control_number')
                    ->label('Control #')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->money('PHP', true)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
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
                Tables\Filters\SelectFilter::make('center')
                    ->label('Project')
                    ->relationship('project.center', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date_range')
                    ->label('Controlled Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        return $q
                            ->when($data['from'] ?? null, fn ($qq, $d) => $qq->whereDate('controlled_date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($qq, $d) => $qq->whereDate('controlled_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading('PR Control Details'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_csv')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = collect([
                                ['ID','Center','Controlled','Control #','Amount'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    optional($r->project?->center)->name,
                                    $r->controlled_date,
                                    $r->control_number,
                                    $r->amount,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=purchase_request_controls.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateHeading('No PR Controls')
            ->emptyStateDescription('Create your first purchase request control record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([25,50,100])
            ->defaultPaginationPageOption(25);
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
