@extends('layouts.app')
@section('page-title', __('messages.system_settings'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-sliders-h"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.system_settings') }}</h1>
            <p class="page-shell-sub">{{ __('messages.control_system_identity') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="g-panel g-panel-p mb-4" style="border-color:rgba(34,197,94,.3); background:rgba(34,197,94,.08);">
    <div style="display:flex; align-items:center; gap:12px; color:#34d399;">
        <i class="fas fa-check-circle" style="font-size:18px;"></i>
        <span style="font-weight:600;">{{ session('success') }}</span>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- SIDEBAR TABS --}}
    <div class="col-lg-3">
        <div class="g-panel" style="padding:12px;">
            <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px;">
                @php
                    $tabs = [
                        ['id'=>'branding', 'icon'=>'fas fa-paint-brush', 'label'=>__('messages.system_branding')],
                        ['id'=>'company', 'icon'=>'fas fa-building', 'label'=>__('messages.customer_identity')],
                        ['id'=>'financials', 'icon'=>'fas fa-wallet', 'label'=>__('messages.financial_settings')],
                        ['id'=>'pipeline', 'icon'=>'fas fa-stream', 'label'=>__('messages.sales_pipeline')],
                        ['id'=>'loss_reasons', 'icon'=>'fas fa-times-circle', 'label'=>__('messages.loss_reasons')],
                        ['id'=>'health_score', 'icon'=>'fas fa-heartbeat', 'label'=>__('messages.health_score_rules')],
                        ['id'=>'plugins', 'icon'=>'fas fa-plug', 'label'=>__('Plugins & Modules')]
                    ];
                @endphp
                @foreach($tabs as $t)
                <li>
                    <a href="{{ route('settings.branding', ['tab'=>$t['id']]) }}" 
                       style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:10px; text-decoration:none; transition:all .2s;
                       {{ $activeTab == $t['id'] ? 'background:rgba(14,165,233,.15); color:var(--brand-cyan); border:1px solid rgba(14,165,233,.2); font-weight:800;' : 'color:var(--text-muted); font-weight:600; border:1px solid transparent;' }}">
                        <i class="{{ $t['icon'] }}" style="font-size:15px; {{ $activeTab == $t['id'] ? 'text-shadow:0 0 10px rgba(14,165,233,.5);' : 'opacity:.7;' }}"></i>
                        {{ $t['label'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- CONTENT AREA --}}
    <div class="col-lg-9">
        
        {{-- BRANDING TAB --}}
        @if($activeTab == 'branding')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-paint-brush" style="color:var(--brand-blue); font-size:18px;"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.system_branding') }}</h3>
            </div>
            <div style="padding:30px;">
                <form action="{{ route('settings.branding.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.identity_details') }}</h4>
                            <div class="mb-4">
                                <label class="gm-label">{{ __('messages.app_name') }}</label>
                                <input type="text" name="app_name" class="gm-input" value="{{ (is_array($settings) || $settings instanceof \Illuminate\Support\Collection) ? ($settings['app_name'] ?? config('app.name')) : config('app.name') }}" required placeholder="e.g. Growth OS">
                            </div>
                            <div class="mb-4">
                                <label class="gm-label">Brand Slogan (Login Page)</label>
                                <input type="text" name="system_slogan" class="gm-input" value="{{ $settings['system_slogan'] ?? 'Elevate Your Business Workflow with Next-Generation Intelligence and Seamless Management.' }}" placeholder="e.g. The Smarter Way to Manage Leads">
                            </div>
                            <div class="mb-4">
                                <label class="gm-label">{{ __('messages.system_icon') ?? 'System Icon' }} (FontAwesome)</label>
                                <input type="text" name="system_icon" class="gm-input" value="{{ $settings['system_icon'] ?? 'fas fa-layer-group' }}" placeholder="e.g. fas fa-rocket">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="gm-label">Primary Color</label>
                                    <div style="display:flex; gap:10px;">
                                        <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#1d4ed8' }}" style="width:44px; height:44px; padding:0; border:none; background:none; cursor:pointer; border-radius:8px;">
                                        <input type="text" value="{{ $settings['primary_color'] ?? '#1d4ed8' }}" class="gm-input" style="flex:1;" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="gm-label">Accent Color</label>
                                    <div style="display:flex; gap:10px;">
                                        <input type="color" name="accent_color" value="{{ $settings['accent_color'] ?? '#0ea5e9' }}" style="width:44px; height:44px; padding:0; border:none; background:none; cursor:pointer; border-radius:8px;">
                                        <input type="text" value="{{ $settings['accent_color'] ?? '#0ea5e9' }}" class="gm-input" style="flex:1;" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.visual_assets') }}</h4>
                            <div class="mb-4">
                                <label class="gm-label">{{ __('messages.logo') }}</label>
                                @if(isset($settings['system_logo']))
                                    <div style="margin-bottom:12px; padding:12px; background:rgba(255,255,255,.05); border-radius:10px; display:inline-block;">
                                        <img src="{{ asset('storage/'.$settings['system_logo']) }}" style="max-height:40px;">
                                    </div>
                                @endif
                                <input type="file" name="logo" class="gm-input" style="padding:10px;">
                            </div>
                            <div class="mb-4">
                                <label class="gm-label">{{ __('messages.favicon') }}</label>
                                @if(isset($settings['system_favicon']))
                                    <div style="margin-bottom:12px; padding:12px; background:rgba(255,255,255,.05); border-radius:10px; display:inline-flex; align-items:center; gap:12px;">
                                        <img src="{{ asset('storage/'.$settings['system_favicon']) }}" style="width:32px; height:32px;">
                                        <span class="t-sub">Current Favicon</span>
                                    </div>
                                @endif
                                <input type="file" name="favicon" class="gm-input" style="padding:10px;">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:30px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <div>
                            <button type="button" class="filter-btn" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.7);" onclick="if(confirm('Refresh system cache to apply all branding changes?')) document.getElementById('refresh-cache-form').submit();">
                                <i class="fas fa-sync-alt"></i> {{ __('messages.refresh_cache') ?? 'Refresh System Cache' }}
                            </button>
                        </div>
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
                <form id="refresh-cache-form" action="{{ route('settings.refresh-cache') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
        @endif

        {{-- COMPANY TAB --}}
        @if($activeTab == 'company')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-building" style="color:#fbbf24; font-size:18px;"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.customer_identity') }}</h3>
            </div>
            <div style="padding:30px;">
                <form action="{{ route('settings.company.update') }}" method="POST">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.contact_details') }}</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.company_name') }}</label>
                                <input type="text" name="company_name" class="gm-input" value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.company_phone') }}</label>
                                <input type="text" name="company_phone" class="gm-input" value="{{ $settings['company_phone'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.company_email') }}</label>
                                <input type="email" name="company_email" class="gm-input" value="{{ $settings['company_email'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.legal_info') }}</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.tax_registration_number') }}</label>
                                <input type="text" name="company_tax_id" class="gm-input" value="{{ $settings['company_tax_id'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.commercial_registration') }}</label>
                                <input type="text" name="company_cr_number" class="gm-input" value="{{ $settings['company_cr_number'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.company_address') }}</label>
                                <textarea name="company_address" class="gm-input" rows="2">{{ $settings['company_address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:30px; text-align:right; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- FINANCIALS TAB --}}
        @if($activeTab == 'financials')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-wallet" style="color:#34d399; font-size:18px;"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.financial_settings') }}</h3>
            </div>
            <div style="padding:30px;">
                <form action="{{ route('settings.financials.update') }}" method="POST">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.currency') }}</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.currency_code') }}</label>
                                <input type="text" name="system_currency" class="gm-input" value="{{ $settings['system_currency'] ?? 'USD' }}" placeholder="USD">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.currency_symbol') }}</label>
                                <input type="text" name="system_currency_symbol" class="gm-input" value="{{ $settings['system_currency_symbol'] ?? '$' }}" placeholder="$">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">{{ __('messages.global_taxes') }}</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.vat_percentage') }} (%)</label>
                                <input type="number" step="0.01" name="system_vat_percentage" class="gm-input" value="{{ $settings['system_vat_percentage'] ?? '14' }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.wht_percentage') }} (%)</label>
                                <input type="number" step="0.01" name="system_wht_percentage" class="gm-input" value="{{ $settings['system_wht_percentage'] ?? '1' }}">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:30px; text-align:right; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- HEALTH SCORE TAB --}}
        @if($activeTab == 'health_score')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-heartbeat" style="color:#f87171; font-size:18px;"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.health_score_rules') }}</h3>
            </div>
            <div style="padding:30px;">
                <form action="{{ route('settings.health-score.update') }}" method="POST">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">Stage Scores</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.score_new_lead') }}</label>
                                <input type="number" name="health_score_new" class="gm-input" value="{{ $settings['health_score_new'] ?? 10 }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.score_contacted') }}</label>
                                <input type="number" name="health_score_contacted" class="gm-input" value="{{ $settings['health_score_contacted'] ?? 30 }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.score_interested') }}</label>
                                <input type="number" name="health_score_interested" class="gm-input" value="{{ $settings['health_score_interested'] ?? 60 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 style="font-size:14px; color:#fff; font-weight:800; margin-bottom:20px;">Additional Metrics</h4>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.score_converted') }}</label>
                                <input type="number" name="health_score_converted" class="gm-input" style="border-color:rgba(16,185,129,.5); color:#34d399;" value="{{ $settings['health_score_converted'] ?? 100 }}">
                            </div>
                            <div class="mb-3">
                                <label class="gm-label">{{ __('messages.activity_weight') }}</label>
                                <input type="number" name="health_score_activity_weight" class="gm-input" style="color:var(--brand-cyan);" value="{{ $settings['health_score_activity_weight'] ?? 5 }}">
                                <span class="t-sub" style="display:block; margin-top:6px;">Points added for each logged activity.</span>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:30px; text-align:right; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- PIPELINE TAB --}}
        @if($activeTab == 'pipeline')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-stream" style="color:#0ea5e9; font-size:18px;"></i>
                    <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.sales_pipeline') }}</h3>
                </div>
                <button type="button" class="filter-btn filter-btn-primary" onclick="openAddStageModal()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_stage') }}
                </button>
            </div>
            <div style="padding:20px;">
                <p class="t-sub" style="margin-bottom:20px;"><i class="fas fa-info-circle"></i> {{ __('messages.drag_to_reorder') }}</p>
                <div id="stages-list" style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($dealStages as $stage)
                        <div class="stage-item" data-id="{{ $stage->id }}" style="background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08); border-left:4px solid {{ $stage->color }}; border-radius:12px; padding:16px; display:flex; align-items:center; gap:16px;">
                            <div class="drag-handle" style="cursor:grab; color:var(--text-muted); font-size:16px;"><i class="fas fa-grip-vertical"></i></div>
                            <div style="flex-grow:1;">
                                <div class="t-name">{{ $stage->name }}</div>
                                @if($stage->is_won || $stage->is_lost)
                                <div style="margin-top:4px; display:flex; gap:8px;">
                                    @if($stage->is_won) <span class="g-pill g-pill-converted" style="font-size:10px; padding:2px 8px;">WON</span> @endif
                                    @if($stage->is_lost) <span class="g-pill g-pill-overdue" style="font-size:10px; padding:2px 8px;">LOST</span> @endif
                                </div>
                                @endif
                            </div>
                            <div class="g-act-row">
                                <button type="button" class="g-btn-icon g-btn-icon-edit" onclick='openEditStageModal(@json($stage))'><i class="fas fa-pen"></i></button>
                                <form action="{{ route('settings.stages.destroy', $stage) }}" method="POST" style="margin:0;" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="g-btn-icon g-btn-icon-delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="g-panel" style="margin-top:24px;">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; align-items:center; gap:12px;">
                <i class="fas fa-filter" style="color:var(--brand-blue); font-size:18px;"></i>
                <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.duplicate_checking_rules') ?? 'Duplicate Validation Rules' }}</h3>
            </div>
            <div style="padding:30px;">
                <form action="{{ route('settings.workflow.update') }}" method="POST">
                    @csrf
                    <p class="t-sub" style="margin-bottom:24px;">{{ __('messages.duplicate_checking_desc') ?? 'Select which fields must be unique across leads (OR logic applies when multiple are selected).' }}</p>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label style="display:flex; align-items:center; gap:8px; color:#fff; font-weight:600; cursor:pointer;">
                                <input type="checkbox" name="lead_dup_name" {{ ($settings['lead_dup_name'] ?? '0') == '1' ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--brand-cyan);">
                                {{ __('messages.check_dup_name') ?? 'Check by Name' }}
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label style="display:flex; align-items:center; gap:8px; color:#fff; font-weight:600; cursor:pointer;">
                                <input type="checkbox" name="lead_dup_phone" {{ ($settings['lead_dup_phone'] ?? '1') == '1' ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--brand-cyan);">
                                {{ __('messages.check_dup_phone') ?? 'Check by Phone Number' }}
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label style="display:flex; align-items:center; gap:8px; color:#fff; font-weight:600; cursor:pointer;">
                                <input type="checkbox" name="lead_dup_email" {{ ($settings['lead_dup_email'] ?? '0') == '1' ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--brand-cyan);">
                                {{ __('messages.check_dup_email') ?? 'Check by Email Address' }}
                            </label>
                        </div>
                    </div>
                    <div style="text-align:right; border-top:1px solid rgba(255,255,255,.05); padding-top:20px;">
                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- LOSS REASONS TAB --}}
        @if($activeTab == 'loss_reasons')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-times-circle" style="color:#ef4444; font-size:18px;"></i>
                    <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.loss_reasons') }}</h3>
                </div>
                <button type="button" class="filter-btn filter-btn-primary" onclick="openAddReasonModal()">
                    <i class="fas fa-plus"></i> {{ __('messages.add_loss_reason') }}
                </button>
            </div>
            <div class="g-table-wrap">
                <table class="g-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.reason_text') }}</th>
                            <th style="text-align:center;">{{ __('messages.status') }}</th>
                            <th style="text-align:right;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lossReasons as $reason)
                        <tr>
                            <td class="t-name">{{ $reason->reason }}</td>
                            <td style="text-align:center;">
                                @if($reason->is_active)
                                    <span class="g-pill g-pill-converted">{{ __('messages.active') }}</span>
                                @else
                                    <span class="g-pill" style="background:rgba(255,255,255,.1); color:rgba(255,255,255,.5);">{{ __('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="g-act-row" style="justify-content:flex-end;">
                                    <button type="button" class="g-btn-icon g-btn-icon-edit" onclick='openEditReasonModal(@json($reason))'><i class="fas fa-pen"></i></button>
                                    <form action="{{ route('settings.reasons.destroy', $reason) }}" method="POST" style="margin:0;" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="g-btn-icon g-btn-icon-delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        {{-- PLUGINS TAB --}}
        @if($activeTab == 'plugins')
        <div class="g-panel">
            <div style="padding:20px; border-bottom:1px solid rgba(255,255,255,.05); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-plug" style="color:#a855f7; font-size:18px;"></i>
                    <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('Plugins & Modules') }}</h3>
                </div>
                <button type="button" class="filter-btn filter-btn-primary" onclick="document.getElementById('uploadPluginModal').classList.add('show')">
                    <i class="fas fa-upload"></i> {{ __('Upload Plugin (.zip)') }}
                </button>
            </div>
            <div style="padding:20px;">
                @if(session('error'))
                    <div class="alert badge-urgent mb-4" style="width:100%; padding:15px;">{{ session('error') }}</div>
                @endif
                <div class="row g-4">
                    @forelse($plugins ?? [] as $plugin)
                        <div class="col-md-6">
                            <div class="glass-card" style="padding:20px; border:1px solid {{ $plugin->is_active ? 'rgba(168,85,247,0.3)' : 'rgba(255,255,255,0.05)' }}; transform:none !important;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                                    <div style="flex:1;">
                                        <h4 style="margin:0; font-size:16px; color:#fff;">{{ $plugin->name }} <span style="font-size:11px; opacity:0.5;">v{{ $plugin->version }}</span></h4>
                                        <p style="font-size:12px; color:var(--text-muted); margin:4px 0 0; line-height:1.4;">{{ $plugin->description }}</p>
                                    </div>
                                    <div style="margin-left:15px;">
                                        <form action="{{ route('settings.plugins.toggle', $plugin) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; cursor:pointer; color:{{ $plugin->is_active ? '#a855f7' : 'var(--text-muted)' }}; font-size:28px; transition:0.3s; padding:0;">
                                                <i class="fas fa-toggle-{{ $plugin->is_active ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
                                    <span class="badge {{ $plugin->is_active ? 'badge-interested' : 'badge-low' }}" style="font-size:10px;">
                                        {{ $plugin->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                    </span>
                                    <form action="{{ route('settings.plugins.delete', $plugin) }}" method="POST" onsubmit="return confirm('Delete this plugin and all its files?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:#ef4444; font-size:12px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:6px;">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div style="text-align:center; padding:60px 20px; background:rgba(255,255,255,0.02); border-radius:20px; border:1px dashed rgba(255,255,255,0.1);">
                                <i class="fas fa-puzzle-piece" style="font-size:48px; color:var(--text-muted); margin-bottom:16px; opacity:0.3;"></i>
                                <h4 style="color:var(--text-muted); margin-bottom:8px;">No Plugins Installed</h4>
                                <p style="font-size:14px; color:var(--text-muted);">Expand your CRM by uploading a new module.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@include('settings.partials.modals')

{{-- UPLOAD PLUGIN MODAL --}}
<div id="uploadPluginModal" class="modal-overlay gm-overlay">
    <div class="glass-card" style="width:100%; max-width:450px; padding:30px; position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <h3 style="margin:0; font-size:18px; color:#fff;"><i class="fas fa-upload" style="color:var(--primary-light);"></i> Upload New Plugin</h3>
            <button type="button" class="btn-logout" onclick="closeModal('uploadPluginModal')" style="padding:5px;"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="{{ route('settings.plugins.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="gm-label">Plugin File (.zip)</label>
                <div style="background:rgba(0,0,0,0.2); border:2px dashed rgba(255,255,255,0.1); border-radius:12px; padding:30px; text-align:center; position:relative; transition:0.3s;" onmouseover="this.style.borderColor='var(--primary-light)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
                    <i class="fas fa-file-archive" style="font-size:32px; color:var(--text-muted); margin-bottom:12px;"></i>
                    <p style="font-size:13px; color:var(--text-muted); margin:0;">Drag and drop or click to select</p>
                    <input type="file" name="plugin_zip" accept=".zip" required style="position:absolute; inset:0; opacity:0; cursor:pointer;">
                </div>
            </div>
            
            <div style="display:flex; gap:12px; margin-top:30px;">
                <button type="button" class="btn btn-ghost" style="flex:1;" onclick="closeModal('uploadPluginModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Upload & Register</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Native sortable logic for pipeline
        const stagesList = document.getElementById('stages-list');
        if (stagesList && typeof Sortable !== 'undefined') {
            new Sortable(stagesList, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    const ids = Array.from(stagesList.children).map(el => el.dataset.id);
                    fetch('{{ route("settings.stages.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: ids })
                    });
                }
            });
        }

        // Close on backdrop click
        document.querySelectorAll('.gm-overlay').forEach(o => {
            o.addEventListener('click', e => { if(e.target === o) o.classList.remove('show'); });
        });
    });

    function openAddStageModal() {
        document.getElementById('stageModal').classList.add('show');
        document.getElementById('stageForm').action = '{{ route("settings.stages.store") }}';
        document.getElementById('stageMethod').value = 'POST';
        document.getElementById('stageForm').reset();
    }
    
    function openEditStageModal(stage) {
        document.getElementById('stageModal').classList.add('show');
        document.getElementById('stageForm').action = `/settings/stages/${stage.id}`;
        document.getElementById('stageMethod').value = 'PUT';
        document.querySelector('#stageModal [name="name"]').value = stage.name;
        document.querySelector('#stageModal [name="color"]').value = stage.color;
        document.querySelector('#stageModal [name="is_won"]').checked = !!stage.is_won;
        document.querySelector('#stageModal [name="is_lost"]').checked = !!stage.is_lost;
    }
    
    function openAddReasonModal() {
        document.getElementById('reasonModal').classList.add('show');
        document.getElementById('reasonForm').action = '{{ route("settings.reasons.store") }}';
        document.getElementById('reasonMethod').value = 'POST';
        document.getElementById('reasonForm').reset();
    }
    
    function openEditReasonModal(reason) {
        document.getElementById('reasonModal').classList.add('show');
        document.getElementById('reasonForm').action = `/settings/reasons/${reason.id}`;
        document.getElementById('reasonMethod').value = 'PUT';
        document.querySelector('#reasonModal [name="reason"]').value = reason.reason;
        document.querySelector('#reasonModal [name="is_active"]').checked = !!reason.is_active;
    }
</script>
@endsection
