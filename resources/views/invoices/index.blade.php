@extends('layouts.app')
@section('page-title', __('messages.invoices'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-green"><i class="fas fa-file-invoice-dollar"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.invoices') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_invoices') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('invoices.create') }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.create_invoice') }}
        </a>
    </div>
</div>

{{-- SUMMARY STAT --}}
<div class="g-stat-row" style="margin-bottom:24px;">
    <div class="g-stat">
        <div class="g-stat-icon" style="background:rgba(248,113,113,.15); color:#f87171;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div class="g-stat-val" style="color:#f87171;">
                {{ number_format($totalOutstanding, 2) }}
                <span style="font-size:13px; font-weight:500; opacity:.6;">{{ $system_branding['system_currency_symbol'] ?? '' }}</span>
            </div>
            <div class="g-stat-lbl">{{ __('messages.total_outstanding') }}</div>
        </div>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.invoice_number') }}</th>
                    <th>{{ __('messages.company') }}</th>
                    <th>{{ __('messages.total') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.due_date') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>
                        <div class="t-name">{{ $invoice->invoice_number }}</div>
                    </td>
                    <td class="t-muted">{{ $invoice->company->name ?? '—' }}</td>
                    <td>
                        <span style="font-size:15px; font-weight:900; color:var(--brand-cyan);">
                            {{ number_format($invoice->total, 2) }}
                            <span style="font-size:10px; opacity:.6; font-weight:500;">{{ $system_branding['system_currency_symbol'] ?? '' }}</span>
                        </span>
                    </td>
                    <td>
                        <span class="g-pill g-pill-{{ $invoice->status }}">
                            {{ __('messages.' . $invoice->status) }}
                        </span>
                    </td>
                    <td class="t-muted">
                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') : '—' }}
                    </td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('invoices.show', $invoice) }}" class="g-btn-icon g-btn-icon-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display:inline"
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
                <tr><td colspan="6">
                    <div class="g-empty">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h3>{{ __('messages.no_invoices') }}</h3>
                        <p>{{ __('messages.create_first_invoice') }}</p>
                        <a href="{{ route('invoices.create') }}" class="filter-btn filter-btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.create_invoice') }}
                        </a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($invoices, 'links'))
<div class="pagination" style="margin-top:24px;">{{ $invoices->links() }}</div>
@endif

@endsection
