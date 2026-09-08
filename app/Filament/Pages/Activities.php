<?php

namespace App\Filament\Pages;

use App\Models\Implementation;
use App\Models\ObligationRequest;
use App\Models\Office;
use App\Models\Payment;
use App\Models\PreProcurement;
use App\Models\Procurement;
use App\Models\ProcurementControl;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestControl;
use App\Models\TechnicalWorkingGroup;
use App\Models\User;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Spatie\Activitylog\Models\Activity;

class Activities extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.activities';

    protected static ?string $slug = 'activities';

    #[Url]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $tableFilters = null;

    /**
     * @var ?string
     */
    #[Url]
    public $tableSearch = '';

    #[Url]
    public ?string $tableSortColumn = null;

    #[Url]
    public ?string $tableSortDirection = null;

    public function getTitle(): string
    {
        return 'Activity Log';
    }

    public static function getNavigationLabel(): string
    {
        return 'Activity Log';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getActivityQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->dateTimeTooltip('M d, Y g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('System')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => static::eventLabel($state))
                    ->color(fn (?string $state): string => static::eventColor($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Record')
                    ->state(fn (Activity $record): string => static::subjectSummary($record))
                    ->wrap()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $nested) use ($search): void {
                            $nested->where('subject_type', 'like', "%{$search}%");

                            foreach (static::subjectLabels() as $class => $label) {
                                if (str_contains(mb_strtolower($label), mb_strtolower($search))) {
                                    $nested->orWhere('subject_type', $class);
                                }
                            }
                        });
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->limit(40)
                    ->tooltip(fn (Activity $record): string => (string) $record->description)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('changes_summary')
                    ->label('Changes')
                    ->state(fn (Activity $record): string => static::changesSummary($record))
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Event')
                    ->options(static::eventOptions()),
                Tables\Filters\SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Record type')
                    ->options(static::subjectLabels())
                    ->searchable(),
                Tables\Filters\Filter::make('created_at')
                    ->label('Date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $nested, mixed $date): Builder => $nested->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $nested, mixed $date): Builder => $nested->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (filled($data['from'] ?? null)) {
                            $indicators['from'] = 'From '.$data['from'];
                        }

                        if (filled($data['until'] ?? null)) {
                            $indicators['until'] = 'Until '.$data['until'];
                        }

                        return $indicators;
                    }),
            ], FiltersLayout::AboveContent)
            ->filtersFormColumns([
                'default' => 1,
                'md' => 2,
                'xl' => 4,
            ])
            ->actions([
                Tables\Actions\Action::make('details')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->button()
                    ->color('info')
                    ->slideOver()
                    ->modalWidth('lg')
                    ->modalHeading(fn (Activity $record): string => static::subjectSummary($record).' · '.static::eventLabel($record->event))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Activity $record) => view('filament.pages.partials.activity-log-details', [
                        'activity' => $record,
                        'changes' => static::changeRows($record),
                    ])),
            ])
            ->recordAction('details')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('60s')
            ->searchPlaceholder('Search by user, event, record, or description')
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('No activity logs')
            ->emptyStateDescription('Changes to records will show up here.')
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15)
            ->modelLabel('log')
            ->pluralModelLabel('logs');
    }

    /**
     * @return Builder<Activity>
     */
    protected function getActivityQuery(): Builder
    {
        $query = Activity::query()->with(['causer', 'subject']);
        $user = Auth::user();

        if ($user && ! $user->hasRole('super_admin')) {
            $query->where('causer_id', $user->id);
        }

        return $query;
    }

    /**
     * @return array<string, string>
     */
    public static function subjectLabels(): array
    {
        return [
            Project::class => 'Project',
            PreProcurement::class => 'Pre-Procurement',
            PurchaseRequest::class => 'Purchase Request',
            TechnicalWorkingGroup::class => 'Technical Working Group',
            ProcurementControl::class => 'Procurement Control',
            PurchaseRequestControl::class => 'PR Control',
            Procurement::class => 'Procurement',
            ObligationRequest::class => 'Obligation Request',
            Implementation::class => 'Implementation',
            Payment::class => 'Payment',
            User::class => 'User',
            Office::class => 'Office',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function eventOptions(): array
    {
        return [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'attached' => 'Attached',
            'detached' => 'Detached',
        ];
    }

    public static function eventLabel(?string $event): string
    {
        if (! filled($event)) {
            return '—';
        }

        return static::eventOptions()[$event] ?? ucfirst($event);
    }

    public static function eventColor(?string $event): string
    {
        return match ($event) {
            'created', 'attached' => 'success',
            'updated' => 'info',
            'deleted', 'detached' => 'danger',
            'restored' => 'warning',
            default => 'gray',
        };
    }

    public static function subjectSummary(Activity $record): string
    {
        $label = static::subjectLabels()[$record->subject_type] ?? class_basename((string) $record->subject_type);
        $subject = $record->subject;

        if ($subject instanceof Project) {
            return trim($label.' · '.$subject->code);
        }

        if (is_object($subject) && isset($subject->name) && filled($subject->name)) {
            return $label.' · '.$subject->name;
        }

        if (filled($record->subject_id)) {
            return $label.' #'.$record->subject_id;
        }

        return $label !== '' ? $label : 'Unknown';
    }

    public static function changesSummary(Activity $record): string
    {
        $rows = static::changeRows($record);

        if ($rows === []) {
            return '—';
        }

        $fields = array_slice(array_column($rows, 'field'), 0, 3);
        $extra = count($rows) - count($fields);

        return implode(', ', $fields).($extra > 0 ? ' +'.$extra : '');
    }

    /**
     * @return list<array{field: string, old: string, new: string}>
     */
    public static function changeRows(Activity $record): array
    {
        $properties = $record->properties instanceof Collection
            ? $record->properties->toArray()
            : (array) $record->properties;

        $old = is_array($properties['old'] ?? null) ? $properties['old'] : [];
        $new = is_array($properties['attributes'] ?? null) ? $properties['attributes'] : [];

        if ($new === [] && is_array($properties['new'] ?? null)) {
            $new = $properties['new'];
        }

        $hidden = ['password', 'remember_token'];
        $keys = array_values(array_unique([...array_keys($old), ...array_keys($new)]));
        $rows = [];

        foreach ($keys as $key) {
            if (in_array($key, $hidden, true)) {
                continue;
            }

            $rows[] = [
                'field' => str_replace('_', ' ', (string) $key),
                'old' => static::stringifyChangeValue($old[$key] ?? null),
                'new' => static::stringifyChangeValue($new[$key] ?? null),
            ];
        }

        return $rows;
    }

    public static function stringifyChangeValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE) ?: '—';
        }

        return (string) $value;
    }
}
