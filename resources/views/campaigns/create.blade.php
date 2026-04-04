@extends('layouts.app')
@section('page-title', __('messages.add_campaign'))

@section('content')
<div class="page-header">
    <div>
        <h2>{{ __('messages.add_campaign') }}</h2>
    </div>
    <a href="{{ route('campaigns.index') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        {{ __('messages.back') }}
    </a>
</div>

<div class="glass-card" style="max-width: 780px;">
    <form method="POST" action="{{ route('campaigns.store') }}">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label><i class="fas fa-bullhorn" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-money-bill-wave" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.budget') }}</label>
                <input type="number" name="budget" class="form-control" value="{{ old('budget') }}" step="0.01" min="0">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label><i class="fas fa-coins" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.currency') }}</label>
                <select name="currency" class="form-control">
                    <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>EGP</option>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-flag" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.status') }}</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
                    <option value="paused" {{ old('status') == 'paused' ? 'selected' : '' }}>{{ __('messages.status_paused') }}</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label><i class="fas fa-calendar" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="form-group">
                <label><i class="fas fa-calendar-check" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-th-large" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.platforms') }}</label>
            <div style="display: flex; flex-wrap: wrap; gap: 16px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid var(--glass-border);">
                @foreach(['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'] as $platform)
                <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="platforms[]" value="{{ $platform }}"
                        {{ (is_array(old('platforms')) && in_array($platform, old('platforms'))) ? 'checked' : '' }}
                        style="accent-color: var(--primary);">
                    <i class="fab fa-{{ $platform }}" style="color: var(--primary-light);"></i> {{ ucfirst($platform) }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-align-left" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.description') }}</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label><i class="fas fa-eye" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.reach') }}</label>
                <input type="number" name="reach" class="form-control" value="{{ old('reach', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label><i class="fas fa-chart-bar" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.impressions') }}</label>
                <input type="number" name="impressions" class="form-control" value="{{ old('impressions', 0) }}" min="0">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label><i class="fas fa-mouse-pointer" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.clicks') }}</label>
                <input type="number" name="clicks" class="form-control" value="{{ old('clicks', 0) }}" min="0">
            </div>
            <div class="form-group">
                <label><i class="fas fa-exchange-alt" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.conversions') }}</label>
                <input type="number" name="conversions" class="form-control" value="{{ old('conversions', 0) }}" min="0">
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user-plus" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px; opacity: 0.5;"></i> {{ __('messages.leads_generated') }}</label>
            <input type="number" name="leads_generated" class="form-control" value="{{ old('leads_generated', 0) }}" min="0" style="max-width: 360px;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
            <i class="fas fa-plus-circle"></i>
            {{ __('messages.add_campaign') }}
        </button>
    </form>
</div>
@endsection
