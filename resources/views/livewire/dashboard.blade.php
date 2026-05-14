<div class="content">
    <div class="container-fluid" style="padding: 0 8px;">

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--  ROW 1 — STAT CARDS                                --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="stats-grid" style="margin-bottom:16px;">

            {{-- SUPPLIERS --}}
            <div class="stat-card blue">
                <div class="stat-header">
                    <span class="stat-label">Total Suppliers</span>
                    <div class="stat-icon blue">🚚</div>
                </div>
                <div class="stat-value blue">{{ $total_suppliers }}</div>
                <div class="stat-sub">Active vendor relationships</div>
            </div>

            {{-- CATEGORIES --}}
            <div class="stat-card orange">
                <div class="stat-header">
                    <span class="stat-label">Total Categories</span>
                    <div class="stat-icon orange">🗂️</div>
                </div>
                <div class="stat-value orange">{{ $total_category }}</div>
                <div class="stat-sub">Product classification groups</div>
            </div>

            {{-- SUB CATEGORIES --}}
            <div class="stat-card red">
                <div class="stat-header">
                    <span class="stat-label">Sub Categories</span>
                    <div class="stat-icon red">📂</div>
                </div>
                <div class="stat-value red">{{ $total_sub_category }}</div>
                <div class="stat-sub">Nested category entries</div>
            </div>

            {{-- PRODUCTS --}}
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Total Products</span>
                    <div class="stat-icon green">📦</div>
                </div>
                <div class="stat-value green">{{ $total_products }}</div>
                <div class="stat-sub">SKUs in system</div>
            </div>

        </div>{{-- /stats-grid row 1 --}}

        {{-- STATS ROW 2 --}}
        <div class="stats-grid" style="margin-bottom:24px;">

            {{-- ITEMS --}}
            <div class="stat-card blue">
                <div class="stat-header">
                    <span class="stat-label">Total Items</span>
                    <div class="stat-icon blue">📋</div>
                </div>
                <div class="stat-value blue">{{ $total_items }}</div>
                <div class="stat-sub">Line items tracked</div>
            </div>

            {{-- REQUESTS --}}
            <div class="stat-card orange">
                <div class="stat-header">
                    <span class="stat-label">Total Requests</span>
                    <div class="stat-icon orange">📨</div>
                </div>
                <div class="stat-value orange">{{ $overall_request }}</div>
                <div class="stat-sub">Pending supply requests</div>
            </div>

            {{-- DENIED --}}
            <div class="stat-card red">
                <div class="stat-header">
                    <span class="stat-label">Total Denied</span>
                    <div class="stat-icon red">❌</div>
                </div>
                <div class="stat-value red">{{ $count_of_denied }}</div>
                <div class="stat-sub">No denied requests</div>
            </div>

            {{-- APPROVED --}}
            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Total Approved</span>
                    <div class="stat-icon green">✅</div>
                </div>
                <div class="stat-value green">{{ $count_of_approved }}</div>
                <div class="stat-sub">Approved this period</div>
            </div>

        </div>{{-- /stats-grid row 2 --}}

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--  ROW 2 — CHARTS & MOVING ITEMS                     --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="dash-row-3" style="margin-bottom:24px;">

            {{-- FAST / SLOW MOVING --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <span class="panel-title-dot" style="background:var(--green)"></span>
                        Item Movement
                    </div>
                </div>
                <div class="panel-body" style="padding-top:8px;">
                    <div class="items-list">

                        {{-- Fast Moving --}}
                        <div class="list-heading">
                            <span class="arrow-up">↑</span> Fast-Moving
                        </div>
                        @php $counter = 1; @endphp
                        @foreach ($fastMovingItems as $item)
                        <div class="list-item">
                            <div class="list-rank">{{ $counter }}</div>
                            <div class="list-name">{{ $item['item_name'] }}</div>
                            <div class="list-bar-wrap">
                                <div class="list-bar bar-fast" style="width:{{ max(20, 100 - ($counter - 1) * 18) }}%"></div>
                            </div>
                        </div>
                        @php $counter++; @endphp
                        @endforeach

                        {{-- Slow Moving --}}
                        <div class="list-heading" style="margin-top:8px;">
                            <span class="arrow-down">↓</span> Slow-Moving
                        </div>
                        @php $counter = 1; @endphp
                        @foreach ($slowMovingItems as $item)
                        <div class="list-item">
                            <div class="list-rank">{{ $counter }}</div>
                            <div class="list-name">{{ $item['item_name'] }}</div>
                            <div class="list-bar-wrap">
                                <div class="list-bar bar-slow" style="width:{{ max(20, 100 - ($counter - 1) * 18) }}%"></div>
                            </div>
                        </div>
                        @php $counter++; @endphp
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- DONUT CHART --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <span class="panel-title-dot" style="background:var(--accent)"></span>
                        Re-Order Statuses
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap" style="height:200px;">
                        <canvas id="donutChart" width="200" height="200" style="max-width:200px;max-height:200px;"></canvas>
                        <div class="donut-center">
                            <strong>{{ $total_items }}</strong>
                            <span>Items</span>
                        </div>
                    </div>
                    <div class="legend">
                        <div class="legend-item"><div class="legend-dot" style="background:#3fb950"></div>Abundant</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#2f81f7"></div>Sufficient</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#e3b341"></div>Warning Level</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#f85149"></div>Critical Level</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#7d8590"></div>Out of Stock</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#21262d"></div>Reorder Not Set</div>
                    </div>
                </div>
            </div>

            {{-- RADAR CHART --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <span class="panel-title-dot" style="background:var(--purple)"></span>
                        Request Status Distribution
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap" style="height:300px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>
            </div>

        </div>{{-- /dash-row-3 --}}

        {{-- ═══════════════════════════════════════════════════ --}}
        {{--  ROW 3 — RECENT TRANSACTIONS                       --}}
        {{-- ═══════════════════════════════════════════════════ --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title">
                    <span class="panel-title-dot" style="background:var(--orange)"></span>
                    Recent Transactions
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="text" id="txn-search" placeholder="Search…" style="
                        background:var(--surface2); border:1px solid var(--border);
                        border-radius:8px; padding:6px 12px; color:var(--text);
                        font-size:12px; font-family:inherit; outline:none; width:160px;
                        transition:border-color 0.15s;
                    " onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
                </div>
            </div>
            <div class="table-wrap">
                <table id="recent_transaction" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Issued By</th>
                            <th>Issued To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->recent_transactions as $index => $rt)
                        <tr>
                            <td class="td-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="td-num">{{ $rt['number'] }}</td>
                            <td class="td-date">{{ $rt['date'] }}</td>
                            <td>
                                <div class="user-chip">
                                    <div class="user-chip-av">{{ strtoupper(substr($rt['issued_by'], 0, 2)) }}</div>
                                    {{ $rt['issued_by'] }}
                                </div>
                            </td>
                            <td>
                                <div class="user-chip">
                                    <div class="user-chip-av">{{ strtoupper(substr($rt['issued_to'], 0, 2)) }}</div>
                                    {{ $rt['issued_to'] }}
                                </div>
                            </td>
                            <td>
                                @if(isset($rt['status']))
                                    <span class="status-chip {{ $rt['status'] === 'Completed' ? 'done' : 'pending' }}">
                                        {{ $rt['status'] }}
                                    </span>
                                @else
                                    <span class="status-chip done">Completed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /container-fluid --}}
</div>{{-- /content --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- ── DONUT CHART ──────────────────────────────────────── --}}
<script>
(function () {
    var ctx = document.getElementById('donutChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Abundant','Sufficient','Warning Level','Critical Level','Out of Stock','Reorder Not Set'],
            datasets: [{
                data: [
                    {{ $this->abundant_level }},
                    {{ $this->sufficient_level }},
                    {{ $this->warning_level }},
                    {{ $this->crtical_level }},
                    {{ $this->out_of_stoock_level }},
                    {{ $this->reorder_not_set }}
                ],
                backgroundColor: ['#3fb950','#2f81f7','#e3b341','#f85149','#7d8590','#21262d'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c2330',
                    borderColor: '#30363d',
                    borderWidth: 1,
                    titleColor: '#e6edf3',
                    bodyColor: '#7d8590',
                    padding: 10,
                    callbacks: {
                        label: (c) => ` ${c.label}: ${c.raw} items`
                    }
                }
            },
            animation: { animateRotate: true, duration: 800 },
        }
    });
})();
</script>

{{-- ── RADAR CHART ──────────────────────────────────────── --}}
<script>
(function () {
    var ctx = document.getElementById('radarChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Checked Out','For Release','In-Transit','Received','Approved','Denied'],
            datasets: [{
                label: 'Requests',
                data: [
                    {{ $this->count_of_checked_out }},
                    {{ $this->count_of_for_release }},
                    {{ $this->count_of_intransit }},
                    {{ $this->count_of_received }},
                    {{ $this->count_of_approved }},
                    {{ $this->count_of_denied }}
                ],
                backgroundColor: 'rgba(163,113,247,0.15)',
                borderColor: '#a371f7',
                borderWidth: 2,
                pointBackgroundColor: '#a371f7',
                pointBorderColor: '#161b22',
                pointHoverBackgroundColor: '#e6edf3',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    ticks: {
                        color: '#7d8590',
                        backdropColor: 'transparent',
                        font: { size: 10 }
                    },
                    grid:        { color: '#21262d' },
                    angleLines:  { color: '#21262d' },
                    pointLabels: { color: '#7d8590', font: { size: 11, family: 'DM Sans' } }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1c2330',
                    borderColor: '#30363d',
                    borderWidth: 1,
                    titleColor: '#e6edf3',
                    bodyColor: '#7d8590',
                    padding: 10,
                }
            }
        }
    });
})();
</script>

