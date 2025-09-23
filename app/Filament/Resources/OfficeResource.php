<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Filament\Resources\OfficeResource\RelationManagers;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Office Details')
                ->columns(12)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->minLength(2)
                        ->maxLength(50)
                        ->autofocus()
                        ->unique(ignoreRecord: true)
                        ->columnSpan(6),
                    TagsInput::make('emails')
                        ->required()
                        ->columnSpan(6),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->striped()
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('Office')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('emails')
                    ->label('Emails')
                    ->badge()
                    ->wrap()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->emails)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('Y-m-d H:i'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_multiple_email')
                    ->label('Multiple Emails')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereRaw('JSON_LENGTH(emails) > 1')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalHeading('Office Details'),
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
                                ['ID','Office','Emails'],
                            ])->merge(
                                $records->map(fn ($r) => [
                                    $r->id,
                                    $r->name,
                                    is_array($r->emails) ? implode('|', $r->emails) : $r->emails,
                                ])
                            )->map(fn ($row) => implode(',', array_map(fn ($v) => '"'.str_replace('"','""',$v).'"', $row)))->implode("\n");

                            return response($csv)
                                ->withHeaders([
                                    'Content-Type' => 'text/csv',
                                    'Content-Disposition' => 'attachment; filename=offices.csv',
                                ]);
                        })
                        ->requiresConfirmation()
                        ->color('primary'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('No Offices')
            ->emptyStateDescription('Create your first office record.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->paginated([15,25,50,100])
            ->defaultPaginationPageOption(15);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
