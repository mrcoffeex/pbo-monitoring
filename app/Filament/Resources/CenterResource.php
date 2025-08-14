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
            ->poll('60s')
            ->striped()
            ->defaultSort('code', 'asc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('funds')
                    ->label('Funds')
                    ->badge()
                    ->formatStateUsing(fn ($state) => CustomOptions::FUNDS[$state] ?? $state)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Responsibility Center')
                    ->wrap()
                    ->limit(75)
                    ->tooltip(fn ($record) => $record->name)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('fund_filter')
                    ->label('Funds')
                    ->options(CustomOptions::FUNDS)
                    ->multiple()
                    ->searchable()
                    ->query(function (Builder $q, array $data) {
                        $values = array_filter($data['values'] ?? []);
                        if (!$values) return $q;
                        return $q->where(function ($qq) use ($values) {
                            foreach ($values as $v) {
                                $qq->orWhereJsonContains('funds', $v);
                            }
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading('Center Details'),
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
                                ['ID','Code','Funds','Name'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    $r->code,
                                    is_array($r->funds) ? implode('|', $r->funds) : $r->funds,
                                    $r->name,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=centers.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateHeading('No Centers')
            ->emptyStateDescription('Create your first responsibility center record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([25,50,100])
            ->defaultPaginationPageOption(25);
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
