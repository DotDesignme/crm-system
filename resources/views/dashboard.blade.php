@extends('layouts.app')
@section('page-title', __('messages.dashboard'))

@section('content')

{{-- ===== PAGE HEADER ===== --}}
<div class="dash-header">
    <div class="dash-header-left">
        <div class="dash-greeting">
            <span class="greeting-emoji">👋</span>
            <div>
                <h1>{{ __('messages.welcome') }}, <span class="gradient-name">{{ auth()->user()->name }}</span></h1>
                <p class="date-line">{{ now()->format('l, d F Y') }}</p>
            </div>
        </div>
    </div>
    <div class="dash-header-right">
        @if(auth()->user()->is_admin)
        <a href="{{ route('companies.index') }}" class="dash-btn dash-btn-ghost">
            <i class="fas fa-building"></i>
            {{ __('messages.manage_branches') }}
        </a>
        @endif
        <a href="{{ route('leads.create') }}" class="dash-btn dash-btn-primary">
            <i class="fas fa-plus"></i>
            {{ __('messages.add_lead') }}
        </a>
    </div>
</div>

{{-- ===== ADMIN: FINANCIAL KPIs ===== --}}
@if(auth()->user()->is_admin)

<div class="kpi-row">
    {{-- Forecasted Revenue --}}
    <div class="kpi-glass kpi-forecast fade-in-up" style="--delay:0.05s">
        <div class="kpi-bg-circle"></div>
        <div class="kpi-icon-wrap">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">{{ __('messages.forecasted') }}</div>
            <div class="kpi-value">{{ number_format($weightedRevenue ?? 0, 0) }}</div>
            <div class="kpi-sub">{{ $system_branding['system_currency'] ?? 'EGP' }} &mdash; {{ __('messages.weighted_revenue') }}</div>
        </div>
        <div class="kpi-glow kpi-glow-cyan"></div>
    </div>

    {{-- Actual Revenue --}}
    <div class="kpi-glass kpi-actual fade-in-up" style="--delay:0.10s">
        <div class="kpi-bg-circle"></div>
        <div class="kpi-icon-wrap kpi-icon-green">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">{{ __('messages.actual') }}</div>
            <div class="kpi-value">{{ number_format($wonDealsValue ?? 0, 0) }}</div>
            <div class="kpi-sub">{{ $system_branding['system_currency'] ?? 'EGP' }} &mdash; {{ __('messages.won_deals') }}</div>
        </div>
        <div class="kpi-glow kpi-glow-green"></div>
    </div>

    {{-- Pipeline Value --}}
    <div class="kpi-glass kpi-pipeline fade-in-up" style="--delay:0.15s">
        <div class="kpi-bg-circle"></div>
        <div class="kpi-icon-wrap kpi-icon-blue">
            <i class="fas fa-filter"></i>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">{{ __('messages.pipeline') }}</div>
            <div class="kpi-value">{{ number_format($totalPipelineValue ?? 0, 0) }}</div>
            <div class="kpi-sub">{{ $system_branding['system_currency'] ?? 'EGP' }} &mdash; {{ __('messages.pipeline_value') }}</div>
        </div>
        <div class="kpi-glow kpi-glow-blue"></div>
    </div>
</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="charts-row">
    {{-- Revenue Chart --}}
    <div class="glass-panel chart-panel fade-in-up" style="--delay:0.20s">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot" style="background:var(--brand-cyan)"></span>
                {{ __('messages.revenue_forecast') }}
            </div>
            <div class="panel-badge">{{ __('messages.last_7_days') }}</div>
        </div>
        <div class="chart-wrap">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Sales Funnel --}}
    <div class="glass-panel chart-panel-sm fade-in-up" style="--delay:0.25s">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot" style="background:#a78bfa"></span>
                {{ __('messages.sales_funnel') }}
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="funnelChart"></canvas>
        </div>
    </div>
</div>

@endif

