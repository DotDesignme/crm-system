@extends('layouts.app')
@section('page-title', __('messages.companies'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-building"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.companies') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_internal_branches') ?? 'Manage your internal branches and legal entities' }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        @if(auth()->user()->is_admin)
        <button class="filter-btn filter-btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus-circle"></i> {{ __('messages.add_company') }}
        </button>
        @endif
    </div>
</div>

<div class="g-panel">
    @if($companies->count())
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('messages.company_name') }}</th>
                    <th>{{ __('messages.company_url') }}</th>
                    <th style="text-align:center;">{{ __('messages.employees_count') }}</th>
                    <th style="text-align:center;">{{ __('messages.leads_count') }}</th>
                    @if(auth()->user()->is_admin)
                    <th style="text-align:right;">{{ __('messages.actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td style="color:var(--text-muted); font-size:12px;">{{ $company->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, var(--brand-blue), var(--brand-cyan)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:900; font-size:15px; box-shadow:0 0 15px rgba(14,165,233,.2);">
                                {{ mb_substr($company->name, 0, 1) }}
                            </div>
                            <div class="t-name" style="font-size:15px;">{{ $company->name }}</div>
                        </div>
                    </td>
                    <td>
                        @if($company->url)
                        <a href="{{ $company->url }}" target="_blank" style="color:var(--brand-cyan); text-decoration:none; font-size:13px; font-weight:600; display:flex; align-items:center; gap:6px;">
                            <i class="fas fa-external-link-alt" style="font-size:10px; opacity:.7;"></i> {{ Str::limit($company->url, 25) }}
                        </a>
                        @else 
                        <span style="color:var(--text-muted); font-size:13px;">—</span> 
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="g-pill" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); font-weight:800; min-width:40px;">{{ $company->employees_count }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="g-pill g-pill-converted" style="font-weight:800; min-width:40px;">{{ $company->leads_count }}</span>
                    </td>
                    @if(auth()->user()->is_admin)
                    <td>
                        <div class="g-act-row" style="justify-content:flex-end;">
                            <a href="{{ route('companies.show', $company) }}" class="g-btn-icon" style="color:var(--brand-cyan);" title="{{ __('messages.view_details') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="g-btn-icon g-btn-icon-edit" onclick='openEditModal(@json($company))' title="{{ __('messages.edit') }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('companies.destroy', $company) }}" style="margin:0;" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="g-empty">
        <i class="fas fa-building"></i>
        <h3>{{ __('messages.no_companies') ?? 'No branches added yet' }}</h3>
        <p>{{ __('messages.add_company_desc') ?? 'Divide your leads and team across multiple business units or branches.' }}</p>
        @if(auth()->user()->is_admin)
        <button class="filter-btn filter-btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus-circle"></i> {{ __('messages.add_company') }}
        </button>
        @endif
    </div>
    @endif
</div>

{{-- MODAL --}}
<div class="gm-overlay" id="companyModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title" id="modalTitle">
                <i class="fas fa-plus-circle" style="color:var(--brand-cyan);"></i>
                {{ __('messages.add_company') }}
            </div>
            <button class="gm-close" onclick="closeModal()">&#215;</button>
        </div>
        <form method="POST" action="{{ route('companies.store') }}" id="companyForm">
            @csrf
            <div id="methodField"></div>
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.company_name') }} *</label>
                    <input type="text" name="name" id="company_name" class="gm-input" required>
                </div>
                <div class="mb-0">
                    <label class="gm-label">{{ __('messages.company_url') }}</label>
                    <input type="url" name="url" id="company_url" class="gm-input" placeholder="https://example.com">
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                <button type="submit" class="filter-btn filter-btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openAddModal() {
        document.getElementById('companyModal').classList.add('show');
        resetForm();
    }
    
    function closeModal() {
        document.getElementById('companyModal').classList.remove('show');
    }

    function resetForm() {
        const form = document.getElementById('companyForm');
        form.action = "{{ route('companies.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:var(--brand-cyan);"></i> {{ __('messages.add_company') }}';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> {{ __('messages.save') }}';
        form.reset();
    }

    function openEditModal(company) {
        const form = document.getElementById('companyForm');
        form.action = `/companies/${company.id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen" style="color:var(--brand-cyan);"></i> {{ __('messages.edit_company') }}';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> {{ __('messages.update') }}';
        
        document.getElementById('company_name').value = company.name;
        document.getElementById('company_url').value = company.url || '';
        
        document.getElementById('companyModal').classList.add('show');
    }

    document.getElementById('companyModal').addEventListener('click', function(e) {
        if(e.target === this) closeModal();
    });
</script>
@endsection
