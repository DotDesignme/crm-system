@extends('layouts.app')
@section('page-title', __('messages.edit_customer'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('customers.index') }}" class="btn btn-glass-sm text-white">
            <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-white mb-0">{{ __('messages.edit_customer') }}</h2>
            <p class="text-muted small mb-0">{{ $customer->name }}</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="glass-card-static border-0 shadow-lg p-4">
            <form method="POST" action="{{ route('customers.update', $customer) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.customer_name') }} *</label>
                        <input type="text" name="name" class="form-control glass-input" value="{{ old('name', $customer->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.industry') }}</label>
                        <input type="text" name="industry" class="form-control glass-input" value="{{ old('industry', $customer->industry) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.email') }}</label>
                        <input type="email" name="email" class="form-control glass-input" value="{{ old('email', $customer->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" class="form-control glass-input" value="{{ old('phone', $customer->phone) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-white-50 small">{{ __('messages.address') }}</label>
                        <textarea name="address" class="form-control glass-input" rows="3">{{ old('address', $customer->address) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.health_score') }}</label>
                        <select name="health_score" class="form-select glass-select">
                            <option value="hot" {{ (old('health_score', $customer->health_score) == 'hot') ? 'selected' : '' }}>{{ __('messages.health_hot') }}</option>
                            <option value="warm" {{ (old('health_score', $customer->health_score) == 'warm') ? 'selected' : '' }}>{{ __('messages.health_warm') }}</option>
                            <option value="cold" {{ (old('health_score', $customer->health_score) == 'cold') ? 'selected' : '' }}>{{ __('messages.health_cold') }}</option>
                            <option value="churning" {{ (old('health_score', $customer->health_score) == 'churning') ? 'selected' : '' }}>{{ __('messages.health_churning') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.status') }} *</label>
                        <select name="status" class="form-select glass-select" required>
                            <option value="active" {{ (old('status', $customer->status) == 'active') ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
                            <option value="inactive" {{ (old('status', $customer->status) == 'inactive') ? 'selected' : '' }}>{{ __('messages.status_inactive') }}</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <hr class="border-white-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">* {{ __('messages.required') }}</span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('customers.index') }}" class="btn btn-glass-secondary px-4">{{ __('messages.cancel') }}</a>
                                <button type="submit" class="btn btn-glass-primary px-5">
                                    <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
