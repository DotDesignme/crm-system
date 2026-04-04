@extends('layouts.app')
@section('page-title', __('messages.contracts'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-amber"><i class="fas fa-file-contract"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.contracts') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_contracts') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <button class="filter-btn filter-btn-primary" onclick="document.getElementById('addModal').classList.add('show')">
            <i class="fas fa-plus"></i> {{ __('messages.add_contract') }}
        </button>
    </div>
</div>

{{-- EXPIRY WARNING --}}
@if($expiringSoon > 0)
<div class="g-panel g-panel-p mb-4" style="border-color:rgba(245,158,11,.3); background:rgba(245,158,11,.08);">
    <div style="display:flex; align-items:center; gap:12px; color:#fbbf24;">
        <i class="fas fa-exclamation-triangle" style="font-size:18px;"></i>
        <span style="font-weight:600;">{{ trans_choice('messages.contracts_expiring_soon', $expiringSoon, ['count' => $expiringSoon]) }}</span>
    </div>
</div>
@endif

{{-- DATA TABLE --}}
<div class="g-panel">
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.contract_number') }}</th>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.company') }}</th>
                    <th>{{ __('messages.value') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.start_date') }}</th>
                    <th>{{ __('messages.end_date') }}</th>
                    <th>{{ __('messages.renewal_in') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td><span class="t-name">{{ $contract->contract_number }}</span></td>
                    <td class="t-muted" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $contract->title }}</td>
                    <td class="t-muted">{{ $contract->company->name ?? '—' }}</td>
                    <td>
                        <span style="font-weight:800; color:var(--brand-cyan);">
                            {{ number_format($contract->value, 2) }}
                        </span>
                        <span class="t-sub" style="display:inline;">{{ $contract->currency ?? 'EGP' }}</span>
                    </td>
                    <td>
                        <span class="g-pill g-pill-{{ $contract->status }}">
                            {{ __('messages.' . $contract->status) }}
                        </span>
                    </td>
                    <td class="t-muted">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d M Y') : '—' }}</td>
                    <td class="t-muted">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d M Y') : '—' }}</td>
                    <td>
                        @if($contract->days_until_renewal !== null && $contract->days_until_renewal >= 0)
                            @php $d = $contract->days_until_renewal; @endphp
                            <span class="g-pill {{ $d <= 30 ? 'g-pill-overdue' : ($d <= 90 ? 'g-pill-partial' : 'g-pill-sent') }}">
                                {{ $d }} {{ __('messages.days') }}
                            </span>
                        @else
                            <span class="t-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('contracts.show', $contract) }}" class="g-btn-icon g-btn-icon-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('contracts.edit', $contract) }}" class="g-btn-icon g-btn-icon-edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" style="display:inline"
                                onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="g-empty">
                        <i class="fas fa-file-contract"></i>
                        <h3>{{ __('messages.no_contracts') }}</h3>
                        <p>{{ __('messages.add_first_contract') }}</p>
                        <button class="filter-btn filter-btn-primary" onclick="document.getElementById('addModal').classList.add('show')">
                            <i class="fas fa-plus"></i> {{ __('messages.add_contract') }}
                        </button>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($contracts, 'links'))
<div class="pagination" style="margin-top:24px;">{{ $contracts->links() }}</div>
@endif

{{-- ADD CONTRACT MODAL --}}
<div class="gm-overlay" id="addModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-file-contract" style="color:#fbbf24"></i>
                {{ __('messages.add_contract') }}
            </div>
            <button class="gm-close" onclick="document.getElementById('addModal').classList.remove('show')">&#215;</button>
        </div>
        <form action="{{ route('contracts.store') }}" method="POST">
            @csrf
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.title') }} *</label>
                    <input type="text" name="title" class="gm-input" required value="{{ old('title') }}">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.company') }}</label>
                        <select name="customer_id" class="gm-input">
                            <option value="">{{ __('messages.select_company') }}</option>
                            @foreach($customers as $company)
                                <option value="{{ $company->id }}" {{ old('customer_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.deal') }}</label>
                        <select name="deal_id" class="gm-input">
                            <option value="">{{ __('messages.select_deal') }}</option>
                            @foreach($deals as $deal)
                                <option value="{{ $deal->id }}" {{ old('deal_id') == $deal->id ? 'selected' : '' }}>{{ $deal->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.value') }}</label>
                        <input type="number" name="value" class="gm-input" step="0.01" min="0" value="{{ old('value', 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.currency') }}</label>
                        <select name="currency" class="gm-input">
                            <option value="EGP" {{ old('currency','EGP') == 'EGP' ? 'selected' : '' }}>EGP</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" class="gm-input" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.end_date') }}</label>
                        <input type="date" name="end_date" class="gm-input" value="{{ old('end_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.renewal_period_months') }}</label>
                        <input type="number" name="renewal_period_months" class="gm-input" min="1" value="{{ old('renewal_period_months', 12) }}">
                    </div>
                    <div class="col-md-6" style="display:flex; align-items:flex-end; padding-bottom:4px;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; color:rgba(255,255,255,.7); font-size:14px;">
                            <input type="checkbox" name="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }}
                                style="width:16px; height:16px; accent-color:var(--brand-cyan);">
                            {{ __('messages.auto_renew') }}
                        </label>
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.terms') }}</label>
                        <textarea name="terms" class="gm-input" rows="2">{{ old('terms') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" class="gm-input" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost"
                    onclick="document.getElementById('addModal').classList.remove('show')">
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script>
document.querySelectorAll('.gm-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) o.classList.remove('show'); });
});
</script>
@endsection
