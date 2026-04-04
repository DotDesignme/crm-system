@extends('layouts.app')
@section('page-title', $customer->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('customers.index') }}" class="btn btn-glass-sm text-white">
            <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div>
            <h2 class="text-white mb-0">{{ $customer->name }}</h2>
            <p class="text-muted small mb-0">{{ __('messages.customer_details') }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-glass-warning" onclick="document.getElementById('editCustomerModal').classList.add('show')">
            <i class="fas fa-edit me-2"></i> {{ __('messages.edit') }}
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Info & Contacts -->
    <div class="col-lg-8">
        <!-- Main Info Card -->
        <div class="glass-card-static border-0 shadow-lg p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h3 class="text-white fs-5 fw-bold mb-0">
                    <i class="fas fa-id-card me-2 text-primary"></i> {{ __('messages.customer_info') }}
                </h3>
                @php
                    $healthClass = match($customer->health_score) {
                        'hot' => 'badge-glass-success',
                        'warm' => 'badge-glass-info',
                        'cold' => 'badge-glass-warning',
                        'churning' => 'badge-glass-danger',
                        default => 'badge-glass-secondary'
                    };
                @endphp
                <span class="badge {{ $healthClass }} px-3 py-2">
                    {{ __('messages.health_' . ($customer->health_score ?? 'unknown')) }}
                </span>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted small d-block mb-1">{{ __('messages.industry') }}</label>
                        <div class="text-white">{{ $customer->industry ?? '---' }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="text-muted small d-block mb-1">{{ __('messages.email') }}</label>
                        <div class="text-white">{{ $customer->email ?? '---' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted small d-block mb-1">{{ __('messages.phone') }}</label>
                        <div class="text-white">{{ $customer->phone ?? '---' }}</div>
                    </div>
                    <div class="info-item mb-3">
                        <label class="text-muted small d-block mb-1">{{ __('messages.assigned_to') }}</label>
                        <div class="text-white">{{ $customer->assignedEmployee->name ?? '---' }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="info-item">
                        <label class="text-muted small d-block mb-1">{{ __('messages.address') }}</label>
                        <div class="text-white opacity-75">{{ $customer->address ?? '---' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts Table -->
        <div class="glass-card-static border-0 shadow-lg p-0 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom border-white-05">
                <h3 class="text-white fs-5 fw-bold mb-0">
                    <i class="fas fa-users me-2 text-info"></i> {{ __('messages.contacts') }}
                </h3>
                <button class="btn btn-glass-sm text-info" onclick="document.getElementById('addContactModal').classList.add('show')">
                    <i class="fas fa-plus me-1"></i> {{ __('messages.add_contact') }}
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ __('messages.name') }}</th>
                            <th>{{ __('messages.position') }}</th>
                            <th>{{ __('messages.contact') }}</th>
                             <th class="text-center">{{ __('messages.decision_maker') }}</th>
                            <th class="text-end pe-4">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->contacts as $contact)
                        <tr>
                            <td class="ps-4 d-flex align-items-center gap-2">
                                <div class="avatar-xs rounded-circle bg-white-05 d-flex align-items-center justify-content-center text-white small" style="width:28px; height:28px;">
                                    {{ mb_substr($contact->name, 0, 1) }}
                                </div>
                                <span class="fw-bold text-white">{{ $contact->name }}</span>
                            </td>
                            <td class="text-white-50 small">{{ $contact->position ?? '---' }}</td>
                            <td>
                                <div class="text-white-50 small">{{ $contact->email }}</div>
                                <div class="text-muted small" style="font-size: 11px;">{{ $contact->phone }}</div>
                            </td>
                            <td class="text-center">
                                @if($contact->is_decision_maker)
                                <i class="fas fa-check-circle text-success"></i>
                                @else
                                <i class="fas fa-minus text-muted opacity-25"></i>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-glass-sm text-danger btn-icon-xs">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted small">
                                {{ __('messages.no_contacts_found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Activity -->
    <div class="col-lg-4">
        <!-- Stats Widgets -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="glass-card-static border-0 shadow-lg p-3 text-center">
                    <div class="text-primary fs-3 fw-bold mb-1">{{ $customer->deals->count() }}</div>
                    <div class="text-muted small text-uppercase" style="font-size: 10px; letter-spacing: 1px;">{{ __('messages.deals') }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="glass-card-static border-0 shadow-lg p-3 text-center">
                    <div class="text-success fs-3 fw-bold mb-1">{{ $customer->leads->count() }}</div>
                    <div class="text-muted small text-uppercase" style="font-size: 10px; letter-spacing: 1px;">{{ __('messages.leads') }}</div>
                </div>
            </div>
        </div>

        <!-- Deals Mini List -->
        <div class="glass-card-static border-0 shadow-lg p-4 mb-4">
            <h3 class="text-white fs-6 fw-bold mb-3">
                <i class="fas fa-handshake me-2 text-warning"></i> {{ __('messages.recent_deals') }}
            </h3>
            @forelse($customer->deals->take(5) as $deal)
            <div class="deal-item-glass p-2 rounded-3 border border-white-05 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-white small fw-bold text-truncate" style="max-width: 150px;">{{ $deal->title }}</span>
                    <span class="text-cyan small fw-bold">{{ number_format($deal->value) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="text-muted" style="font-size: 10px;">{{ $deal->stage->name ?? '---' }}</span>
                    <a href="{{ route('deals.show', $deal) }}" class="text-white-50" style="font-size: 10px;"><i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>
            @empty
            <div class="text-center text-muted small py-3">{{ __('messages.no_deals_found') }}</div>
            @endforelse
        </div>

        <!-- Activity Timeline -->
        <div class="glass-card-static border-0 shadow-lg p-4">
            <h3 class="text-white fs-6 fw-bold mb-4">
                <i class="fas fa-history me-2 text-info"></i> {{ __('messages.activity_timeline') }}
            </h3>
            <div class="activity-timeline-glass ps-3 border-start border-white-10">
                @forelse($activities->take(10) as $activity)
                <div class="activity-item-glass position-relative pb-4 ms-2">
                    <div class="activity-dot"></div>
                    <div class="text-white small fw-bold mb-1">{{ $activity->subject ?? $activity->description }}</div>
                    <div class="text-muted" style="font-size: 11px;">
                        {{ $activity->created_at->diffForHumans() }} &middot; {{ $activity->user->name ?? 'System' }}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted small py-3">{{ __('messages.no_activity_found') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal-overlay" id="editCustomerModal">
    <div class="modal-glass modal-lg">
        <div class="modal-header border-0">
            <h3 class="text-white mb-0"><i class="fas fa-edit me-2 text-warning"></i> {{ __('messages.edit_customer') }}</h3>
            <button class="btn-close btn-close-white opacity-50" onclick="document.getElementById('editCustomerModal').classList.remove('show')"></button>
        </div>
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            <div class="modal-body py-4">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label text-white-50 small">{{ __('messages.customer_name') }}</label><input type="text" name="name" class="form-control glass-input" value="{{ $customer->name }}" required></div>
                    <div class="col-md-6"><label class="form-label text-white-50 small">{{ __('messages.industry') }}</label><input type="text" name="industry" class="form-control glass-input" value="{{ $customer->industry }}"></div>
                    <div class="col-md-6"><label class="form-label text-white-50 small">{{ __('messages.email') }}</label><input type="email" name="email" class="form-control glass-input" value="{{ $customer->email }}"></div>
                    <div class="col-md-6"><label class="form-label text-white-50 small">{{ __('messages.phone') }}</label><input type="text" name="phone" class="form-control glass-input" value="{{ $customer->phone }}"></div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.health_score') }}</label>
                        <select name="health_score" class="form-select glass-select">
                            <option value="hot" {{ $customer->health_score == 'hot' ? 'selected' : '' }}>{{ __('messages.health_hot') }}</option>
                            <option value="warm" {{ $customer->health_score == 'warm' ? 'selected' : '' }}>{{ __('messages.health_warm') }}</option>
                            <option value="cold" {{ $customer->health_score == 'cold' ? 'selected' : '' }}>{{ __('messages.health_cold') }}</option>
                            <option value="churning" {{ $customer->health_score == 'churning' ? 'selected' : '' }}>{{ __('messages.health_churning') }}</option>
                        </select>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label text-white-50 small">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select glass-select">
                            <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>{{ __('messages.status_active') }}</option>
                            <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>{{ __('messages.status_inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-glass-secondary" onclick="document.getElementById('editCustomerModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-glass-primary px-4">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Contact Modal -->
<div class="modal-overlay" id="addContactModal">
    <div class="modal-glass">
        <div class="modal-header border-0">
            <h3 class="text-white mb-0"><i class="fas fa-plus-circle me-2 text-info"></i> {{ __('messages.add_contact') }}</h3>
            <button class="btn-close btn-close-white opacity-50" onclick="document.getElementById('addContactModal').classList.remove('show')"></button>
        </div>
        <form method="POST" action="{{ route('customers.contacts.store', $customer) }}">
            @csrf
            <div class="modal-body py-4">
                <div class="form-group mb-3"><label class="form-label text-white-50 small">{{ __('messages.name') }}</label><input type="text" name="name" class="form-control glass-input" required></div>
                <div class="form-group mb-3"><label class="form-label text-white-50 small">{{ __('messages.position') }}</label><input type="text" name="position" class="form-control glass-input"></div>
                <div class="form-group mb-3"><label class="form-label text-white-50 small">{{ __('messages.email') }}</label><input type="email" name="email" class="form-control glass-input"></div>
                <div class="form-group mb-3"><label class="form-label text-white-50 small">{{ __('messages.phone') }}</label><input type="text" name="phone" class="form-control glass-input"></div>
                <div class="form-check mb-0 mt-3">
                    <input class="form-check-input" type="checkbox" name="is_decision_maker" id="dmCheck" value="1">
                    <label class="form-check-label text-white-50 small" for="dmCheck">{{ __('messages.decision_maker') }}</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-glass-secondary" onclick="document.getElementById('addContactModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-glass-primary px-4">{{ __('messages.add') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