{{-- ===== OPERATIONAL METRIC CARDS ===== --}}
<div class="op-kpi-row">
    <div class="op-kpi fade-in-up" style="--delay:0.30s">
        <div class="op-kpi-icon" style="background:rgba(99,102,241,0.15);color:#818cf8">
            <i class="fas fa-users"></i>
        </div>
        <div class="op-kpi-info">
            <div class="op-kpi-num">{{ $totalLeads }}</div>
            <div class="op-kpi-lbl">{{ __('messages.total_leads') }}</div>
        </div>
        <div class="op-kpi-spark" style="background:rgba(99,102,241,0.08)"></div>
    </div>

    <div class="op-kpi fade-in-up" style="--delay:0.35s">
        <div class="op-kpi-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24">
            <i class="fas fa-calendar-star"></i>
        </div>
        <div class="op-kpi-info">
            <div class="op-kpi-num">{{ $todayLeads }}</div>
            <div class="op-kpi-lbl">{{ __('messages.today_leads') }}</div>
        </div>
        <div class="op-kpi-spark" style="background:rgba(245,158,11,0.08)"></div>
    </div>

    @if(auth()->user()->is_admin)
    <div class="op-kpi fade-in-up" style="--delay:0.40s">
        <div class="op-kpi-icon" style="background:rgba(14,165,233,0.15);color:#38bdf8">
            <i class="fas fa-building"></i>
        </div>
        <div class="op-kpi-info">
            <div class="op-kpi-num">{{ $companies->count() }}</div>
            <div class="op-kpi-lbl">{{ __('messages.companies') }}</div>
        </div>
        <div class="op-kpi-spark" style="background:rgba(14,165,233,0.08)"></div>
    </div>

    <div class="op-kpi fade-in-up" style="--delay:0.45s">
        <div class="op-kpi-icon" style="background:rgba(16,185,129,0.15);color:#34d399">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="op-kpi-info">
            <div class="op-kpi-num">{{ $employees->count() }}</div>
            <div class="op-kpi-lbl">{{ __('messages.team_members') }}</div>
        </div>
        <div class="op-kpi-spark" style="background:rgba(16,185,129,0.08)"></div>
    </div>
    @else
    <div class="op-kpi fade-in-up" style="--delay:0.40s">
        <div class="op-kpi-icon" style="background:rgba(251,113,133,0.15);color:#fb7185">
            <i class="fas fa-star"></i>
        </div>
        <div class="op-kpi-info">
            <div class="op-kpi-num">{{ $myLeads }}</div>
            <div class="op-kpi-lbl">{{ __('messages.my_leads') }}</div>
        </div>
        <div class="op-kpi-spark" style="background:rgba(251,113,133,0.08)"></div>
    </div>
    @endif
</div>

