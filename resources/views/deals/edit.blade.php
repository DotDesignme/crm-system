@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 20px 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #fff; font-weight: 800; font-size: 32px; margin-bottom: 8px;">{{ __('messages.edit_deal') }}</h1>
            <p style="color: var(--text-muted); font-size: 14px;">{{ $deal->title }}</p>
        </div>
        <a href="{{ route('deals.show', $deal) }}" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="glass-card" style="padding: 40px; border-radius: 32px; border: 1px solid var(--glass-border); max-width: 800px;">
        <form action="{{ route('deals.update', $deal) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid-2" style="gap: 24px; margin-bottom: 24px;">
                <div class="form-group">
                    <label class="label-muted">{{ __('messages.title') }}</label>
                    <input type="text" name="title" class="form-control form-control-recessed" value="{{ $deal->title }}" required>
                </div>
                <div class="form-group">
                    <label class="label-muted">{{ __('messages.value') }} ({{ \App\Models\SystemSetting::get('system_currency', 'EGP') }})</label>
                    <input type="number" step="0.01" name="value" class="form-control form-control-recessed" value="{{ $deal->value }}">
                </div>
            </div>

            <div class="grid-2" style="gap: 24px; margin-bottom: 24px;">
                <div class="form-group">
                    <label class="label-muted">{{ __('messages.stage') }}</label>
                    <select name="deal_stage_id" class="form-control form-control-recessed" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" @if($deal->deal_stage_id == $stage->id) selected @endif>
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label-muted">{{ __('messages.expected_close_date') }}</label>
                    <input type="date" name="expected_close_date" class="form-control form-control-recessed" value="{{ $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : '' }}">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="label-muted">{{ __('messages.description') }}</label>
                <textarea name="description" class="form-control form-control-recessed" rows="4">{{ $deal->description }}</textarea>
            </div>

            <div style="display: flex; gap: 16px; justify-content: flex-end; margin-top: 40px;">
                <a href="{{ route('deals.show', $deal) }}" class="btn btn-link" style="color: var(--text-muted); text-decoration: none; font-weight: 700;">{{ __('messages.cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-glow" style="padding: 12px 40px; border-radius: 14px;">
                    {{ __('messages.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
