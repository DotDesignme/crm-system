@extends('layouts.app')
@section('page-title', __('messages.services'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-concierge-bell"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.services') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_services') ?? 'Manage your services and pricing' }}</p>
        </div>
    </div>
    <div class="page-shell-right" style="display:flex; gap:12px; align-items:center;">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px;"></i>
            <input type="text" id="searchInput" placeholder="{{ __('messages.search') }}" class="gm-input" style="padding-left:36px; height:42px; width:220px; border-radius:12px; font-size:13px;" onkeyup="filterServices()">
        </div>
        <button class="filter-btn filter-btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> {{ __('messages.add_service') }}
        </button>
    </div>
</div>

<div class="g-panel">
    @if($services->count())
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.cost_price') }}</th>
                    <th>{{ __('messages.selling_price') }}</th>
                    <th>{{ __('messages.margin') }}</th>
                    <th>{{ __('messages.unit') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        <div class="t-name">{{ $service->name }}</div>
                        @if($service->description)
                            <div class="t-sub" style="font-size:11px; margin-top:2px; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $service->description }}</div>
                        @endif
                    </td>
                    <td><span class="g-pill" style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">{{ $service->category ?? '—' }}</span></td>
                    <td>{{ number_format($service->cost_price, 2) }}</td>
                    <td style="font-weight:700; color:var(--success);">{{ number_format($service->selling_price, 2) }}</td>
                    <td>
                        <span style="color:{{ $service->margin > 20 ? 'var(--success)' : ($service->margin > 0 ? 'var(--brand-cyan)' : 'var(--danger)') }}">{{ $service->margin }}%</span>
                    </td>
                    <td>{{ $service->unit }}</td>
                    <td>
                        @if($service->is_active)
                            <span class="g-pill g-pill-converted"><i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i> {{ __('messages.active') }}</span>
                        @else
                            <span class="g-pill g-pill-overdue"><i class="fas fa-circle" style="font-size:6px; margin-right:4px;"></i> {{ __('messages.inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="g-act-row" style="justify-content:flex-end;">
                            <button class="g-btn-icon g-btn-icon-edit" onclick="openEditModal({{ $service->id }}, '{{ addslashes($service->name) }}', '{{ addslashes($service->description ?? '') }}', '{{ addslashes($service->category ?? '') }}', {{ $service->cost_price }}, {{ $service->selling_price }}, '{{ addslashes($service->unit ?? '') }}', {{ $service->is_active ? 'true' : 'false' }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(method_exists($services, 'links'))
        <div style="margin-top:24px;">{{ $services->links() }}</div>
    @endif
    @else
    <div class="g-empty">
        <i class="fas fa-concierge-bell"></i>
        <h3>{{ __('messages.no_services') }}</h3>
        <p>{{ __('messages.add_service') ?? 'Add services and products to build quotations easily.' }}</p>
        <button class="filter-btn filter-btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> {{ __('messages.add_service') }}
        </button>
    </div>
    @endif
</div>

{{-- Add Service Modal --}}
<div class="gm-overlay" id="addModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-plus-circle" style="color:var(--brand-cyan);"></i>
                {{ __('messages.add_service') }}
            </div>
            <button class="gm-close" onclick="closeModal('addModal')">&#215;</button>
        </div>
        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.name') }} *</label>
                    <input type="text" name="name" class="gm-input" required value="{{ old('name') }}">
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.description') }}</label>
                    <textarea name="description" class="gm-input" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.category') }}</label>
                        <input type="text" name="category" class="gm-input" value="{{ old('category') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.unit') }}</label>
                        <input type="text" name="unit" class="gm-input" placeholder="e.g. Hour, Project" value="{{ old('unit') }}">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.cost_price') }}</label>
                        <input type="number" name="cost_price" class="gm-input" step="0.01" min="0" value="{{ old('cost_price', 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.selling_price') }} *</label>
                        <input type="number" name="selling_price" class="gm-input" step="0.01" min="0" required value="{{ old('selling_price', 0) }}">
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('addModal')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Service Modal --}}
<div class="gm-overlay" id="editModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-pen" style="color:var(--brand-cyan);"></i>
                {{ __('messages.edit_service') }}
            </div>
            <button class="gm-close" onclick="closeModal('editModal')">&#215;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.name') }} *</label>
                    <input type="text" name="name" id="edit_name" class="gm-input" required>
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.description') }}</label>
                    <textarea name="description" id="edit_description" class="gm-input" rows="2"></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.category') }}</label>
                        <input type="text" name="category" id="edit_category" class="gm-input">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.unit') }}</label>
                        <input type="text" name="unit" id="edit_unit" class="gm-input">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.cost_price') }}</label>
                        <input type="number" name="cost_price" id="edit_cost_price" class="gm-input" step="0.01" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.selling_price') }} *</label>
                        <input type="number" name="selling_price" id="edit_selling_price" class="gm-input" step="0.01" min="0" required>
                    </div>
                </div>
                <div>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-primary); font-size:14px; font-weight:600;">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="accent-color:var(--brand-cyan); width:16px; height:16px;">
                        {{ __('messages.active') }}
                    </label>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('editModal')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openAddModal() { document.getElementById('addModal').classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    
    function openEditModal(id, name, description, category, costPrice, sellingPrice, unit, isActive) {
        document.getElementById('editForm').action = '/services/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_category').value = category || '';
        document.getElementById('edit_cost_price').value = costPrice;
        document.getElementById('edit_selling_price').value = sellingPrice;
        document.getElementById('edit_unit').value = unit || '';
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editModal').classList.add('show');
    }

    function filterServices() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    document.querySelectorAll('.gm-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });
</script>
@endsection
