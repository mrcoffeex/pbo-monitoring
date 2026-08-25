<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Project;
use Filament\Support\RawJs;

trait HasYearChartFilter
{
    protected function getFilters(): ?array
    {
        $years = Project::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year): string => (string) $year)
            ->all();

        if ($years === []) {
            $years[] = (string) now()->year;
        }

        return collect($years)
            ->mapWithKeys(fn (string $year): array => [$year => $year])
            ->all();
    }

    protected function selectedYear(): int
    {
        return (int) ($this->filter ?? now()->year);
    }

    /**
     * @return array{default: int, md: int, xl: int}
     */
    protected function halfWidthSpan(): array
    {
        return [
            'default' => 1,
            'md' => 1,
            'xl' => 1,
        ];
    }

    /**
     * @return array{default: int, md: int, xl: int}
     */
    protected function fullWidthSpan(): array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }

    protected function currencyAxisOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.dataset.label ? context.dataset.label + ': ' : '';
                            const value = Number(context.parsed.y ?? context.parsed ?? 0);

                            return label + new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP',
                                maximumFractionDigits: 0,
                            }).format(value);
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => {
                            const amount = Number(value);

                            if (Math.abs(amount) >= 1000000000) {
                                return '₱' + (amount / 1000000000).toFixed(1) + 'B';
                            }

                            if (Math.abs(amount) >= 1000000) {
                                return '₱' + (amount / 1000000).toFixed(1) + 'M';
                            }

                            if (Math.abs(amount) >= 1000) {
                                return '₱' + (amount / 1000).toFixed(0) + 'K';
                            }

                            return '₱' + amount;
                        },
                    },
                },
            },
        }
        JS);
    }

    protected function countAxisOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        }
        JS);
    }

    protected function doughnutOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            maintainAspectRatio: false,
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 14,
                        usePointStyle: true,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.label || '';
                            const value = Number(context.parsed ?? 0);
                            const total = context.dataset.data.reduce((sum, item) => sum + Number(item || 0), 0);
                            const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                            return label + ': ' + value.toLocaleString() + ' (' + percent + '%)';
                        },
                    },
                },
            },
            scales: {
                x: { display: false },
                y: { display: false },
            },
        }
        JS);
    }

    protected function currencyDoughnutOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            maintainAspectRatio: false,
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 14,
                        usePointStyle: true,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.label || '';
                            const value = Number(context.parsed ?? 0);
                            const total = context.dataset.data.reduce((sum, item) => sum + Number(item || 0), 0);
                            const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            const formatted = new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP',
                                maximumFractionDigits: 0,
                            }).format(value);

                            return label + ': ' + formatted + ' (' + percent + '%)';
                        },
                    },
                },
            },
            scales: {
                x: { display: false },
                y: { display: false },
            },
        }
        JS);
    }
}
