@extends('layouts.app')
@section('page-title', $company->name)

@section('content')

{{-- PAGE HEADER WITH BREADCRUMBS --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-building"></i></div>
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                <a href="{{ route('companies.index') }}" style="color:var(--text-muted); text-decoration:none; font-size:12px; font-weight:700;">{{ __('messages.companies') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size:10px; color:var(--text-muted); opacity:.5;"></i>
                <span style="color:var(--brand-cyan); font-size:12px; font-weight:800;">{{ $company->name }}</span>
            </div>
            <h1 class="page-shell-title" style="margin:0;">{{ $company->name }}</h1>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('companies.activity-log', $company) }}" class="filter-btn filter-btn-primary" style="background:rgba(255,255,255,.05); color:#fff; border:1px solid rgba(255,255,255,.1);">
            <i class="fas fa-history"></i> {{ __('messages.activity_log') }}
        </a>
    </div>
</div>

{{-- TOP STATS ROW --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="text-align:center;">
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:8px;">{{ __('messages.leads_count') }}</div>
            <div style="font-size:28px; font-weight:900; color:#fff;">{{ number_format($company->leads_count) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="text-align:center;">
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:8px;">{{ __('messages.won_deals') }}</div>
            <div style="font-size:28px; font-weight:900; color:var(--success);">{{ number_format($wonDealsCount) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="text-align:center;">
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:8px;">{{ __('messages.revenue') }}</div>
            <div style="font-size:28px; font-weight:900; color:var(--brand-cyan);">{{ number_format($totalRevenue, 2) }} <span style="font-size:12px; font-weight:600; opacity:.6;">{{ $settings['system_currency_symbol'] ?? '' }}</span></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="g-panel g-panel-p" style="text-align:center;">
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:8px;">{{ __('messages.employees_count') }}</div>
            <div style="font-size:28px; font-weight:900; color:#fff;">{{ number_format($company->employees_count) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- SETTINGS FORM --}}
    <div class="col-lg-7">
        <div class="g-panel" style="padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.02); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-sliders-h" style="color:var(--brand-cyan);"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.branch_settings') ?? 'Branch Settings' }}</h3>
            </div>
            <div style="padding:30px;">
                <form method="POST" action="{{ route('companies.update', $company) }}">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="gm-label">{{ __('messages.company_name') }} *</label>
                        <input type="text" name="name" class="gm-input" value="{{ $company->name }}" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="gm-label">{{ __('messages.company_url') }}</label>
                        <input type="url" name="url" class="gm-input" value="{{ $company->url }}" placeholder="https://...">
                    </div>

                    <div class="mb-5">
                        <label class="gm-label">{{ __('messages.brand_color') ?? 'Brand Color' }}</label>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="color" name="brand_color" style="width:48px; height:48px; border:1px solid var(--glass-border); border-radius:10px; background:rgba(255,255,255,.05); cursor:pointer; padding:4px;" value="{{ $company->brand_color ?? '#0ea5e9' }}">
                            <input type="text" class="gm-input font-monospace" style="flex:1;" value="{{ $company->brand_color ?? '#0ea5e9' }}" readonly>
                        </div>
                    </div>

                    <div style="text-align:right; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <button type="submit" class="filter-btn filter-btn-primary" style="padding:12px 30px; font-weight:900;">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TEAM LIST --}}
    <div class="col-lg-5">
        <div class="g-panel" style="padding:0; overflow:hidden; height:100%;">
            <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.02); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-users-gear" style="color:var(--brand-blue);"></i>
                    <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.branch_team') ?? 'Branch Team' }}</h3>
                </div>
                <span class="g-pill" style="background:rgba(255,255,255,.05); font-weight:800;">{{ $employees->count() }}</span>
            </div>

            <div style="padding:16px; max-height:450px; overflow-y:auto;">
                @forelse($employees as $emp)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px; border-radius:14px; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.05); margin-bottom:10px; transition:.2s; cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background='rgba(255,255,255,.02)'">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,.05); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:900; border:1px solid rgba(255,255,255,.1);">
                            {{ mb_substr($emp->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="t-name" style="font-size:14px;">{{ $emp->name }}</div>
                            <div class="t-sub" style="font-size:11px;">{{ $emp->job_title ?? __('messages.employee') }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:var(--brand-cyan); font-weight:900; font-size:13px;">{{ $emp->leads_count }}</div>
                        <div class="t-sub" style="font-size:10px; text-uppercase:true; font-weight:800;">{{ __('messages.leads') }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 opacity-50">
                    <i class="fas fa-user-slash" style="font-size:32px; margin-bottom:12px;"></i>
                    <p class="small" style="color:var(--text-muted);">{{ __('messages.no_employees_assigned') ?? 'No employees assigned to this branch.' }}</p>
                </div>
                @endforelse
            </div>
            
            <div style="padding:16px; text-align:center; border-top:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.01);">
                <a href="{{ route('employees.index', ['company_id' => $company->id]) }}" style="color:var(--brand-cyan); text-decoration:none; font-size:13px; font-weight:800; display:flex; align-items:center; justify-content:center; gap:8px;">
                    {{ __('messages.manage_team') }} <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}" style="font-size:10px;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
