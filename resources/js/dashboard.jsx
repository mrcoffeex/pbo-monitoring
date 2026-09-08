function mountFromDom() {
    const el = document.getElementById("dashboard-charts-root");

    if (!el) {
        return;
    }

    import("./dashboard/mount.jsx").then(({ mountDashboardCharts }) => {
        mountDashboardCharts(el);
    });
}

document.addEventListener("DOMContentLoaded", mountFromDom);
document.addEventListener("livewire:navigated", mountFromDom);

document.addEventListener("livewire:init", () => {
    Livewire.on("dashboard-charts-updated", (event) => {
        const el = document.getElementById("dashboard-charts-root");

        if (!el) {
            return;
        }

        const charts = event?.charts ?? event?.[0]?.charts;

        import("./dashboard/mount.jsx").then(({ mountDashboardCharts }) => {
            mountDashboardCharts(el, charts ?? {});
        });
    });
});
