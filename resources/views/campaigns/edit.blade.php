@extends('layouts.app')
@section('page-title', __('messages.edit_campaign'))

@section('content')
<div class="page-header">
    <div>
        <h2>{{ __('messages.edit_campaign') }}</h2>
        <p>{{ $campaign->name }}</p>
    </div>
    <a href="{{ route('campaigns.index') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        {{ __('messages.back') }}
    </a>
</div>

<div class="glass-card" style="max-width: 780px;">
    <form method="POST" action="{{ route('campaigns.update', $campaign) }}">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name) }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('messages.budget') }}</label>
                <input type="number" name="budget" class="form-control" value="{{ old('budget', $campaign->budget) }}" step="0.01" min="0">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.currency') }}</label>
                <select name="currency" class="form-control">
                    @foreach(['EGP','USD','EUR'] as $c)
                    <option value="{{ $c }}" {{ old('currency', $campaign->currency) == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('messages.status') }}</label>
                <select name="status" class="form-control">
                    @foreach(['active','paused','completed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $campaign->status) == $s ? 'selected' : '' }}>{{ __('messages.status_' . $s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('messages.platforms') }}</label>
            <div style="display: flex; flex-wrap: wrap; gap: 16px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid var(--glass-border);">
                @php $oldPlatforms = old('platforms', $campaign->platforms ?? []); @endphp
                @foreach(['facebook','instagram','google','tiktok','youtube','twitter','linkedin','snapchat'] as $platform)
                <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text-secondary); cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="platforms[]" value="{{ $platform }}"
                        {{ (is_array($oldPlatforms) && in_array($platform, $oldPlatforms)) ? 'checked' : '' }}
                        style="accent-color: var(--primary);">
                    <i class="fab fa-{{ $platform }}" style="color: var(--primary-light);"></i> {{ ucfirst($platform) }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('messages.description') }}</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $campaign->description) }}</textarea>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.reach') }}</label>
                <input type="number" name="reach" class="form-control" value="{{ old('reach', $campaign->reach) }}" min="0">
            </div>
            <div class="form-group">
                <label>{{ __('messages.impressions') }}</label>
                <input type="number" name="impressions" class="form-control" value="{{ old('impressions', $campaign->impressions) }}" min="0">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.clicks') }}</label>
                <input type="number" name="clicks" class="form-control" value="{{ old('clicks', $campaign->clicks) }}" min="0">
            </div>
            <div class="form-group">
                <label>{{ __('messages.conversions') }}</label>
                <input type="number" name="conversions" class="form-control" value="{{ old('conversions', $campaign->conversions) }}" min="0">
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('messages.leads_generated') }}</label>
            <input type="number" name="leads_generated" class="form-control" value="{{ old('leads_generated', $campaign->leads_generated) }}" min="0" style="max-width: 360px;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
            <i class="fas fa-save"></i>
            {{ __('messages.save') }}
        </button>
    </form>
</div>
@endsection
