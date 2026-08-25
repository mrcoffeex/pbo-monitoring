import { useEffect, useMemo, useState } from 'react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

const COLORS = ['#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#6366f1', '#f43f5e', '#84cc16', '#8b5cf6'];
const PIPELINE_COLORS = ['#6366f1', '#22c55e', '#a855f7', '#f59e0b', '#06b6d4'];
const STATUS_COLORS = {
    Released: '#10b981',
    Unreleased: '#3b82f6',
    Canceled: '#f43f5e',
};

function formatPhp(value) {
    const amount = Number(value) || 0;
    const absolute = Math.abs(amount);

    if (absolute >= 1_000_000_000) {
        return `₱${(amount / 1_000_000_000).toFixed(1)}B`;
    }

    if (absolute >= 1_000_000) {
        return `₱${(amount / 1_000_000).toFixed(1)}M`;
    }

    if (absolute >= 1_000) {
        return `₱${(amount / 1_000).toFixed(0)}K`;
    }

    return `₱${amount.toLocaleString('en-PH', { maximumFractionDigits: 0 })}`;
}

function formatFullPhp(value) {
    return `₱${Number(value || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
}

function hasValues(rows, key = 'value') {
    return Array.isArray(rows) && rows.some((row) => Number(row?.[key] || 0) > 0);
}

function useFilamentTheme() {
    const [dark, setDark] = useState(() => document.documentElement.classList.contains('dark'));

    useEffect(() => {
        const observer = new MutationObserver(() => {
            setDark(document.documentElement.classList.contains('dark'));
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });

        return () => observer.disconnect();
    }, []);

    return useMemo(
        () => ({
            dark,
            grid: dark ? '#3f3f3f' : '#e5e7eb',
            tick: dark ? '#a3a3a3' : '#6b7280',
        }),
        [dark],
    );
}

function ChartCard({ title, children, className = '', bodyClassName = 'h-56' }) {
    return (
        <div className={`min-w-0 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900 ${className}`}>
            <h3 className="mb-3 text-sm font-semibold tracking-tight text-gray-950 dark:text-white">{title}</h3>
            <div className={`w-full min-w-0 ${bodyClassName}`}>{children}</div>
        </div>
    );
}

function EmptyState() {
    return (
        <div className="flex h-full items-center justify-center text-xs text-gray-500 dark:text-gray-400">
            No data for this period
        </div>
    );
}

function CurrencyTooltip({ active, payload, label }) {
    if (!active || !payload?.length) {
        return null;
    }

    return (
        <div className="rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs shadow-sm dark:border-gray-700 dark:bg-gray-900">
            {label ? <p className="mb-1 font-medium text-gray-950 dark:text-white">{label}</p> : null}
            {payload.map((entry) => (
                <p key={entry.name} className="text-gray-600 dark:text-gray-300">
                    {entry.name}: {formatFullPhp(entry.value)}
                </p>
            ))}
        </div>
    );
}

function CountTooltip({ active, payload, label }) {
    if (!active || !payload?.length) {
        return null;
    }

    return (
        <div className="rounded-md border border-gray-200 bg-white px-2.5 py-1.5 text-xs shadow-sm dark:border-gray-700 dark:bg-gray-900">
            {label ? <p className="mb-1 font-medium text-gray-950 dark:text-white">{label}</p> : null}
            {payload.map((entry) => (
                <p key={entry.name} className="text-gray-600 dark:text-gray-300">
                    {entry.name}: {Number(entry.value || 0).toLocaleString()}
                </p>
            ))}
        </div>
    );
}

function PieLegend({ items, colors, formatValue = (value) => Number(value || 0).toLocaleString() }) {
    return (
        <ul className="max-h-full space-y-1.5 overflow-y-auto self-center">
            {items.map((item, index) => (
                <li key={item.name} className="flex items-center justify-between gap-2 text-xs text-gray-600 dark:text-gray-300">
                    <span className="flex min-w-0 items-center gap-1.5">
                        <span
                            className="h-2.5 w-2.5 shrink-0 rounded-full"
                            style={{ backgroundColor: colors[index % colors.length] }}
                        />
                        <span className="truncate">{item.name}</span>
                    </span>
                    <span className="shrink-0 font-medium text-gray-950 dark:text-white">{formatValue(item.value)}</span>
                </li>
            ))}
        </ul>
    );
}

function PieWithLegend({ data, colorMap, palette = COLORS, formatValue, currency = false }) {
    const colorList = data.map((entry, index) => colorMap?.[entry.name] ?? palette[index % palette.length]);

    return (
        <div className="grid h-full min-w-0 grid-cols-[minmax(0,1.4fr)_minmax(7rem,0.8fr)] items-center gap-3">
            <div className="h-full min-h-0 min-w-0">
                <PieChart responsive style={{ width: '100%', height: '100%' }}>
                    <Pie
                        data={data}
                        dataKey="value"
                        nameKey="name"
                        cx="50%"
                        cy="50%"
                        innerRadius="52%"
                        outerRadius="88%"
                        paddingAngle={2}
                    >
                        {data.map((entry, index) => (
                            <Cell key={entry.name} fill={colorList[index]} />
                        ))}
                    </Pie>
                    <Tooltip content={currency ? <CurrencyTooltip /> : <CountTooltip />} />
                </PieChart>
            </div>
            <PieLegend items={data} colors={colorList} formatValue={formatValue} />
        </div>
    );
}

export default function DashboardCharts({ data }) {
    const theme = useFilamentTheme();
    const financialOverview = data?.financialOverview ?? [];
    const monthlyPayments = data?.monthlyPayments ?? [];
    const projectStatus = data?.projectStatus ?? [];
    const procurementPipeline = data?.procurementPipeline ?? [];
    const paymentTypes = data?.paymentTypes ?? [];
    const projectTypes = data?.projectTypes ?? [];
    const monthlyImplementations = data?.monthlyImplementations ?? [];

    return (
        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <ChartCard title="Financial overview" className="md:col-span-2 xl:col-span-1">
                {hasValues(financialOverview, 'amount') ? (
                    <BarChart
                        responsive
                        data={financialOverview}
                        style={{ width: '100%', height: '100%' }}
                        margin={{ top: 8, right: 8, left: 4, bottom: 4 }}
                    >
                        <CartesianGrid stroke={theme.grid} strokeDasharray="3 3" vertical={false} />
                        <XAxis dataKey="name" tick={{ fill: theme.tick, fontSize: 11 }} axisLine={false} tickLine={false} />
                        <YAxis
                            width={48}
                            tick={{ fill: theme.tick, fontSize: 11 }}
                            axisLine={false}
                            tickLine={false}
                            tickFormatter={formatPhp}
                        />
                        <Tooltip content={<CurrencyTooltip />} />
                        <Bar dataKey="amount" name="Amount" radius={[4, 4, 0, 0]}>
                            {financialOverview.map((entry, index) => (
                                <Cell key={entry.name} fill={COLORS[index % COLORS.length]} />
                            ))}
                        </Bar>
                    </BarChart>
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Monthly payments">
                {hasValues(monthlyPayments, 'amount') ? (
                    <AreaChart
                        responsive
                        data={monthlyPayments}
                        style={{ width: '100%', height: '100%' }}
                        margin={{ top: 8, right: 8, left: 4, bottom: 4 }}
                    >
                        <defs>
                            <linearGradient id="paymentFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor="#ec4899" stopOpacity={0.28} />
                                <stop offset="95%" stopColor="#ec4899" stopOpacity={0.04} />
                            </linearGradient>
                        </defs>
                        <CartesianGrid stroke={theme.grid} strokeDasharray="3 3" vertical={false} />
                        <XAxis dataKey="month" tick={{ fill: theme.tick, fontSize: 11 }} axisLine={false} tickLine={false} />
                        <YAxis
                            width={48}
                            tick={{ fill: theme.tick, fontSize: 11 }}
                            axisLine={false}
                            tickLine={false}
                            tickFormatter={formatPhp}
                        />
                        <Tooltip content={<CurrencyTooltip />} />
                        <Area
                            type="monotone"
                            dataKey="amount"
                            name="Payments"
                            stroke="#ec4899"
                            fill="url(#paymentFill)"
                            strokeWidth={2}
                        />
                    </AreaChart>
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Project status" bodyClassName="h-72">
                {hasValues(projectStatus) ? (
                    <PieWithLegend data={projectStatus} colorMap={STATUS_COLORS} />
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Procurement pipeline">
                {hasValues(procurementPipeline) ? (
                    <BarChart
                        responsive
                        data={procurementPipeline}
                        layout="vertical"
                        style={{ width: '100%', height: '100%' }}
                        margin={{ top: 8, right: 16, left: 4, bottom: 4 }}
                    >
                        <CartesianGrid stroke={theme.grid} strokeDasharray="3 3" horizontal={false} />
                        <XAxis type="number" tick={{ fill: theme.tick, fontSize: 11 }} axisLine={false} tickLine={false} />
                        <YAxis
                            type="category"
                            dataKey="name"
                            width={88}
                            tick={{ fill: theme.tick, fontSize: 11 }}
                            axisLine={false}
                            tickLine={false}
                        />
                        <Tooltip content={<CountTooltip />} />
                        <Bar dataKey="value" name="Count" radius={[0, 4, 4, 0]}>
                            {procurementPipeline.map((entry, index) => (
                                <Cell key={entry.name} fill={PIPELINE_COLORS[index % PIPELINE_COLORS.length]} />
                            ))}
                        </Bar>
                    </BarChart>
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Payment mix" bodyClassName="h-72">
                {hasValues(paymentTypes) ? (
                    <PieWithLegend data={paymentTypes} palette={COLORS} formatValue={formatPhp} currency />
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Projects by type">
                {hasValues(projectTypes) ? (
                    <BarChart
                        responsive
                        data={projectTypes}
                        layout="vertical"
                        style={{ width: '100%', height: '100%' }}
                        margin={{ top: 8, right: 16, left: 4, bottom: 4 }}
                    >
                        <CartesianGrid stroke={theme.grid} strokeDasharray="3 3" horizontal={false} />
                        <XAxis type="number" tick={{ fill: theme.tick, fontSize: 11 }} axisLine={false} tickLine={false} allowDecimals={false} />
                        <YAxis
                            type="category"
                            dataKey="name"
                            width={108}
                            tick={{ fill: theme.tick, fontSize: 10 }}
                            axisLine={false}
                            tickLine={false}
                        />
                        <Tooltip content={<CountTooltip />} />
                        <Bar dataKey="value" name="Projects" fill="#6366f1" radius={[0, 4, 4, 0]} />
                    </BarChart>
                ) : (
                    <EmptyState />
                )}
            </ChartCard>

            <ChartCard title="Implementations with NTP" className="md:col-span-2 xl:col-span-3">
                {hasValues(monthlyImplementations, 'count') ? (
                    <AreaChart
                        responsive
                        data={monthlyImplementations}
                        style={{ width: '100%', height: '100%' }}
                        margin={{ top: 8, right: 8, left: 4, bottom: 4 }}
                    >
                        <defs>
                            <linearGradient id="implementationFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor="#10b981" stopOpacity={0.28} />
                                <stop offset="95%" stopColor="#10b981" stopOpacity={0.04} />
                            </linearGradient>
                        </defs>
                        <CartesianGrid stroke={theme.grid} strokeDasharray="3 3" vertical={false} />
                        <XAxis dataKey="month" tick={{ fill: theme.tick, fontSize: 11 }} axisLine={false} tickLine={false} />
                        <YAxis
                            width={36}
                            allowDecimals={false}
                            tick={{ fill: theme.tick, fontSize: 11 }}
                            axisLine={false}
                            tickLine={false}
                        />
                        <Tooltip content={<CountTooltip />} />
                        <Area
                            type="monotone"
                            dataKey="count"
                            name="Implementations"
                            stroke="#10b981"
                            fill="url(#implementationFill)"
                            strokeWidth={2}
                        />
                    </AreaChart>
                ) : (
                    <EmptyState />
                )}
            </ChartCard>
        </div>
    );
}
