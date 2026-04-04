@extends('layouts.app')
@section('page-title', __('messages.edit_lead'))

@section('content')
<div class="page-header">
    <div>
        <h2>{{ __('messages.edit_lead') }}</h2>
        <p>{{ $lead->name }}</p>
    </div>
    <a href="{{ route('leads.index') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        {{ __('messages.back') }}
    </a>
</div>

<div class="glass-card" style="max-width: 640px;">
    <form method="POST" action="{{ route('leads.update', $lead) }}">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('messages.phone') }}</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
            </div>
            <div class="form-group">
                <label>{{ __('messages.status') }}</label>
                <select name="status" class="form-control">
                    @foreach(['new','contacted','interested','not_interested','converted'] as $s)
                    <option value="{{ $s }}" {{ old('status', $lead->status) == $s ? 'selected' : '' }}>{{ __('messages.status_' . $s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('messages.select_campaign') }}</label>
                <select name="campaign_id" class="form-control">
                    <option value="">{{ __('messages.none') }}</option>
                    @foreach($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" {{ old('campaign_id', $lead->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('messages.notes') }}</label>
            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
            <i class="fas fa-save"></i>
            {{ __('messages.save') }}
        </button>
    </form>
</div>
@endsection
