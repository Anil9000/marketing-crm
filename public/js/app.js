/**
 * Marketing CRM — Main Application JavaScript
 * =============================================
 * Provides: Chart.js helpers, AJAX helpers,
 *           Toast notifications, Sidebar toggle,
 *           and general UI utilities.
 */

'use strict';

/* ----------------------------------------------------------
   1. Global CSRF Token Setup
   ---------------------------------------------------------- */
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

/* ----------------------------------------------------------
   2. Chart.js Global Defaults (Dark Theme)
   ---------------------------------------------------------- */
if (typeof Chart !== 'undefined') {
    Chart.defaults.color            = '#64748b';
    Chart.defaults.borderColor      = '#1e2130';
    Chart.defaults.font.family      = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size        = 12;
    Chart.defaults.plugins.legend.labels.boxWidth  = 12;
    Chart.defaults.plugins.legend.labels.padding   = 16;
    Chart.defaults.plugins.tooltip.backgroundColor = '#1a1f2e';
    Chart.defaults.plugins.tooltip.borderColor     = '#2d3347';
    Chart.defaults.plugins.tooltip.borderWidth     = 1;
    Chart.defaults.plugins.tooltip.titleColor      = '#e2e8f0';
    Chart.defaults.plugins.tooltip.bodyColor       = '#94a3b8';
    Chart.defaults.plugins.tooltip.padding         = 10;
    Chart.defaults.plugins.tooltip.cornerRadius    = 8;
}

/**
 * CRM Charts namespace — collection of factory helpers.
 */
const CRMCharts = {
    /**
     * Accent colour palette (matches CSS variables).
     */
    colors: {
        purple: '#6366f1',
        green:  '#10b981',
        red:    '#ef4444',
        yellow: '#f59e0b',
        cyan:   '#06b6d4',
        blue:   '#3b82f6',
    },

    /**
     * Build a gradient fill for line charts.
     * @param {CanvasRenderingContext2D} ctx
     * @param {string} hexColor
     * @returns {CanvasGradient}
     */
    gradient(ctx, hexColor) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, hexColor + '33');
        gradient.addColorStop(1, hexColor + '00');
        return gradient;
    },

    /**
     * Create a line chart with area fill.
     * @param {string}   canvasId
     * @param {string[]} labels
     * @param {object[]} datasets   Each: { label, data, color? }
     * @param {object}   [options]  Chart.js options overrides
     * @returns {Chart|null}
     */
    line(canvasId, labels, datasets, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');

        const builtDatasets = datasets.map((ds, i) => {
            const palette = Object.values(this.colors);
            const color   = ds.color || palette[i % palette.length];
            return {
                label:           ds.label,
                data:            ds.data,
                borderColor:     color,
                backgroundColor: this.gradient(ctx, color),
                borderWidth:     2,
                pointBackgroundColor: color,
                pointRadius:     4,
                pointHoverRadius: 6,
                tension:         0.4,
                fill:            true,
            };
        });

        return new Chart(ctx, {
            type: 'line',
            data: { labels, datasets: builtDatasets },
            options: Object.assign({
                responsive:          true,
                maintainAspectRatio: true,
                interaction: {
                    intersect: false,
                    mode:      'index',
                },
                plugins: {
                    legend: { display: datasets.length > 1 },
                },
                scales: {
                    x: {
                        grid: { color: '#1e2130' },
                        ticks: { color: '#64748b' },
                    },
                    y: {
                        grid:    { color: '#1e2130' },
                        ticks:   { color: '#64748b' },
                        beginAtZero: true,
                    },
                },
            }, options),
        });
    },

    /**
     * Create a bar chart.
     * @param {string}   canvasId
     * @param {string[]} labels
     * @param {object[]} datasets   Each: { label, data, color? }
     * @param {object}   [options]
     * @returns {Chart|null}
     */
    bar(canvasId, labels, datasets, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const palette     = Object.values(this.colors);
        const builtDatasets = datasets.map((ds, i) => ({
            label:           ds.label,
            data:            ds.data,
            backgroundColor: (ds.color || palette[i % palette.length]) + 'cc',
            borderColor:     ds.color || palette[i % palette.length],
            borderWidth:     1,
            borderRadius:    4,
        }));

        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: { labels, datasets: builtDatasets },
            options: Object.assign({
                responsive:          true,
                maintainAspectRatio: true,
                plugins: { legend: { display: datasets.length > 1 } },
                scales: {
                    x: { grid: { color: '#1e2130' }, ticks: { color: '#64748b' } },
                    y: { grid: { color: '#1e2130' }, ticks: { color: '#64748b' }, beginAtZero: true },
                },
            }, options),
        });
    },

    /**
     * Create a doughnut chart.
     * @param {string}   canvasId
     * @param {string[]} labels
     * @param {number[]} data
     * @param {string[]} [colors]
     * @param {object}   [options]
     * @returns {Chart|null}
     */
    doughnut(canvasId, labels, data, colors, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const palette = Object.values(this.colors);
        const bgColors = colors || palette.slice(0, data.length);

        return new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: bgColors.map(c => c + 'cc'),
                    borderColor:     bgColors,
                    borderWidth:     2,
                    hoverOffset:     6,
                }],
            },
            options: Object.assign({
                responsive:          true,
                maintainAspectRatio: true,
                cutout:              '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, boxWidth: 12 },
                    },
                },
            }, options),
        });
    },
};