{{-- ===== BOTTOM ROW: Recent Leads + Activity ===== --}}
<div class="bottom-row">

    {{-- Recent Leads Table --}}
    <div class="glass-panel fade-in-up" style="--delay:0.50s; flex: 2;">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot" style="background:#f59e0b"></span>
                {{ __('messages.recent_leads') }}
            </div>
            <a href="{{ route('leads.index') }}" class="panel-link">
                {{ __('messages.view_all') }} <i class="fas fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>

        @if($recentLeads->count())
        <div class="leads-table-wrap">
            <table class="leads-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        @if(auth()->user()->is_admin)
                        <th>{{ __('messages.company') }}</th>
                        @endif
                        <th>{{ __('messages.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLeads as $lead)
                    <tr class="lead-row">
                        <td>
                            <div class="lead-name-wrap">
                                <div class="lead-avatar">{{ mb_substr($lead->name, 0, 1) }}</div>
                                <div>
                                    <div class="lead-name">{{ $lead->name }}</div>
                                    <div class="lead-phone">{{ $lead->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-pill status-{{ $lead->status }}">{{ $lead->status_ar }}</span></td>
                        @if(auth()->user()->is_admin)
                        <td class="text-muted-sm">{{ $lead->company->name ?? '–' }}</td>
                        @endif
                        <td class="text-muted-sm">{{ $lead->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-block">
            <i class="fas fa-inbox"></i>
            <p>{{ __('messages.no_leads') }}</p>
        </div>
        @endif
    </div>

    {{-- Activity Feed --}}
    <div class="glass-panel fade-in-up" style="--delay:0.55s; flex: 1;">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot" style="background:#f472b6"></span>
                {{ __('messages.recent_activities') }}
            </div>
        </div>

        @if(isset($activities) && $activities->count())
        <div class="activity-feed">
            @foreach($activities as $activity)
            <div class="activity-item">
                <div class="activity-dot-wrap">
                    <div class="activity-dot"></div>
                    <div class="activity-line"></div>
                </div>
                <div class="activity-content">
                    <div class="activity-meta">
                        <span class="activity-actor">{{ $activity->employee->name ?? 'System' }}</span>
                        <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="activity-desc">{{ $activity->description }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-block">
            <i class="fas fa-bolt"></i>
            <p>{{ __('messages.no_activity') }}</p>
        </div>
        @endif
    </div>

</div>

{{-- ===== INTERNAL STYLES ===== --}}
<style>
/* ---------- HEADER ---------- */
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 36px;
    padding: 0 4px;
}
.dash-greeting {
    display: flex;
    align-items: center;
    gap: 18px;
}
.greeting-emoji {
    font-size: 42px;
    line-height: 1;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,.4));
}
.dash-header h1 {
    font-size: clamp(22px, 3vw, 30px);
    font-weight: 800;
    color: #fff;
    margin: 0 0 4px;
}
.gradient-name {
    background: linear-gradient(135deg, #0ea5e9, #818cf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.date-line {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}
.dash-header-right {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: 1px solid transparent;
}
.dash-btn-ghost {
    background: rgba(255,255,255,.06);
    color: rgba(255,255,255,.75);
    border-color: rgba(255,255,255,.1);
}
.dash-btn-ghost:hover { background: rgba(255,255,255,.12); color: #fff; }
.dash-btn-primary {
    background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
    color: #fff;
    box-shadow: 0 8px 24px rgba(29,78,216,.4);
}
.dash-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(29,78,216,.5); color:#fff; }

/* ---------- BIG KPI CARDS ---------- */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}
@media(max-width:900px){ .kpi-row{grid-template-columns:1fr;} }

.kpi-glass {
    position: relative;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 24px;
    padding: 28px;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform .3s, box-shadow .3s;
}
.kpi-glass:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,0,0,.4); }

.kpi-bg-circle {
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    top: -40px; right: -40px;
    pointer-events: none;
}
.kpi-forecast .kpi-bg-circle { background: radial-gradient(circle, rgba(14,165,233,.15), transparent 70%); }
.kpi-actual   .kpi-bg-circle { background: radial-gradient(circle, rgba(16,185,129,.15), transparent 70%); }
.kpi-pipeline .kpi-bg-circle { background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%); }

.kpi-icon-wrap {
    flex-shrink: 0;
    width: 52px; height: 52px;
    border-radius: 16px;
    background: rgba(14,165,233,.15);
    color: #38bdf8;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    z-index: 1;
}
.kpi-icon-green { background: rgba(16,185,129,.15); color: #34d399; }
.kpi-icon-blue  { background: rgba(99,102,241,.15);  color: #818cf8; }

.kpi-body { z-index: 1; }
.kpi-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 6px;
}
.kpi-value {
    font-size: clamp(24px, 3.5vw, 36px);
    font-weight: 900;
    color: #fff;
    line-height: 1;
    margin-bottom: 6px;
}
.kpi-sub { font-size: 12px; color: var(--text-muted); }

.kpi-glow {
    position: absolute;
    inset: 0;
    border-radius: 24px;
    pointer-events: none;
    opacity: 0;
    transition: opacity .3s;
}
.kpi-glass:hover .kpi-glow { opacity: 1; }
.kpi-glow-cyan  { box-shadow: inset 0 0 40px rgba(14,165,233,.12); }
.kpi-glow-green { box-shadow: inset 0 0 40px rgba(16,185,129,.12); }
.kpi-glow-blue  { box-shadow: inset 0 0 40px rgba(99,102,241,.12); }

/* ---------- CHARTS ---------- */
.charts-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
}
@media(max-width:1024px){ .charts-row{grid-template-columns:1fr;} }

.glass-panel {
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 24px;
    padding: 24px 28px;
    transition: box-shadow .3s;
}
.glass-panel:hover { box-shadow: 0 16px 40px rgba(0,0,0,.35); }

.chart-panel { }
.chart-panel-sm { }
.chart-wrap { height: 300px; position: relative; }

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.panel-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
}
.panel-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 8px currentColor;
}
.panel-badge {
    font-size: 12px;
    color: var(--text-muted);
    background: rgba(255,255,255,.05);
    padding: 4px 12px;
    border-radius: 100px;
    border: 1px solid rgba(255,255,255,.08);
}
.panel-link {
    font-size: 13px;
    color: var(--brand-cyan);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: opacity .2s;
}
.panel-link:hover { opacity: .8; color: var(--brand-cyan); }

/* ---------- OPERATIONAL KPIs ---------- */
.op-kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
@media(max-width:900px){ .op-kpi-row{grid-template-columns:repeat(2,1fr);} }
@media(max-width:480px){ .op-kpi-row{grid-template-columns:1fr;} }

.op-kpi {
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 20px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    animation: fadeInUp .6s both;
    animation-delay: var(--delay);
}
.op-kpi:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(0,0,0,.35); }
.op-kpi-icon {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    z-index: 1;
}
.op-kpi-info { z-index: 1; }
.op-kpi-num {
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    margin-bottom: 4px;
}
.op-kpi-lbl { font-size: 12px; color: var(--text-muted); font-weight: 600; }
.op-kpi-spark {
    position: absolute;
    right: -20px; bottom: -20px;
    width: 100px; height: 100px;
    border-radius: 50%;
}

/* ---------- BOTTOM ROW ---------- */
.bottom-row {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
@media(max-width:1024px){ .bottom-row{flex-direction:column;} .bottom-row > * { flex: 1 !important; width:100%; } }

/* ---------- LEADS TABLE ---------- */
.leads-table-wrap { overflow-x: auto; margin: 0 -4px; }
.leads-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 6px;
}
.leads-table thead th {
    padding: 0 14px 10px;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    text-align: left;
    border: none;
}
.lead-row td {
    padding: 12px 14px;
    background: rgba(255,255,255,.03);
    border: none;
    transition: background .2s;
}
.lead-row:hover td { background: rgba(255,255,255,.07); }
.lead-row td:first-child { border-radius: 12px 0 0 12px; }
.lead-row td:last-child  { border-radius: 0 12px 12px 0; }

.lead-name-wrap { display: flex; align-items: center; gap: 12px; }
.lead-avatar {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lead-name { font-size: 14px; font-weight: 700; color: #fff; }
.lead-phone { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.status-pill {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing:.5px;
}
.status-new         { background: rgba(99,102,241,.15); color: #818cf8; }
.status-contacted   { background: rgba(14,165,233,.15); color: #38bdf8; }
.status-interested  { background: rgba(245,158,11,.15); color: #fbbf24; }
.status-converted   { background: rgba(16,185,129,.15); color: #34d399; }
.status-lost        { background: rgba(239,68,68,.15);  color: #f87171; }

.text-muted-sm { font-size: 13px; color: var(--text-muted); }

/* ---------- ACTIVITY FEED ---------- */
.activity-feed { display: flex; flex-direction: column; gap: 0; }
.activity-item { display: flex; gap: 14px; }

.activity-dot-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
    padding-top: 2px;
}
.activity-dot {
    width: 10px; height: 10px;
    background: var(--brand-cyan);
    border-radius: 50%;
    box-shadow: 0 0 10px var(--brand-cyan);
    flex-shrink: 0;
}
.activity-line {
    flex: 1;
    width: 1px;
    background: rgba(255,255,255,.07);
    margin: 6px 0;
    min-height: 28px;
}
.activity-content { padding-bottom: 20px; }
.activity-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; flex-wrap: wrap; gap: 4px; }
.activity-actor { font-size: 13px; font-weight: 700; color: #fff; }
.activity-time  { font-size: 11px; color: var(--text-muted); }
.activity-desc  { font-size: 12px; color: var(--text-secondary); line-height: 1.5; margin: 0; }

/* ---------- EMPTY ---------- */
.empty-block {
    text-align: center;
    padding: 50px 20px;
    color: var(--text-muted);
}
.empty-block i { font-size: 36px; margin-bottom: 12px; display: block; opacity: .4; }
.empty-block p { font-size: 14px; margin: 0; }

/* ---------- ANIMATION ---------- */
.fade-in-up {
    animation: fadeInUp .6s both;
    animation-delay: var(--delay, 0s);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.color = 'rgba(255,255,255,0.4)';
    Chart.defaults.font.family = '"Inter", sans-serif';

    @if(auth()->user()->is_admin)

    /* ── Revenue Area Chart ── */
    const revCtx = document.getElementById('revenueChart')?.getContext('2d');
    if (revCtx) {
        const grad = revCtx.createLinearGradient(0, 0, 0, 300);
        grad.addColorStop(0, 'rgba(14,165,233,0.35)');
        grad.addColorStop(1, 'rgba(14,165,233,0)');

        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: @json($revenueByMonth->pluck('month')),
                datasets: [{
                    label: '{{ __("messages.revenue") }}',
                    data: @json($revenueByMonth->pluck('total')),
                    borderColor: '#0ea5e9',
                    borderWidth: 2.5,
                    fill: true,
                    backgroundColor: grad,
                    tension: 0.45,
                    pointBackgroundColor: '#0ea5e9',
                    pointBorderColor: '#050a15',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: 'rgba(255,255,255,.6)',
                        padding: 12,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                        ticks: { maxTicksLimit: 5 }
                    },
                    x: {
                        grid: { display: false },
                    }
                }
            }
        });
    }

    /* ── Funnel Horizontal Bar ── */
    const funnelCtx = document.getElementById('funnelChart')?.getContext('2d');
    if (funnelCtx) {
        const colors = [
            'rgba(129,140,248,0.75)',
            'rgba(56,189,248,0.75)',
            'rgba(251,191,36,0.75)',
            'rgba(52,211,153,0.75)',
            'rgba(248,113,113,0.75)',
        ];
        new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($funnelData->pluck('name')) !!},
                datasets: [{
                    label: '{{ __("messages.deals") }}',
                    data: {!! json_encode($funnelData->pluck('deals_count')) !!},
                    backgroundColor: colors,
                    borderRadius: 8,
                    barThickness: 20,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.95)',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,.05)', drawBorder: false }, ticks: { maxTicksLimit: 5 } },
                    y: { grid: { display: false }, ticks: { color: '#fff', font: { weight: '600' } } }
                }
            }
        });
    }

    @endif
});
</script>
@endsection
