@extends('layouts.app')
@section('page-title', __('messages.edit_employee'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('employees.index') }}" class="btn btn-glass-sm text-white">
            <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-white mb-0">{{ __('messages.edit_employee') }}</h2>
            <p class="text-muted small mb-0">{{ $employee->name }}</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="glass-card-static border-0 shadow-lg p-4">
            <form method="POST" action="{{ route('employees.update', $employee) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.employee_name') }} *</label>
                        <input type="text" name="name" class="form-control glass-input" value="{{ old('name', $employee->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.job_title') }}</label>
                        <input type="text" name="job_title" class="form-control glass-input" value="{{ old('job_title', $employee->job_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.username') }} *</label>
                        <input type="text" name="username" class="form-control glass-input" value="{{ old('username', $employee->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.phone_number') }}</label>
                        <input type="text" name="phone_number" class="form-control glass-input" value="{{ old('phone_number', $employee->phone_number) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.branch') }} *</label>
                        <select name="company_id" class="form-select glass-select" required>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $employee->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.status') }} *</label>
                        <select name="is_active" class="form-select glass-select" required>
                            <option value="1" {{ $employee->is_active ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
                            <option value="0" {{ !$employee->is_active ? 'selected' : '' }}>{{ __('messages.status_inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-12 mt-4">
                        <hr class="border-white-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">* {{ __('messages.required') }}</span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('employees.index') }}" class="btn btn-glass-secondary px-4">{{ __('messages.cancel') }}</a>
                                <button type="submit" class="btn btn-glass-primary px-5">
                                    <i class="fas fa-save me-2"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Password Reset Card -->
        <div class="glass-card-static border-0 shadow-lg p-4 mt-4">
            <h3 class="text-white fs-6 fw-bold mb-3">
                <i class="fas fa-key me-2 text-warning"></i> {{ __('messages.change_password') }}
            </h3>
            <form method="POST" action="{{ route('employees.update-password', $employee) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="password" name="password" class="form-control glass-input" placeholder="{{ __('messages.new_password') }}" required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-glass-warning w-100">{{ __('messages.update_password') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