/* ----------------------------------------------------------
   3. AJAX / Fetch Helpers
   ---------------------------------------------------------- */
const CRMApi = {
    /**
     * Internal fetch wrapper with JSON and CSRF headers.
     * @param {string} url
     * @param {object} options  fetch() options
     * @returns {Promise<any>}
     */
    async _request(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  csrfToken || '',
            },
        };

        const merged   = Object.assign({}, defaults, options);
        merged.headers = Object.assign({}, defaults.headers, options.headers || {});

        try {
            const response = await fetch(url, merged);

            if (!response.ok) {
                const body = await response.json().catch(() => ({}));
                throw { status: response.status, body };
            }

            // 204 No Content has no body
            if (response.status === 204) return null;

            return await response.json();
        } catch (error) {
            CRMToast.show('Request failed', error?.body?.message || 'An unexpected error occurred.', 'error');
            throw error;
        }
    },

    get:    (url, options = {})       => CRMApi._request(url, { ...options, method: 'GET' }),
    post:   (url, data, options = {}) => CRMApi._request(url, { ...options, method: 'POST',   body: JSON.stringify(data) }),
    put:    (url, data, options = {}) => CRMApi._request(url, { ...options, method: 'PUT',    body: JSON.stringify(data) }),
    patch:  (url, data, options = {}) => CRMApi._request(url, { ...options, method: 'PATCH',  body: JSON.stringify(data) }),
    delete: (url, options = {})       => CRMApi._request(url, { ...options, method: 'DELETE' }),
};

/* ----------------------------------------------------------
   4. Toast Notification System
   ---------------------------------------------------------- */
const CRMToast = (() => {
    let container = null;

    function ensureContainer() {
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    // Bootstrap Icons are used throughout this project (bi-* classes)
    const icons = {
        success: '<i class="bi bi-check-circle-fill"></i>',
        error:   '<i class="bi bi-x-circle-fill"></i>',
        warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
        info:    '<i class="bi bi-info-circle-fill"></i>',
    };

    /**
     * Show a toast notification.
     * @param {string} title
     * @param {string} [message]
     * @param {'success'|'error'|'warning'|'info'} [type]
     * @param {number} [duration]  ms before auto-dismiss (0 = no auto-dismiss)
     */
    function show(title, message = '', type = 'info', duration = 4000) {
        const c    = ensureContainer();
        const toast = document.createElement('div');
        toast.className = `crm-toast toast-${type}`;

        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-body">
                <div class="toast-title">${escapeHtml(title)}</div>
                ${message ? `<div class="toast-message">${escapeHtml(message)}</div>` : ''}
            </div>
            <button type="button" style="background:none;border:none;color:#64748b;cursor:pointer;padding:0 0 0 8px;font-size:14px;" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        const closeBtn = toast.querySelector('button[aria-label="Close"]');
        closeBtn.addEventListener('click', () => dismiss(toast));

        c.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => dismiss(toast), duration);
        }

        return toast;
    }

    function dismiss(toast) {
        if (!toast || toast.classList.contains('removing')) return;
        toast.classList.add('removing');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    return { show, dismiss };
})();

// Expose globally
window.CRMToast = CRMToast;
window.CRMApi   = CRMApi;
window.CRMCharts = CRMCharts;

/* ----------------------------------------------------------
   5. Sidebar Toggle
   ---------------------------------------------------------- */
(function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('sidebar');
    const wrapper   = document.getElementById('wrapper');

    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', function () {
        // Desktop: collapse sidebar by adjusting margin
        if (window.innerWidth >= 993) {
            wrapper.classList.toggle('sidebar-collapsed');
        } else {
            // Mobile: slide in/out
            sidebar.classList.toggle('open');
        }
    });

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function (e) {
        if (
            window.innerWidth < 993 &&
            sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            e.target !== toggleBtn &&
            !toggleBtn.contains(e.target)
        ) {
            sidebar.classList.remove('open');
        }
    });
})();

