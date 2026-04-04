@extends('layouts.app')
@section('page-title', __('messages.edit_company'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('companies.index') }}" class="btn btn-glass-sm text-white">
            <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-white mb-0">{{ __('messages.edit_company') }}</h2>
            <p class="text-muted small mb-0">{{ $company->name }}</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="glass-card-static border-0 shadow-lg p-4">
            <form method="POST" action="{{ route('companies.update', $company) }}">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-4">
                    <label class="form-label text-white-50 small">{{ __('messages.company_name') }} *</label>
                    <input type="text" name="name" class="form-control glass-input" value="{{ old('name', $company->name) }}" required>
                </div>
                
                <div class="form-group mb-4">
                    <label class="form-label text-white-50 small">{{ __('messages.company_url') }}</label>
                    <input type="url" name="url" class="form-control glass-input" value="{{ old('url', $company->url) }}" placeholder="https://facebook.com/...">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5">
                    <span class="text-muted small">* {{ __('messages.required') }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('companies.index') }}" class="btn btn-glass-secondary px-4">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="btn btn-glass-primary px-5">
                            <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
