@extends('layouts.app')
@section('page-title', __('messages.employees'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-blue"><i class="fas fa-users-gear"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.team_performance') }}</h1>
            <p class="page-shell-sub">{{ __('messages.admin_management') }}</p>
        </div>
    </div>
    <div class="page-shell-right" style="background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.05); border-radius:14px; padding:6px; display:flex; gap:6px; align-items:center;">
        <div class="view-toggles" style="display:flex; border-right:1px solid rgba(255,255,255,.1); padding-right:6px; margin-right:6px;">
            <button class="view-toggle active" id="view-grid" onclick="switchView('grid')" style="background:transparent; border:none; width:36px; height:36px; border-radius:10px; color:var(--text-muted); cursor:pointer; transition:.3s;"><i class="fas fa-th-large"></i></button>
            <button class="view-toggle" id="view-list" onclick="switchView('list')" style="background:transparent; border:none; width:36px; height:36px; border-radius:10px; color:var(--text-muted); cursor:pointer; transition:.3s;"><i class="fas fa-list"></i></button>
        </div>
        <button class="nav-toggle-btn active" id="btn-employees" onclick="switchMainTab('employees')" style="background:transparent; border:none; padding:8px 16px; border-radius:10px; color:var(--text-muted); font-weight:700; cursor:pointer; transition:.3s;">
            <i class="fas fa-user-tie" style="margin-right:6px;"></i> {{ __('messages.employees') }}
        </button>
        <button class="nav-toggle-btn" id="btn-companies" onclick="switchMainTab('companies')" style="background:transparent; border:none; padding:8px 16px; border-radius:10px; color:var(--text-muted); font-weight:700; cursor:pointer; transition:.3s;">
            <i class="fas fa-building" style="margin-right:6px;"></i> {{ __('messages.companies') }}
        </button>
    </div>
</div>

<style>
    .view-toggle.active { background:var(--brand-blue) !important; color:#fff !important; }
    .nav-toggle-btn.active { background:rgba(255,255,255,.1) !important; color:#fff !important; }
    .view-content, .tab-section { display:none; }
    .view-content.active, .tab-section.active { display:block; animation:fadeIn .3s ease; }
    @keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }

    .emp-grid-card { background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.05); border-radius:16px; padding:20px; transition:.3s; text-align:center; }
    .emp-grid-card:hover { border-color:var(--brand-cyan); background:rgba(14,165,233,.02); transform:translateY(-4px); }
</style>

{{-- STATS ROW --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="height:100%; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-50px; right:-50px; width:150px; height:150px; background:var(--brand-blue); filter:blur(60px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px; line-height:1;">{{ __('messages.total_employees') }}</div>
            <div style="font-size:32px; font-weight:900; color:#fff; line-height:1;">{{ $employees->count() }}</div>
            <div style="margin-top:8px; color:var(--success); font-size:12px; font-weight:700;"><i class="fas fa-check-circle"></i> {{ __('messages.status_active') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="height:100%; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-50px; right:-50px; width:150px; height:150px; background:var(--brand-cyan); filter:blur(60px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px; line-height:1;">{{ __('messages.companies') }}</div>
            <div style="font-size:32px; font-weight:900; color:var(--brand-cyan); line-height:1;">{{ $companies->count() }}</div>
            <div style="margin-top:8px; color:var(--text-muted); font-size:12px; font-weight:600;"><i class="fas fa-sitemap"></i> {{ __('messages.manage_branches') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="height:100%; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-50px; right:-50px; width:150px; height:150px; background:#fbbf24; filter:blur(60px); opacity:0.1; border-radius:50%;"></div>
            <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px; line-height:1;">{{ __('messages.performance') }}</div>
            <div style="font-size:32px; font-weight:900; color:#fbbf24; line-height:1;">{{ round($systemPerformancePercent) }}%</div>
            <div style="margin-top:8px; color:var(--text-muted); font-size:12px; font-weight:600;"><i class="fas fa-chart-line"></i> {{ __('messages.target_amount') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <button onclick="openCreateModal()" class="g-panel g-panel-p" style="height:100%; width:100%; text-align:left; cursor:pointer; background:rgba(14,165,233,.02); border:1px solid rgba(14,165,233,.1); display:flex; align-items:center; justify-content:space-between; transition:.3s;">
            <div>
                <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-bottom:8px; color:var(--brand-cyan);" id="add-label">{{ __('messages.add_employee') }}</div>
                <div style="font-size:24px; font-weight:900; color:#fff;">{{ __('messages.add') }}</div>
            </div>
            <div style="width:48px; height:48px; border-radius:14px; background:rgba(14,165,233,.1); color:var(--brand-cyan); display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fas fa-plus"></i>
            </div>
        </button>
    </div>
</div>

{{-- EMPLOYEES TAB --}}
<div id="section-employees" class="tab-section active">
    
    {{-- GRID VIEW --}}
    <div id="employees-grid" class="view-content active">
        <div class="row g-4">
            @foreach($employees as $emp)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="emp-grid-card">
                    <div style="position:relative; width:64px; height:64px; border-radius:16px; background:linear-gradient(135deg,var(--brand-blue),var(--brand-cyan)); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; color:#fff; margin:0 auto 16px;">
                        {{ mb_substr($emp->name, 0, 1) }}
                        <div style="position:absolute; bottom:-4px; right:-4px; width:18px; height:18px; border-radius:50%; background:{{ $emp->is_active ? 'var(--success)' : '#ef4444' }}; border:3px solid var(--bg-card);"></div>
                    </div>
                    <div class="t-name" style="font-size:16px;">{{ $emp->name }}</div>
                    <div class="t-sub" style="font-size:12px; margin-top:4px;"><i class="fas fa-building"></i> {{ $emp->companies->count() > 0 ? $emp->companies->pluck('name')->join(', ') : ($emp->company->name ?? '-') }}</div>
                    
                    <div style="display:flex; justify-content:center; gap:8px; margin-top:20px; padding-bottom:20px; border-bottom:1px solid rgba(255,255,255,.05);">
                        <button class="g-btn-icon g-btn-icon-edit" onclick='editEmployee(@json($emp))'><i class="fas fa-pen"></i></button>
                        <button class="g-btn-icon g-btn-icon-view" style="color:#fbbf24;" onclick='openResetPasswordModal({{ $emp->id }}, "{{ addslashes($emp->name) }}")'><i class="fas fa-key"></i></button>
                        <a href="{{ route('employees.activity-log', $emp) }}" class="g-btn-icon g-btn-icon-view"><i class="fas fa-history"></i></a>
                    </div>

                    <div style="margin-top:20px; text-align:left;">
                        @php 
                            $target = $emp->currentTarget();
                            $wonValue = $emp->won_deals_value ?? 0;
                            $progress = $emp->performance_progress ?? 0;
                        @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span class="t-sub text-uppercase" style="font-size:10px; font-weight:800;">Target</span>
                            <span style="font-weight:800; font-size:11px; color:{{ $progress > 80 ? 'var(--success)' : 'var(--brand-cyan)' }};">{{ round($progress) }}%</span>
                        </div>
                        <div style="height:6px; background:rgba(255,255,255,.05); border-radius:10px; overflow:hidden;">
                            <div style="height:100%; width:{{ $progress }}%; background:{{ $progress > 80 ? 'var(--success)' : 'var(--brand-cyan)' }}; border-radius:10px;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
                            <span style="font-size:12px; font-weight:800; color:#fff;">{{ number_format(optional($target)->target_amount ?? 0) }} <span class="t-sub" style="font-size:10px;">{{ $system_branding['system_currency_symbol'] ?? '' }}</span></span>
                            <button style="background:transparent; border:none; color:var(--brand-cyan); font-size:11px; font-weight:800; cursor:pointer;" onclick="openTargetModal({{ $emp->id }}, '{{ addslashes($emp->name) }}', {{ optional($target)->target_amount ?? 0 }}, {{ optional($target)->commission_percentage ?? 0 }})">
                                <i class="fas fa-bullseye"></i> Set
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- LIST VIEW --}}
    <div id="employees-list" class="view-content">
        <div class="g-panel">
            <div class="g-table-wrap">
                <table class="g-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.employee') }}</th>
                            <th>{{ __('messages.company') }}</th>
                            <th>{{ __('messages.performance') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:right">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,var(--brand-blue),var(--brand-cyan)); display:flex; align-items:center; justify-content:center; font-weight:800; color:#fff;">
                                        {{ mb_substr($emp->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="t-name">{{ $emp->name }}</div>
                                        <div class="t-sub">{{ $emp->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="g-pill" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">
                                    <i class="fas fa-building" style="color:var(--brand-cyan);"></i> {{ $emp->companies->count() > 0 ? $emp->companies->pluck('name')->join(', ') : ($emp->company->name ?? '-') }}
                                </span>
                            </td>
                            <td>
                                @php 
                                    $target = $emp->currentTarget();
                                    $progress = $emp->performance_progress ?? 0;
                                @endphp
                                <div style="width:120px;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:11px; font-weight:800;">
                                        <span class="t-name">{{ number_format(optional($target)->target_amount ?? 0) }}</span>
                                        <span style="color:var(--brand-cyan);">{{ $progress }}%</span>
                                    </div>
                                    <div style="height:4px; background:rgba(255,255,255,.05); border-radius:10px;">
                                        <div style="height:100%; width:{{ $progress }}%; background:var(--brand-cyan); border-radius:10px;"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($emp->is_active)
                                    <span class="g-pill g-pill-converted"><i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i> {{ __('messages.active') }}</span>
                                @else
                                    <span class="g-pill g-pill-overdue"><i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i> {{ __('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="g-act-row" style="justify-content:flex-end;">
                                    <button class="g-btn-icon g-btn-icon-edit" onclick='editEmployee(@json($emp))'><i class="fas fa-pen"></i></button>
                                    <button class="g-btn-icon g-btn-icon-view" style="color:#fbbf24;" onclick='openResetPasswordModal({{ $emp->id }}, "{{ addslashes($emp->name) }}")'><i class="fas fa-key"></i></button>
                                    <a href="{{ route('employees.activity-log', $emp) }}" class="g-btn-icon g-btn-icon-view"><i class="fas fa-history"></i></a>
                                    <form action="{{ route('employees.toggle-status', $emp) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="g-btn-icon {{ $emp->is_active ? 'g-btn-icon-delete' : 'g-btn-icon-view' }}" style="{{ $emp->is_active ? '' : 'color:var(--success);' }}">
                                            <i class="fas {{ $emp->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- COMPANIES TAB --}}
<div id="section-companies" class="tab-section">
    <div class="row g-4">
        @foreach($companies as $company)
        <div class="col-xl-4 col-lg-6">
            <div class="emp-grid-card" style="text-align:left;">
                <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px;">
                    <div style="width:64px; height:64px; border-radius:16px; background:rgba(14,165,233,.1); color:var(--brand-cyan); display:flex; align-items:center; justify-content:center; font-size:24px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h4 style="margin:0 0 8px; font-size:18px; font-weight:800; color:#fff;">{{ $company->name }}</h4>
                        <div style="display:flex; gap:8px;">
                            <span class="g-pill" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">{{ $company->employees_count }} Team</span>
                            <span class="g-pill" style="background:rgba(251,191,36,.1); color:#fbbf24;">{{ $company->leads_count }} Leads</span>
                        </div>
                    </div>
                </div>

                <div class="t-sub text-uppercase" style="font-size:10px; font-weight:800; margin-bottom:12px;">Team Members</div>
                <div style="display:flex; gap:6px; margin-bottom:24px;">
                    @foreach($company->employees->take(5) as $mem)
                        <div style="width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; font-weight:800;" title="{{ $mem->name }}">
                            {{ mb_substr($mem->name, 0, 1) }}
                        </div>
                    @endforeach
                    @if($company->employees->count() > 5)
                        <div style="width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,.05); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:11px; font-weight:800;">
                            +{{ $company->employees->count() - 5 }}
                        </div>
                    @endif
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                    <button class="filter-btn filter-btn-ghost" onclick='editCompany(@json($company))'><i class="fas fa-pen"></i></button>
                    <form action="{{ route('companies.destroy', $company) }}" method="POST" style="margin:0;" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="filter-btn filter-btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@include('employees.modals')

@endsection

@section('scripts')
<script>
    let activeMainTab = 'employees';
    let currentView = 'grid';

    function switchView(view) {
        currentView = view;
        document.querySelectorAll('.view-toggle').forEach(b => b.classList.remove('active'));
        document.getElementById('view-' + view).classList.add('active');
        
        document.querySelectorAll('.view-content').forEach(c => c.classList.remove('active'));
        document.getElementById('employees-' + view).classList.add('active');
    }

    function switchMainTab(tab) {
        activeMainTab = tab;
        document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + tab).classList.add('active');
        
        document.querySelectorAll('.nav-toggle-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('btn-' + tab).classList.add('active');

        const label = tab === 'employees' ? "{{ __('messages.add_employee') }}" : "{{ __('messages.add_company') }}";
        document.getElementById('add-label').innerText = label;
    }
</script>
@endsection
