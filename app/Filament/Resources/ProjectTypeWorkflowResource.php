<?php

namespace App\Filament\Resources;

use App\Enums\CustomOptions;
use App\Enums\ProcessStage;
use App\Filament\Resources\ProjectTypeWorkflowResource\Pages;
use App\Models\ProjectTypeWorkflow;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectTypeWorkflowResource extends Resource
{
    protected static ?string $model = ProjectTypeWorkflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_type')
                    ->label('Project Type')
                    ->options(CustomOptions::PROJECT_TYPES)
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'),
                Repeater::make('stages')
                    ->label('Workflow stages')
                    ->simple(
                        Select::make('stage')
                            ->label('Stage')
                            ->options(ProcessStage::options())
                            ->required(),
                    )
                    ->minItems(1)
                    ->reorderable()
                    ->helperText('Existing projects keep the workflow they were created with. Only new projects use these stages.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project_type')
                    ->label('Project Type')
                    ->formatStateUsing(fn (string $state): string => CustomOptions::PROJECT_TYPES[$state] ?? $state)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stages')
                    ->label('Workflow')
                    ->state(fn (ProjectTypeWorkflow $record): array => $record->stageValues())
                    ->formatStateUsing(fn (mixed $state): string => ProcessStage::fromStored($state)?->label() ?? (string) $state)
                    ->badge()
                    ->color(fn (mixed $state): string => ProcessStage::fromStored($state)?->badgeColor() ?? 'primary')
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationLabel(): string
    {
        return 'Project Workflows';
    }

    public static function getModelLabel(): string
    {
        return 'Project Workflow';
    }

    public static function getNavigationSort(): int
    {
        return 20;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProjectTypeWorkflows::route('/'),
        ];
    }
}
