@extends('layouts.app')
@section('page-title', __('messages.campaigns'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-amber"><i class="fas fa-bullhorn"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.campaigns') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_campaigns') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('export.campaigns') }}" class="filter-btn filter-btn-ghost">
            <i class="fas fa-file-export"></i> {{ __('messages.export') }}
        </a>
        <button class="filter-btn filter-btn-primary" onclick="openModal('addModal', 'add')">
            <i class="fas fa-plus"></i> {{ __('messages.add_campaign') }}
        </button>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="g-panel mb-4">
    <form method="GET" class="filter-bar">
        <div class="filter-search">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="{{ __('messages.search_campaigns') }}" value="{{ request('search') }}">
        </div>
        <select name="status" class="filter-select">
            <option value="">{{ __('messages.all_statuses') }}</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
            <option value="paused"   {{ request('status') == 'paused'   ? 'selected' : '' }}>{{ __('messages.status_paused') }}</option>
            <option value="completed"{{ request('status') == 'completed'? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
        </select>
        <button type="submit" class="filter-btn filter-btn-ghost">
            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('campaigns.index') }}" class="filter-btn filter-btn-danger">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </form>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    @if($campaigns->count())
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.budget') }}</th>
                    <th>{{ __('messages.platforms') }}</th>
                    <th>{{ __('messages.dates') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.leads') }} / {{ __('messages.cpl') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                <tr>
                    <td>
                        <div class="t-name">{{ $campaign->name }}</div>
                        <div class="t-sub" style="font-size:11px;">#{{ $campaign->id }}</div>
                    </td>
                    <td>
                        <div style="font-weight:900; color:var(--brand-cyan); font-size:14px;">
                            {{ number_format($campaign->total_spend, 2) }} <span style="font-size:10px; opacity:.6;">{{ $campaign->currency }}</span>
                        </div>
                        <div class="t-sub">{{ __('messages.budget') }}: {{ number_format($campaign->budget) }}</div>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            @if($campaign->platforms)
                                @foreach($campaign->platforms as $platform)
                                    <div style="width:24px; height:24px; border-radius:6px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; color:var(--brand-cyan); font-size:12px;" title="{{ $platform }}">
                                        <i class="fab fa-{{ $platform }}"></i>
                                    </div>
                                @endforeach
                            @else
                                <span class="t-muted">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="t-muted" style="font-size:13px;">
                        @if($campaign->start_date) {{ $campaign->start_date->format('d M y') }} @endif
                        @if($campaign->end_date) — {{ $campaign->end_date->format('d M y') }} @endif
                    </td>
                    <td>
                        <span class="g-pill g-pill-{{ $campaign->status }}">
                            {{ __('messages.status_' . $campaign->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:4px;">
                            <div style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="fas fa-users" style="color:var(--text-muted); font-size:11px;"></i>
                                <span style="font-weight:700;">{{ $campaign->leads()->count() }}</span>
                            </div>
                            <div style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="fas fa-bullseye" style="color:var(--text-muted); font-size:11px;"></i>
                                <span style="color:#fbbf24; font-weight:700;">{{ number_format($campaign->cpl, 2) }}</span>
                                <span class="t-sub" style="font-size:10px;">{{ $campaign->currency }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="g-btn-icon g-btn-icon-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick='editCampaign(@json($campaign))' class="g-btn-icon g-btn-icon-edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(method_exists($campaigns, 'links'))
    <div class="pagination" style="margin-top:24px;">{{ $campaigns->links() }}</div>
    @endif
    @else
    <div class="g-empty">
        <i class="fas fa-bullhorn"></i>
        <h3>{{ __('messages.no_campaigns') }}</h3>
        <p>{{ __('messages.no_data') }}</p>
        <button class="filter-btn filter-btn-primary" onclick="openModal('addModal', 'add')">
            <i class="fas fa-plus"></i> {{ __('messages.add_campaign') }}
        </button>
    </div>
    @endif
</div>


{{-- SHARED ADD/EDIT MODAL --}}
<div class="gm-overlay" id="addModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title" id="modalTitle">
                <i class="fas fa-bullhorn" style="color:var(--brand-cyan);"></i>
                {{ __('messages.add_campaign') }}
            </div>
            <button class="gm-close" onclick="closeModal('addModal')">&#215;</button>
        </div>
        <form id="campaignForm" method="POST" action="{{ route('campaigns.store') }}">
            @csrf
            <div id="methodField"></div>
            <div class="gm-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.name') }} *</label>
                        <input type="text" name="name" id="c_name" class="gm-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.status') }}</label>
                        <select name="status" id="c_status" class="gm-input">
                            <option value="active">{{ __('messages.status_active') }}</option>
                            <option value="paused">{{ __('messages.status_paused') }}</option>
                            <option value="completed">{{ __('messages.status_completed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="gm-label">{{ __('messages.budget') }}</label>
                        <input type="number" name="budget" id="c_budget" class="gm-input" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="gm-label">{{ __('messages.total_spend') }} *</label>
                        <input type="number" name="total_spend" id="c_total_spend" class="gm-input" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="gm-label">{{ __('messages.currency') }}</label>
                        <select name="currency" id="c_currency" class="gm-input">
                            <option value="EGP">EGP</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" id="c_start_date" class="gm-input">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.end_date') }}</label>
                        <input type="date" name="end_date" id="c_end_date" class="gm-input">
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.platforms') }}</label>
                        <div style="display:flex; flex-wrap:wrap; gap:16px; padding:16px; border-radius:12px; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.05);">
                            @foreach(['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'] as $platform)
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-muted); font-size:14px; margin:0;">
                                <input type="checkbox" name="platforms[]" value="{{ $platform }}" id="c_plat_{{ $platform }}" style="accent-color:var(--brand-cyan); width:16px; height:16px;">
                                <i class="fab fa-{{ $platform }}" style="color:var(--brand-cyan);"></i> {{ ucfirst($platform) }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="gm-label">{{ __('messages.reach') }}</label>
                        <input type="number" name="reach" id="c_reach" class="gm-input" min="0">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="gm-label">{{ __('messages.impressions') }}</label>
                        <input type="number" name="impressions" id="c_impressions" class="gm-input" min="0">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="gm-label">{{ __('messages.clicks') }}</label>
                        <input type="number" name="clicks" id="c_clicks" class="gm-input" min="0">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="gm-label">{{ __('messages.conversions') }}</label>
                        <input type="number" name="conversions" id="c_conversions" class="gm-input" min="0">
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.leads_generated') }}</label>
                        <input type="number" name="leads_generated" id="c_leads_generated" class="gm-input" min="0">
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.description') }}</label>
                        <textarea name="description" id="c_description" class="gm-input" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('addModal')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="filter-btn filter-btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script>
function openModal(id, mode) {
    const m = document.getElementById(id);
    m.classList.add('show');
    if(mode === 'add') resetForm();
}
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function resetForm() {
    const f = document.getElementById('campaignForm');
    f.action = '{{ route("campaigns.store") }}';
    f.reset();
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-bullhorn" style="color:var(--brand-cyan)"></i> {{ __("messages.add_campaign") }}';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> {{ __("messages.save") }}';
}

function editCampaign(data) {
    const f = document.getElementById('campaignForm');
    f.action = `/campaigns/${data.id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen" style="color:#fbbf24"></i> {{ __("messages.edit_campaign") }}';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> {{ __("messages.update") }}';

    document.getElementById('c_name').value = data.name || '';
    document.getElementById('c_status').value = data.status || 'active';
    document.getElementById('c_budget').value = data.budget || '';
    document.getElementById('c_total_spend').value = data.total_spend || '';
    document.getElementById('c_currency').value = data.currency || 'EGP';
    document.getElementById('c_start_date').value = data.start_date ? data.start_date.substring(0,10) : '';
    document.getElementById('c_end_date').value = data.end_date ? data.end_date.substring(0,10) : '';
    document.getElementById('c_reach').value = data.reach || '';
    document.getElementById('c_impressions').value = data.impressions || '';
    document.getElementById('c_clicks').value = data.clicks || '';
    document.getElementById('c_conversions').value = data.conversions || '';
    document.getElementById('c_leads_generated').value = data.leads_generated || '';
    document.getElementById('c_description').value = data.description || '';

    // Platforms
    const plats = data.platforms || [];
    ['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'].forEach(p => {
        const cb = document.getElementById('c_plat_' + p);
        if(cb) cb.checked = plats.includes(p);
    });

    openModal('addModal', 'edit');
}

// Close on backdrop click
document.querySelectorAll('.gm-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) closeModal(o.id); });
});
</script>
@endsection
