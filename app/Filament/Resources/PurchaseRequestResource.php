<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseRequestResource\Pages;
use App\Filament\Resources\PurchaseRequestResource\RelationManagers;
use App\Models\Project;
use App\Models\PurchaseRequest;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseRequestResource extends Resource
{
    protected static ?string $model = PurchaseRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('PR Details')
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
                        DateTimePicker::make('received_date')
                            ->label('Received Date & Time')
                            ->required()
                            ->timezone('Asia/Manila')
                            ->default(now())
                            ->columnSpan(6),
                        TextInput::make('pr_number')
                            ->label('PR Number')
                            ->minLength(2)
                            ->maxlength(50)
                            ->required()
                            ->placeholder('e.g. PR00000000')
                            ->columnSpan(6),
                    ]),
                Section::make('TWG - Technical Working Group')
                    ->columns(12)
                    ->schema([
                        DateTimePicker::make('forward_twg_date')
                            ->label('Forwarded to TWG')
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
            ->defaultSort('created_at', 'desc')
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
                TextColumn::make('received_date')
                    ->label('Received Date')
                    ->badge()
                    ->color('warning')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pr_number')
                    ->label('PR Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('forward_twg_date')
                    ->label('Forward to TWG')
                    ->badge()
                    ->color('warning')
                    ->dateTime()
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
        return 'Purchase Request';
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
        return 1;
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
            'index' => Pages\ListPurchaseRequests::route('/'),
            'create' => Pages\CreatePurchaseRequest::route('/create'),
            'edit' => Pages\EditPurchaseRequest::route('/{record}/edit'),
        ];
    }
}