{{-- ── DATATABLES ───────────────────────────────────────── --}}
<script>
$(document).ready(function () {
    $('#recent_transaction').DataTable({
        searching: false,
        language: {
            info: "Showing _START_–_END_ of _TOTAL_ entries",
            paginate: {
                previous: "←",
                next:     "→"
            }
        }
    });

    // wire search box to DataTable
    $('#txn-search').on('keyup', function () {
        $('#recent_transaction').DataTable().search(this.value).draw();
    });
});

@if (session()->has('success'))
    Swal.fire('Success!', '{{ session('success') }}', 'success');
@elseif(session()->has('failed'))
    Swal.fire('Failed!', '{{ session('failed') }}', 'error');
@endif
</script>

{{-- ── DASHBOARD STYLES ─────────────────────────────────── --}}
<style>
/* Stat cards — exact reference layout */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    overflow: hidden;
    cursor: default;
    transition: border-color 0.2s, transform 0.2s;
    animation: fadeUp 0.4s ease both;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    border-radius: 14px 14px 0 0;
}
.stat-card.blue::before   { background: var(--accent); }
.stat-card.orange::before { background: var(--orange); }
.stat-card.red::before    { background: var(--red); }
.stat-card.green::before  { background: var(--green); }
.stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.stat-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.stat-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.stat-icon.blue   { background: var(--accent-glow); }
.stat-icon.orange { background: var(--orange-bg); }
.stat-icon.red    { background: var(--red-bg); }
.stat-icon.green  { background: var(--green-bg); }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1;
}
.stat-value.blue   { color: var(--accent); }
.stat-value.orange { color: var(--orange); }
.stat-value.red    { color: var(--red); }
.stat-value.green  { color: var(--green); }