/* ----------------------------------------------------------
   6. Filter Tabs (Campaign / Contact status tabs)
   ---------------------------------------------------------- */
(function initFilterTabs() {
    const tabs = document.querySelectorAll('.filter-tab[data-status]');
    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const status  = this.dataset.status;
            const rows    = document.querySelectorAll('tr[data-status]');

            rows.forEach(row => {
                const match = status === 'all' || row.dataset.status === status;
                row.style.display = match ? '' : 'none';
            });
        });
    });
})();

/* ----------------------------------------------------------
   7. Confirm Delete Dialog
   ---------------------------------------------------------- */
(function initDeleteConfirm() {
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.dataset.confirm) return;

        if (!confirm(form.dataset.confirm || 'Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
})();

/* ----------------------------------------------------------
   8. CSV Import Drag-Drop Zone
   ---------------------------------------------------------- */
(function initDropZone() {
    const zone  = document.querySelector('.drop-zone');
    const input = zone ? zone.querySelector('input[type="file"]') : null;

    if (!zone || !input) return;

    zone.addEventListener('click', () => input.click());

    zone.addEventListener('dragover', e => {
        e.preventDefault();
        zone.classList.add('dragover');
    });

    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));

    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('dragover');

        const file = e.dataTransfer?.files?.[0];
        if (!file) return;

        const dt   = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;

        updateDropZoneLabel(zone, file.name);
    });

    input.addEventListener('change', function () {
        if (this.files?.[0]) {
            updateDropZoneLabel(zone, this.files[0].name);
        }
    });

    function updateDropZoneLabel(zone, filename) {
        const title    = zone.querySelector('.drop-title');
        const subtitle = zone.querySelector('.drop-subtitle');
        if (title)    title.textContent    = filename;
        if (subtitle) subtitle.textContent = 'File selected — click Upload to continue';
    }
})();

/* ----------------------------------------------------------
   9. Segment Filter Builder (dynamic rows)
   ---------------------------------------------------------- */
(function initSegmentBuilder() {
    const addBtn      = document.getElementById('addFilterRow');
    const container   = document.getElementById('filterRowsContainer');

    if (!addBtn || !container) return;

    let rowIndex = container.querySelectorAll('.filter-row').length;

    addBtn.addEventListener('click', () => {
        rowIndex++;
        const row = document.createElement('div');
        row.className = 'filter-row';
        row.innerHTML = `
            <select name="filters[${rowIndex}][field]" class="form-select form-select-sm">
                <option value="email">Email</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="city">City</option>
                <option value="country">Country</option>
                <option value="status">Status</option>
                <option value="created_at">Created At</option>
            </select>
            <select name="filters[${rowIndex}][operator]" class="form-select form-select-sm">
                <option value="equals">equals</option>
                <option value="not_equals">not equals</option>
                <option value="contains">contains</option>
                <option value="not_contains">does not contain</option>
                <option value="starts_with">starts with</option>
                <option value="ends_with">ends with</option>
                <option value="greater_than">greater than</option>
                <option value="less_than">less than</option>
            </select>
            <input type="text" name="filters[${rowIndex}][value]" class="form-control form-control-sm" placeholder="Value">
            <button type="button" class="btn-remove-filter" title="Remove filter">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        row.querySelector('.btn-remove-filter').addEventListener('click', () => row.remove());
        container.appendChild(row);
    });

    // Attach remove listeners to pre-existing rows
    container.querySelectorAll('.btn-remove-filter').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.filter-row').remove());
    });
})();

/* ----------------------------------------------------------
   10. A/B Test Toggle (Campaign create/edit)
   ---------------------------------------------------------- */
(function initAbTestToggle() {
    const toggle  = document.getElementById('abTestToggle');
    const section = document.getElementById('abTestSection');

    if (!toggle || !section) return;

    function syncVisibility() {
        section.style.display = toggle.checked ? 'block' : 'none';
    }

    toggle.addEventListener('change', syncVisibility);
    syncVisibility();
})();

/* ----------------------------------------------------------
   11. Auto-dismiss Flash Alerts
   ---------------------------------------------------------- */
(function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert[role="alert"]:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap?.Alert?.getOrCreateInstance?.(alert);
            bsAlert ? bsAlert.close() : alert.remove();
        }, 5000);
    });
})();

/* ----------------------------------------------------------
   12. Clipboard Copy Helper
   ---------------------------------------------------------- */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;

    const text = btn.dataset.copy;
    navigator.clipboard?.writeText(text).then(() => {
        CRMToast.show('Copied!', text, 'success', 2000);
    }).catch(() => {
        CRMToast.show('Copy failed', 'Could not access clipboard.', 'error');
    });
});
