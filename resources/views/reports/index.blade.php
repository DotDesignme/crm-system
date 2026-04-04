@extends('layouts.app')
@section('page-title', __('messages.reports'))
@section('content')

@php $totalLeads = array_sum(array_values($leadsByStatus)); @endphp

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-chart-bar"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.reports') }}</h1>
            <p class="page-shell-sub">{{ __('messages.analytics_overview') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <form method="GET" action="{{ route('reports') }}" style="display:flex; gap:10px; align-items:center;">
            <input type="date" name="from_date" value="{{ $fromDate }}" class="gm-input" style="height:38px; min-width:140px; background:rgba(255,255,255,0.03);">
            <span style="color:#6b7280;">-</span>
            <input type="date" name="to_date" value="{{ $toDate }}" class="gm-input" style="height:38px; min-width:140px; background:rgba(255,255,255,0.03);">
            <button type="submit" class="filter-btn filter-btn-primary" style="height:38px;"><i class="fas fa-filter"></i> {{ __('messages.filter') ?? 'Filter' }}</button>
        </form>
        <a href="{{ route('export.leads') }}" class="filter-btn filter-btn-ghost">
            <i class="fas fa-file-csv"></i> {{ __('messages.export') }} {{ __('messages.leads') }}
        </a>
        <a href="{{ route('export.campaigns') }}" class="filter-btn filter-btn-ghost">
            <i class="fas fa-file-csv"></i> {{ __('messages.export') }} {{ __('messages.campaigns') }}
        </a>
    </div>
</div>

{{-- KPI STATS --}}
<div class="g-stat-row" style="grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); margin-bottom:28px;">
    <div class="g-stat">
        <div class="g-stat-icon page-icon-cyan"><i class="fas fa-users"></i></div>
        <div>
            <div class="g-stat-val">{{ $totalLeads }}</div>
            <div class="g-stat-lbl">{{ __('messages.total_leads') }}</div>
        </div>
    </div>
    <div class="g-stat">
        <div class="g-stat-icon page-icon-green"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div class="g-stat-val" style="font-size:20px;">{{ number_format($totalBudget) }}</div>
            <div class="g-stat-lbl">{{ __('messages.total_budget') }}</div>
        </div>
    </div>
    <div class="g-stat">
        <div class="g-stat-icon page-icon-amber"><i class="fas fa-percentage"></i></div>
        <div>
            <div class="g-stat-val">{{ $avgCtr }}%</div>
            <div class="g-stat-lbl">{{ __('messages.avg_ctr') }}</div>
        </div>
    </div>
    <div class="g-stat">
        <div class="g-stat-icon page-icon-blue"><i class="fas fa-hand-pointer"></i></div>
        <div>
            <div class="g-stat-val" style="font-size:20px;">{{ $system_branding['system_currency_symbol'] ?? '' }}{{ $avgCpc }}</div>
            <div class="g-stat-lbl">{{ __('messages.avg_cpc') }}</div>
        </div>
    </div>
    <div class="g-stat">
        <div class="g-stat-icon page-icon-violet"><i class="fas fa-eye"></i></div>
        <div>
            <div class="g-stat-val" style="font-size:20px;">{{ number_format($totalReach) }}</div>
            <div class="g-stat-lbl">{{ __('messages.total_reach') }}</div>
        </div>
    </div>
    <div class="g-stat">
        <div class="g-stat-icon page-icon-rose"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="g-stat-val">{{ $totalConversions }}</div>
            <div class="g-stat-lbl">{{ __('messages.total_conversions') }}</div>
        </div>
    </div>
</div>

{{-- ROW 1: Status Chart + Priority Bars --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

    {{-- Leads by Status --}}
    <div class="g-panel g-panel-p">
        <h3 class="rpt-heading"><i class="fas fa-chart-pie" style="color:#818cf8;"></i> {{ __('messages.leads_by_status') }}</h3>
        @if($totalLeads > 0)
        <div class="r-bar-stack">
            @php $colors = ['new'=>'#60a5fa','contacted'=>'#fbbf24','interested'=>'#34d399','not_interested'=>'#f87171','converted'=>'#c084fc']; @endphp
            @foreach($colors as $s => $c)
                @php $w = ($leadsByStatus[$s] ?? 0) > 0 ? round(($leadsByStatus[$s]/$totalLeads)*100) : 0; @endphp
                @if($w > 0)
                <div class="r-seg" style="width:{{ $w }}%; background:{{ $c }};" title="{{ __('messages.status_'.$s) }}: {{ $leadsByStatus[$s] ?? 0 }}"></div>
                @endif
            @endforeach
        </div>
        <div class="r-legend">
            @foreach($colors as $s => $c)
            <div class="r-legend-item">
                <span class="r-dot" style="background:{{ $c }};"></span>
                {{ __('messages.status_'.$s) }} <span style="color:var(--text-muted); margin-left:4px;">({{ $leadsByStatus[$s] ?? 0 }})</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="g-empty" style="padding:40px 0;"><i class="fas fa-chart-pie"></i><p>{{ __('messages.no_data') }}</p></div>
        @endif
    </div>

    {{-- Leads by Priority --}}
    <div class="g-panel g-panel-p">
        <h3 class="rpt-heading"><i class="fas fa-flag" style="color:#fbbf24;"></i> {{ __('messages.leads_by_priority') }}</h3>
        @if(count($leadsByPriority) > 0)
        @php $maxP = max(array_values($leadsByPriority)) ?: 1; @endphp
        @foreach($leadsByPriority as $priority => $count)
        <div class="r-progress-row">
            <div class="r-progress-label">
                <span>{{ __('messages.priority_' . $priority) }}</span>
                <span style="color:var(--text-muted);">{{ $count }}</span>
            </div>
            <div class="r-progress-track">
                <div class="r-progress-fill"
                    style="width:{{ ($count/$maxP)*100 }}%;
                    background: {{ $priority=='high' ? 'linear-gradient(90deg,#ef4444,#f87171)' : ($priority=='medium' ? 'linear-gradient(90deg,#f59e0b,#fbbf24)' : 'linear-gradient(90deg,#10b981,#34d399)') }};">
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div class="g-empty" style="padding:40px 0;"><i class="fas fa-flag"></i><p>{{ __('messages.no_data') }}</p></div>
        @endif
    </div>
</div>

{{-- ROW 2: Campaign Status + 7-day Trend --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

    {{-- Campaign by Status --}}
    <div class="g-panel g-panel-p">
        <h3 class="rpt-heading"><i class="fas fa-bullhorn" style="color:#22d3ee;"></i> {{ __('messages.campaign_performance') }}</h3>
        @if(count($campaignsByStatus) > 0)
        @php $maxC = max(array_values($campaignsByStatus)) ?: 1; @endphp
        @foreach($campaignsByStatus as $status => $count)
        <div class="r-progress-row">
            <div class="r-progress-label">
                <span>{{ __('messages.campaign_' . $status) }}</span>
                <span style="color:var(--text-muted);">{{ $count }}</span>
            </div>
            <div class="r-progress-track">
                <div class="r-progress-fill" style="width:{{ ($count/$maxC)*100 }}%; background:linear-gradient(90deg, #6366f1, #22d3ee);"></div>
            </div>
        </div>
        @endforeach
        @else
        <div class="g-empty" style="padding:40px 0;"><i class="fas fa-bullhorn"></i><p>{{ __('messages.no_data') }}</p></div>
        @endif
    </div>

    {{-- Last 7 Days Bar Chart --}}
    <div class="g-panel g-panel-p">
        <h3 class="rpt-heading"><i class="fas fa-chart-line" style="color:#34d399;"></i> {{ __('messages.last_7_days') }}</h3>
        @if($last7Days->count() > 0)
        @php $maxDay = $last7Days->max('count') ?: 1; @endphp
        <div class="r-bar-chart">
            @foreach($last7Days as $day)
            <div class="r-bar-col">
                <div class="r-bar-val">{{ $day['count'] }}</div>
                <div class="r-bar" style="height:{{ max(8, ($day['count']/$maxDay)*100) }}%;"></div>
                <div class="r-bar-lbl">{{ $day['label'] }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="g-empty" style="padding:40px 0;"><i class="fas fa-chart-line"></i><p>{{ __('messages.no_data') }}</p></div>
        @endif
    </div>
</div>

{{-- ROW 3: Top Campaigns Table --}}
<div class="g-panel" style="margin-bottom:20px;">
    <div class="g-panel-p" style="padding-bottom:8px;">
        <h3 class="rpt-heading"><i class="fas fa-trophy" style="color:#fbbf24;"></i> {{ __('messages.top_campaigns') }}</h3>
    </div>
    @if($topCampaigns->count() > 0)
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.campaign_name') }}</th>
                    <th>{{ __('messages.budget') }}</th>
                    <th>{{ __('messages.reach') }}</th>
                    <th>{{ __('messages.leads_generated') }}</th>
                    <th>{{ __('messages.ctr') }}</th>
                    <th>{{ __('messages.cpl') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topCampaigns as $i => $campaign)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:26px; height:26px; border-radius:8px; background:{{ ['rgba(99,102,241,.2)','rgba(14,165,233,.2)','rgba(16,185,129,.2)'][$i % 3] }}; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:{{ ['#818cf8','#38bdf8','#34d399'][$i % 3] }};">
                                {{ $i + 1 }}
                            </div>
                            <span class="t-name">{{ $campaign->name }}</span>
                        </div>
                    </td>
                    <td class="t-muted">{{ number_format($campaign->budget) }}</td>
                    <td class="t-muted">{{ number_format($campaign->reach) }}</td>
                    <td>
                        <span class="g-pill g-pill-converted">{{ $campaign->leads_generated }}</span>
                    </td>
                    <td class="t-muted">{{ $campaign->ctr }}%</td>
                    <td style="color:#38bdf8; font-weight:700;">${{ $campaign->cpl }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="g-empty"><i class="fas fa-trophy"></i><h3>{{ __('messages.no_data') }}</h3></div>
    @endif
</div>

{{-- ROW 4: Company Performance Comparison Table --}}
<div class="g-panel" style="margin-bottom:20px;">
    <div class="g-panel-p" style="padding-bottom:8px;">
        <h3 class="rpt-heading"><i class="fas fa-building" style="color:#a855f7;"></i> {{ __('messages.company_performance') ?? 'Company Performance' }}</h3>
    </div>
    @if($companies->count() > 0)
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.company_name') }}</th>
                    <th>{{ __('messages.expenses') ?? 'Expenses (Spend)' }}</th>
                    <th>{{ __('messages.budget') ?? 'Budget' }}</th>
                    <th>{{ __('messages.campaigns') ?? 'Campaigns' }}</th>
                    <th>{{ __('messages.leads') ?? 'Leads' }}</th>
                    <th>{{ __('messages.cpl') ?? 'Avg CPL' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $i => $company)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg, rgba(168,85,247,0.2), rgba(217,70,239,0.2)); display:flex; align-items:center; justify-content:center; color:#d946ef;">
                                <i class="fas fa-building"></i>
                            </div>
                            <span class="t-name">{{ $company->name }}</span>
                        </div>
                    </td>
                    <td><span style="color:#f43f5e; font-weight:800;">${{ number_format($company->time_spend) }}</span></td>
                    <td class="t-muted">${{ number_format($company->time_budget) }}</td>
                    <td><span class="g-pill" style="background:rgba(99,102,241,0.1); color:#818cf8;">{{ $company->time_campaigns_count }}</span></td>
                    <td><span class="g-pill g-pill-new">{{ $company->time_leads_count }}</span></td>
                    <td class="t-muted">${{ number_format($company->cpl, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="g-empty"><i class="fas fa-building"></i><h3>{{ __('messages.no_data') }}</h3></div>
    @endif
</div>

{{-- ROW 5: Leads by Employee --}}
<div class="g-panel g-panel-p">
    <h3 class="rpt-heading"><i class="fas fa-user-tie" style="color:#818cf8;"></i> {{ __('messages.leads_by_employee') }}</h3>
    @if($employees->count() > 0)
    @php $maxEm = $employees->max('time_leads_count') ?: 1; @endphp
    @foreach($employees as $employee)
    <div class="r-progress-row">
        <div class="r-progress-label">
            <span>{{ $employee->name }}</span>
            <span style="color:var(--text-muted);">{{ $employee->time_leads_count }}</span>
        </div>
        <div class="r-progress-track">
            <div class="r-progress-fill" style="width:{{ ($employee->time_leads_count/$maxEm)*100 }}%; background:linear-gradient(90deg,#10b981,#34d399);"></div>
        </div>
    </div>
    @endforeach
    @else
    <div class="g-empty" style="padding:40px 0;"><i class="fas fa-user-tie"></i><p>{{ __('messages.no_data') }}</p></div>
    @endif
</div>

<style>
.rpt-heading {
    font-size: 15px; font-weight: 700;
    color: #fff; margin: 0 0 18px;
    display: flex; align-items: center; gap: 10px;
}
/* Stacked bar */
.r-bar-stack {
    display: flex; height: 10px;
    border-radius: 8px; overflow: hidden;
    margin-bottom: 16px;
    background: rgba(255,255,255,.05);
}
.r-seg { height:100%; transition: width .5s; }
/* Legend */
.r-legend {
    display: flex; flex-wrap: wrap; gap: 10px 16px;
}
.r-legend-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: rgba(255,255,255,.7);
}
.r-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
/* Progress rows */
.r-progress-row { margin-bottom: 14px; }
.r-progress-label {
    display: flex; justify-content: space-between;
    font-size: 13px; color: rgba(255,255,255,.8);
    margin-bottom: 6px;
}
.r-progress-track {
    height: 6px; background: rgba(255,255,255,.05);
    border-radius: 4px; overflow: hidden;
}
.r-progress-fill { height:100%; border-radius:4px; transition: width .5s; }
/* Vertical bar chart */
.r-bar-chart {
    display: flex; align-items: flex-end; gap: 8px;
    height: 130px; padding-bottom: 28px; position: relative;
}
.r-bar-col {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; gap: 4px;
    height: 100%;
    justify-content: flex-end;
    position: relative;
}
.r-bar {
    width: 100%;
    background: linear-gradient(180deg, #6366f1, rgba(99,102,241,.3));
    border-radius: 6px 6px 0 0;
    min-height: 6px;
    transition: height .5s;
}
.r-bar-val {
    font-size: 11px; font-weight: 700;
    color: rgba(255,255,255,.6);
    position: absolute; bottom: 28px; left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}
.r-bar-lbl {
    position: absolute; bottom: 6px;
    font-size: 10px; color: var(--text-muted);
    white-space: nowrap;
}
</style>

@endsection
