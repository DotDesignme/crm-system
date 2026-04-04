@extends('layouts.app')
@section('page-title', __('messages.customers'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-violet"><i class="fas fa-user-tie"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.customers') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_external_clients') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <button class="filter-btn filter-btn-primary" onclick="openModal('addModal','add')">
            <i class="fas fa-user-plus"></i> {{ __('messages.add_customer') }}
        </button>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="g-panel mb-4">
    <form method="GET" class="filter-bar">
        <div class="filter-search">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="{{ __('messages.search_customers') }}" value="{{ request('search') }}">
        </div>
        <select name="status" class="filter-select">
            <option value="">{{ __('messages.all_statuses') }}</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.status_inactive') }}</option>
        </select>
        <button type="submit" class="filter-btn filter-btn-ghost">
            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('customers.index') }}" class="filter-btn filter-btn-danger">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </form>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    @if($customers->count())
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.customer_name') }}</th>
                    <th>{{ __('messages.contact_info') }}</th>
                    <th>{{ __('messages.health_score') }}</th>
                    <th>{{ __('messages.deals') }}</th>
                    <th>{{ __('messages.assigned_to') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>
                        <div class="t-avatar-wrap">
                            <div class="t-avatar t-avatar-violet">{{ mb_substr($customer->name, 0, 1) }}</div>
                            <div>
                                <a href="{{ route('customers.show', $customer) }}" class="t-name" style="text-decoration:none; display:block;">
                                    {{ $customer->name }}
                                </a>
                                <div class="t-sub">{{ $customer->industry ?? __('messages.no_industry') }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($customer->email)
                        <div class="t-muted" style="margin-bottom:3px;">
                            <i class="fas fa-envelope" style="opacity:.4; margin-right:6px; font-size:11px;"></i>{{ $customer->email }}
                        </div>
                        @endif
                        @if($customer->phone)
                        <div class="t-muted">
                            <i class="fas fa-phone" style="opacity:.4; margin-right:6px; font-size:11px;"></i>{{ $customer->phone }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @php $hs = $customer->health_score ?? 'unknown'; @endphp
                        <span class="g-pill g-pill-{{ $hs }}">{{ __('messages.health_' . $hs) }}</span>
                    </td>
                    <td>
                        <span style="font-size:20px; font-weight:900; color:#fff;">{{ $customer->deals->count() }}</span>
                    </td>
                    <td class="t-muted">{{ $customer->assignedEmployee->name ?? '—' }}</td>
                    <td>
                        <div class="g-act-row">
                            <a href="{{ route('customers.show', $customer) }}" class="g-btn-icon g-btn-icon-view">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="g-btn-icon g-btn-icon-edit" onclick='editCustomer(@json($customer))'>
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" style="display:inline"
                                onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div style="padding:16px 20px; border-top:1px solid rgba(255,255,255,.06);">
        {{ $customers->links() }}
    </div>
    @endif
    @else
    <div class="g-empty">
        <i class="fas fa-users"></i>
        <h3>{{ __('messages.no_customers_found') }}</h3>
        <p>{{ __('messages.add_first_customer') }}</p>
        <button class="filter-btn filter-btn-primary" onclick="openModal('addModal','add')">
            <i class="fas fa-user-plus"></i> {{ __('messages.add_customer') }}
        </button>
    </div>
    @endif
</div>

{{-- ===== ADD / EDIT MODAL ===== --}}
<div class="gm-overlay" id="addModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title" id="modalTitle">
                <i class="fas fa-user-plus" style="color:var(--brand-cyan)"></i>
                {{ __('messages.add_customer') }}
            </div>
            <button class="gm-close" onclick="closeModal('addModal')">&#215;</button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" id="customerForm">
            @csrf
            <div id="methodField"></div>
            <div class="gm-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.customer_name') }} *</label>
                        <input type="text" name="name" id="cust_name" class="gm-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.industry') }}</label>
                        <input type="text" name="industry" id="cust_industry" class="gm-input">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.email') }}</label>
                        <input type="email" name="email" id="cust_email" class="gm-input">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" id="cust_phone" class="gm-input">
                    </div>
                    <div class="col-md-12">
                        <label class="gm-label">{{ __('messages.address') }}</label>
                        <textarea name="address" id="cust_address" class="gm-input" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.status') }} *</label>
                        <select name="status" id="cust_status" class="gm-input" required>
                            <option value="active">{{ __('messages.status_active') }}</option>
                            <option value="inactive">{{ __('messages.status_inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('addModal')">
                    {{ __('messages.cancel') }}
                </button>
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
function openModal(id, mode) {
    document.getElementById(id).classList.add('show');
    if (mode === 'add') resetForm();
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}
function resetForm() {
    const form = document.getElementById('customerForm');
    form.action = "{{ route('customers.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus" style="color:var(--brand-cyan)"></i> {{ __("messages.add_customer") }}';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> {{ __("messages.save") }}';
    form.reset();
}
function editCustomer(data) {
    const form = document.getElementById('customerForm');
    form.action = `/customers/${data.id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen" style="color:#fbbf24"></i> {{ __("messages.edit_customer") }}';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check-circle"></i> {{ __("messages.update") }}';
    document.getElementById('cust_name').value = data.name;
    document.getElementById('cust_industry').value = data.industry || '';
    document.getElementById('cust_email').value = data.email || '';
    document.getElementById('cust_phone').value = data.phone || '';
    document.getElementById('cust_address').value = data.address || '';
    document.getElementById('cust_status').value = data.status;
    document.getElementById('addModal').classList.add('show');
}
</script>
@endsection
