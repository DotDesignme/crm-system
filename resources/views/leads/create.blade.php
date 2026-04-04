@extends('layouts.app')
@section('page-title', __('messages.add_lead'))

@section('content')
<div class="page-header">
    <div>
        <h2>{{ __('messages.add_lead') }}</h2>
    </div>
    <a href="{{ route('leads.index') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        {{ __('messages.back') }}
    </a>
</div>

<div class="glass-card" style="max-width: 720px;">
    <form method="POST" action="{{ route('leads.store') }}">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('messages.phone') }}</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="01012345678">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@email.com">
            </div>
            <div class="form-group">
                <label>{{ __('messages.source') }}</label>
                <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="Facebook, Google, etc.">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.status') }}</label>
                <select name="status" class="form-control">
                    @foreach(['new','contacted','interested','not_interested','converted'] as $s)
                    <option value="{{ $s }}" {{ old('status') == $s ? 'selected' : '' }}>{{ __('messages.status_' . $s) }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(auth()->user()->is_admin)
            <div class="form-group">
                <label>{{ __('messages.company') ?? 'Company' }} *</label>
                <select name="company_id" id="company_id_select" class="form-control" required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        
        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.select_campaign') }}</label>
                <select name="campaign_id" id="campaign_id_select" class="form-control">
                    <option value="">{{ __('messages.none') }}</option>
                    @foreach($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" data-company="{{ $campaign->company_id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('messages.priority') }}</label>
                <select name="priority" class="form-control">
                    @foreach(['low','medium','high','urgent'] as $p)
                    <option value="{{ $p }}" {{ old('priority', 'medium') == $p ? 'selected' : '' }}>{{ __('messages.priority_' . $p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>{{ __('messages.tag') }}</label>
                <input type="text" name="tag" class="form-control" value="{{ old('tag') }}" placeholder="VIP, Hot Lead, etc.">
            </div>
            <div class="form-group">
                <label>{{ __('messages.follow_up') }}</label>
                <input type="datetime-local" name="follow_up_at" class="form-control" value="{{ old('follow_up_at') }}">
            </div>
        </div>

        @if(auth()->user()->is_admin)
        <div class="form-group">
            <label>{{ __('messages.assigned_to') }}</label>
            <select name="added_by" id="employee_id_select" class="form-control">
                <option value="{{ auth()->id() }}" data-company="">{{ auth()->user()->name }} ({{ __('messages.me') }})</option>
                @foreach($employees as $emp)
                <option value="{{ $emp->id }}" data-company="{{ $emp->company_id }}" {{ old('added_by') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="form-group">
            <label>{{ __('messages.notes') }}</label>
            <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
            <i class="fas fa-plus-circle"></i>
            {{ __('messages.add_lead') }}
        </button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company_id_select');
    const campaignSelect = document.getElementById('campaign_id_select');
    const employeeSelect = document.getElementById('employee_id_select');
    
    if(companySelect) {
        // Store original options
        const campaignOptions = Array.from(campaignSelect.querySelectorAll('option[data-company]'));
        const employeeOptions = employeeSelect ? Array.from(employeeSelect.querySelectorAll('option[data-company]')) : [];
        
        companySelect.addEventListener('change', function() {
            const companyId = this.value;
            
            // Filter campaigns
            campaignOptions.forEach(opt => {
                if(companyId && opt.getAttribute('data-company') != companyId) {
                    opt.style.display = 'none';
                } else {
                    opt.style.display = '';
                }
            });
            campaignSelect.value = '';
            
            // Filter employees
            if(employeeSelect) {
                employeeOptions.forEach(opt => {
                    if(opt.value != "{{ auth()->id() }}" && companyId && opt.getAttribute('data-company') != companyId) {
                        opt.style.display = 'none';
                    } else {
                        opt.style.display = '';
                    }
                });
                employeeSelect.value = "{{ auth()->id() }}";
            }
        });
        
        // Trigger initial filtering if old value
        if(companySelect.value) {
            companySelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection
