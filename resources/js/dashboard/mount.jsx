import { createRoot } from 'react-dom/client';
import DashboardCharts from './DashboardCharts';

const roots = new WeakMap();

function parseCharts(el, fallback = {}) {
    if (fallback && Object.keys(fallback).length > 0) {
        return fallback;
    }

    try {
        return JSON.parse(el.dataset.charts || '{}');
    } catch {
        return {};
    }
}

export function mountDashboardCharts(el, charts = {}) {
    let root = roots.get(el);

    if (!root) {
        root = createRoot(el);
        roots.set(el, root);
    }

    root.render(<DashboardCharts data={parseCharts(el, charts)} />);
}
