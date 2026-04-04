@extends('layouts.app')
@section('page-title', __('messages.team_performance'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-blue"><i class="fas fa-chart-line"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.team_performance') }}</h1>
            <p class="page-shell-sub">{{ __('messages.monitor_team_metrics') ?? 'Monitor your team metrics and activities' }}</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="position:relative; overflow:hidden;">
            <div style="position:absolute; top:-40px; right:-40px; width:120px; height:120px; background:var(--brand-blue); filter:blur(50px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px;">{{ __('messages.total_deals') }}</div>
            <div style="font-size:32px; font-weight:900; color:#fff; line-height:1;">{{ $leaderboard->sum('deals_count') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="position:relative; overflow:hidden;">
            <div style="position:absolute; top:-40px; right:-40px; width:120px; height:120px; background:var(--success); filter:blur(50px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px;">{{ __('messages.total_revenue') }}</div>
            <div style="font-size:32px; font-weight:900; color:var(--success); line-height:1;">{{ number_format($leaderboard->sum('deals_value'), 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="position:relative; overflow:hidden;">
            <div style="position:absolute; top:-40px; right:-40px; width:120px; height:120px; background:var(--brand-cyan); filter:blur(50px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px;">{{ __('messages.tasks_completed') }}</div>
            <div style="font-size:32px; font-weight:900; color:var(--brand-cyan); line-height:1;">{{ $leaderboard->sum('tasks_completed') }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="g-panel" style="padding:0; overflow:hidden; height:100%;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.02);">
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-trophy" style="color:#fbbf24;"></i> {{ __('messages.leaderboard') }}
                </h3>
            </div>
            <div class="g-table-wrap">
                <table class="g-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.deals') }}</th>
                            <th>{{ __('messages.revenue') }}</th>
                            <th>{{ __('messages.tasks') }}</th>
                            <th>{{ __('messages.commissions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $emp)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--brand-blue),var(--brand-cyan)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800;">
                                        {{ mb_substr($emp->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="t-name">{{ $emp->name }}</div>
                                        <div class="t-sub" style="margin-top:2px; font-size:11px;">{{ $emp->company->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="t-name" style="font-size:15px;">{{ $emp->deals_count }}</td>
                            <td style="font-weight:700; color:var(--success);">{{ number_format($emp->deals_value, 2) }}</td>
                            <td>
                                @php 
                                    $totalTasks = $emp->tasks_pending + $emp->tasks_completed;
                                    $pct = $totalTasks > 0 ? ($emp->tasks_completed / $totalTasks) * 100 : 0; 
                                @endphp
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                    <span style="font-size:11px; font-weight:800; color:#fff;">{{ $emp->tasks_completed }} / {{ $totalTasks }}</span>
                                    <span style="font-size:10px; font-weight:800; color:var(--brand-cyan);">{{ round($pct) }}%</span>
                                </div>
                                <div style="width:100px; height:4px; background:rgba(255,255,255,0.05); border-radius:10px;">
                                    <div style="width:{{ $pct }}%; height:100%; background:var(--brand-cyan); border-radius:10px;"></div>
                                </div>
                            </td>
                            <td><span class="g-pill" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">{{ number_format($emp->commissions, 2) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="g-panel" style="padding:0; overflow:hidden; height:100%;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.02);">
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-bolt" style="color:var(--brand-cyan);"></i> {{ __('messages.recent_activities') }}
                </h3>
            </div>
            <div style="padding:24px;">
                @forelse($todayActivities as $activity)
                <div style="display:flex; gap:16px; margin-bottom:20px; position:relative;">
                    @if(!$loop->last)
                        <div style="position:absolute; left:18px; top:36px; bottom:-16px; width:2px; background:var(--glass-border);"></div>
                    @endif
                    <div style="width:38px; height:38px; border-radius:12px; background:rgba(14,165,233,.1); color:var(--brand-cyan); display:flex; align-items:center; justify-content:center; z-index:1; border:1px solid rgba(14,165,233,.2);">
                        <i class="{{ $activity->icon }}"></i>
                    </div>
                    <div style="flex:1; padding-top:4px;">
                        <div style="font-size:14px; font-weight:600; color:#fff; margin-bottom:4px; line-height:1.4;">{{ $activity->subject }}</div>
                        <div style="font-size:11px; color:var(--text-muted); font-weight:500; display:flex; align-items:center; gap:6px;">
                            <span style="color:var(--text-secondary);">{{ $activity->employee->name }}</span>
                            <span>&middot;</span>
                            <span><i class="far fa-clock"></i> {{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="g-empty" style="padding:40px 20px;">
                    <i class="fas fa-check-circle" style="color:var(--text-muted); font-size:32px;"></i>
                    <h3 style="font-size:16px; margin-top:16px;">{{ __('messages.no_recent_activities') ?? 'No activities today' }}</h3>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