.stat-sub {
    font-size: 12px;
    color: var(--muted);
}

/* 3-col chart row */
.dash-row-3 {
    display: grid;
    grid-template-columns: 1fr 1.2fr 1.4fr;
    gap: 16px;
}

/* Panel */
.panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    animation: fadeUp 0.4s ease both;
    animation-delay: 0.25s;
}
.panel-header {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.panel-title {
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text);
}
.panel-title-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.panel-body { padding: 18px 22px; }

/* Item movement list */
.items-list { display: flex; flex-direction: column; }

.list-heading {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--muted);
    padding: 12px 0 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.list-heading::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}
.arrow-up   { color: var(--green); }
.arrow-down { color: var(--orange); }

.list-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 10px;
    border-radius: 8px;
    transition: background 0.12s;
}
.list-item:hover { background: rgba(255,255,255,0.03); }

.list-rank {
    width: 20px; height: 20px;
    border-radius: 6px;
    background: var(--surface2);
    display: flex; align-items: center; justify-content: center;
    font-size: 10px;
    font-weight: 600;
    color: var(--muted);
    flex-shrink: 0;
}
.list-name {
    flex: 1;
    font-size: 13px;
    font-weight: 400;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.list-bar-wrap {
    width: 60px; height: 4px;
    background: var(--surface2);
    border-radius: 2px;
    flex-shrink: 0;
}
.list-bar { height: 100%; border-radius: 2px; }
.bar-fast  { background: var(--green); }
.bar-slow  { background: var(--orange); }

/* Donut center label */
.chart-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.donut-center {
    position: absolute;
    text-align: center;
    pointer-events: none;
}
.donut-center strong { display: block; font-size: 22px; font-weight: 700; color: var(--text); }
.donut-center span   { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }

/* Legend */
.legend { display: flex; flex-direction: column; gap: 8px; margin-top: 16px; }
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--muted);
}
.legend-dot {
    width: 8px; height: 8px;
    border-radius: 2px;
    flex-shrink: 0;
}

/* Transaction table */
.table-wrap { overflow-x: auto; }

#recent_transaction { width: 100%; border-collapse: collapse; }
#recent_transaction thead tr { border-bottom: 1px solid var(--border); }
#recent_transaction th {
    font-size: 11px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    color: var(--muted) !important;
    padding: 10px 14px !important;
    text-align: left !important;
    white-space: nowrap;
    border-bottom: 1px solid var(--border) !important;
    border-top: none !important;
    background: transparent !important;
}
#recent_transaction td {
    padding: 12px 14px !important;
    font-size: 13px !important;
    color: var(--text) !important;
    border-bottom: 1px solid var(--border) !important;
    border-top: none !important;
    vertical-align: middle !important;
}
#recent_transaction tbody tr:last-child td { border-bottom: none !important; }
#recent_transaction tbody tr:hover td { background: rgba(255,255,255,0.02) !important; }

.td-num  { font-family: 'DM Mono', monospace !important; font-size: 12px !important; color: var(--muted) !important; }
.td-date { font-family: 'DM Mono', monospace !important; font-size: 12px !important; color: var(--muted) !important; }

.user-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 3px 10px 3px 4px;
    font-size: 12px;
    color: var(--text);
}
.user-chip-av {
    width: 18px; height: 18px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2f81f7, #a371f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 9px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.status-chip {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}
.status-chip.done    { background: var(--green-bg); color: var(--green); }
.status-chip.pending { background: var(--orange-bg); color: var(--orange); }

/* Animation */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.10s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.20s; }

/* DataTables wrapper dark override */
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter { display: none; }
</style>
@endpush