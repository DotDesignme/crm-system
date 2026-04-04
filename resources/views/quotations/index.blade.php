@extends('layouts.app')
@section('page-title', __('messages.quotations'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-blue"><i class="fas fa-file-invoice"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.quotations') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_quotations') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('quotations.create') }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.create_quotation') }}
        </a>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.quotation_number') }}</th>
                    <th>{{ __('messages.company') }}</th>
                    <th>{{ __('messages.total') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.valid_until') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $quotation)
                <tr>
                    <td>
                        <div class="t-name">{{ $quotation->quotation_number }}</div>
                    </td>
                    <td>
                        <div class="t-muted" style="font-weight:600;">{{ $quotation->company->name ?? '—' }}</div>
                        @if($quotation->deal)
                        <div class="t-sub" style="color:var(--brand-cyan);">{{ $quotation->deal->title }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:15px; font-weight:900; color:var(--brand-cyan);">
                            {{ number_format($quotation->total, 2) }}
                            <span style="font-size:10px; opacity:.6; font-weight:500;">{{ $system_branding['system_currency_symbol'] ?? '' }}</span>
                        </span>
                    </td>
                    <td>
                        <span class="g-pill g-pill-{{ $quotation->status }}">
                            {{ __('messages.' . $quotation->status) }}
                        </span>
                    </td>
                    <td class="t-muted">
                        {{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d M, Y') : '—' }}
                    </td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('quotations.show', $quotation) }}" class="g-btn-icon g-btn-icon-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('quotations.download', $quotation) }}" class="g-btn-icon g-btn-icon-edit" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" style="display:inline"
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
                        <i class="fas fa-file-invoice"></i>
                        <h3>{{ __('messages.no_quotations') }}</h3>
                        <p>{{ __('messages.create_first_quotation') }}</p>
                        <a href="{{ route('quotations.create') }}" class="filter-btn filter-btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.create_quotation') }}
                        </a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($quotations, 'links'))
<div class="pagination" style="margin-top:24px;">{{ $quotations->links() }}</div>
@endif

@endsection
