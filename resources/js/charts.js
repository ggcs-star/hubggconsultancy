import Chart from 'chart.js/auto';

// Palette pulled from tailwind.config.js so every chart on the site
// matches the brand/badge colors already used elsewhere in the UI.
const PALETTE = {
    brand: '#7c3aed',
    brandDark: '#6d28d9',
    sky: '#0ea5e9',
    success: '#059669',
    warning: '#d97706',
    danger: '#dc2626',
    secondary: '#475569',
    slate: '#94a3b8',
};

const SET = [PALETTE.brand, PALETTE.sky, PALETTE.success, PALETTE.warning, PALETTE.danger, PALETTE.secondary];

function withAlpha(hex, alpha) {
    const int = parseInt(hex.slice(1), 16);
    const r = (int >> 16) & 255, g = (int >> 8) & 255, b = int & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

const sharedScaleGrid = { color: '#f1f5f9' };
const sharedTicks = { color: '#94a3b8', font: { size: 11 } };

function renderLineChart(canvasId, labels, data, options = {}) {
    const el = document.getElementById(canvasId);
    if (!el) return null;

    return new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: options.label || '',
                data,
                borderColor: PALETTE.brand,
                backgroundColor: withAlpha(PALETTE.brand, 0.1),
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: PALETTE.brand,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: sharedTicks },
                y: { beginAtZero: true, grid: sharedScaleGrid, ticks: { ...sharedTicks, precision: 0 } },
            },
        },
    });
}

function renderBarChart(canvasId, labels, data, options = {}) {
    const el = document.getElementById(canvasId);
    if (!el) return null;

    return new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: options.label || '',
                data,
                backgroundColor: options.color || PALETTE.brand,
                borderRadius: 6,
                maxBarThickness: 36,
            }],
        },
        options: {
            indexAxis: options.horizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: options.horizontal ? sharedScaleGrid : { display: false }, ticks: sharedTicks },
                y: { beginAtZero: true, grid: options.horizontal ? { display: false } : sharedScaleGrid, ticks: { ...sharedTicks, precision: 0 } },
            },
        },
    });
}

function renderDoughnutChart(canvasId, labels, data, options = {}) {
    const el = document.getElementById(canvasId);
    if (!el) return null;

    return new Chart(el, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: options.colors || SET,
                borderColor: '#ffffff',
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#475569', font: { size: 11 }, boxWidth: 10, padding: 12 },
                },
            },
        },
    });
}

window.PSSCharts = { renderLineChart, renderBarChart, renderDoughnutChart, PALETTE, SET };
