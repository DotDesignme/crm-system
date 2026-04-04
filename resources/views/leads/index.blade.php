@extends('layouts.app')
@section('page-title', __('messages.leads'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-users"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.leads') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_and_track_leads') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        @can('create-leads')
        <a href="{{ route('leads.create') }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.add_lead') }}
        </a>
        @endcan
    </div>
</div>

{{-- FILTER BAR --}}
<div class="g-panel mb-4">
    <form method="GET" class="filter-bar">
        <div class="filter-search">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="{{ __('messages.search_leads') }}" value="{{ request('search') }}">
        </div>

        <select name="status" class="filter-select">
            <option value="">{{ __('messages.all_statuses') }}</option>
            @foreach(['new','contacted','interested','not_interested','converted'] as $st)
                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                    {{ __('messages.status_' . $st) }}
                </option>
            @endforeach
        </select>

        <select name="campaign_id" class="filter-select">
            <option value="">{{ __('messages.all_campaigns') }}</option>
            @foreach($campaigns as $campaign)
                <option value="{{ $campaign->id }}" {{ request('campaign_id') == $campaign->id ? 'selected' : '' }}>
                    {{ $campaign->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="filter-btn filter-btn-ghost">
            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
        </button>

        @if(request('search') || request('status') || request('campaign_id'))
            <a href="{{ route('leads.index') }}" class="filter-btn filter-btn-danger" title="{{ __('messages.reset') }}">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.lead') }}</th>
                    <th>{{ __('messages.contact') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.source') }}</th>
                    <th>{{ __('messages.added_by') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td>
                        <div class="t-avatar-wrap">
                            <div class="t-avatar">{{ mb_substr($lead->name, 0, 1) }}</div>
                            <div>
                                <div class="t-name">{{ $lead->name }}</div>
                                <div class="t-sub">#{{ $lead->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if($lead->phone)
                                <a href="tel:{{ $lead->phone }}" class="t-muted" style="font-weight:600;">{{ $lead->phone }}</a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="wa-link">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @else
                                <span class="t-muted">—</span>
                            @endif
                        </div>
                        @if($lead->email)
                            <div class="t-sub">{{ $lead->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="g-pill g-pill-{{ $lead->status }}">
                            {{ __('messages.status_' . $lead->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="t-name" style="font-size:13px; color:var(--brand-cyan)">{{ $lead->campaign->name ?? '—' }}</div>
                        <div class="t-sub">{{ $lead->company->name ?? '—' }}</div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="t-avatar" style="width:28px; height:28px; font-size:11px; border-radius:8px;">
                                {{ mb_substr($lead->employee->name ?? '?', 0, 1) }}
                            </div>
                            <span class="t-muted">{{ $lead->employee->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="t-muted" style="font-weight:600;">{{ $lead->created_at->format('d M, Y') }}</div>
                        <div class="t-sub">{{ $lead->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('leads.show', $lead) }}" class="g-btn-icon g-btn-icon-view" title="{{ __('messages.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('edit-leads')
                            <a href="{{ route('leads.edit', $lead) }}" class="g-btn-icon g-btn-icon-edit" title="{{ __('messages.edit') }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="g-empty">
                            <i class="fas fa-users-slash"></i>
                            <h3>{{ __('messages.no_leads_found') }}</h3>
                            <p>{{ __('messages.try_adjusting_filters') }}</p>
                            @can('create-leads')
                            <a href="{{ route('leads.create') }}" class="filter-btn filter-btn-primary">
                                <i class="fas fa-plus"></i> {{ __('messages.add_lead') }}
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(method_exists($leads, 'links'))
<div class="pagination" style="margin-top:24px;">{{ $leads->links() }}</div>
@endif

@endsection
