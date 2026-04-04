@extends('layouts.app')
@section('page-title', __('messages.company_activity_log'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('companies.index') }}" class="btn btn-glass-sm text-white">
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            </a>
            <div>
                <h2 class="text-white mb-0">{{ __('messages.activity_log') }}</h2>
                <p class="text-muted small mb-0">{{ $company->name }}</p>
            </div>
        </div>
    </div>
</div>

<div class="glass-card-static border-0 shadow-lg overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table table-glass mb-0">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 200px;">{{ __('messages.date_time') }}</th>
                    <th style="width: 150px;">{{ __('messages.employee') }}</th>
                    <th style="width: 120px;">{{ __('messages.action') }}</th>
                    <th>{{ __('messages.details') }}</th>
                    <th class="text-end pe-4" style="width: 100px;">{{ __('messages.metadata') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                <tr>
                    <td class="ps-4">
                        <div class="text-white small">{{ $activity->created_at->format('Y-m-d') }}</div>
                        <div class="text-muted" style="font-size: 11px;">{{ $activity->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-xs rounded-circle bg-primary-light d-flex align-items-center justify-content-center text-white fw-bold" style="width:24px; height:24px; font-size: 10px;">
                                {{ mb_substr($activity->employee->name ?? '?', 0, 1) }}
                            </div>
                            <span class="text-white-50 small text-truncate" style="max-width: 100px;">{{ $activity->employee->name ?? '---' }}</span>
                        </div>
                    </td>
                    <td>
                        @php
                            $actionColor = match($activity->action) {
                                'create', 'store' => 'text-success',
                                'update', 'edit' => 'text-warning',
                                'delete', 'destroy' => 'text-danger',
                                'login' => 'text-info',
                                default => 'text-white-50'
                            };
                        @endphp
                        <span class="small fw-bold {{ $actionColor }}">{{ strtoupper($activity->action) }}</span>
                    </td>
                    <td>
                        <div class="text-white-50 small text-truncate" style="max-width: 400px;" title="{{ $activity->description }}">
                            {{ $activity->description }}
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        @if($activity->metadata)
                        <button class="btn btn-glass-sm text-cyan btn-icon-xs" onclick='viewMetadata(@json($activity->metadata))'>
                            <i class="fas fa-eye"></i>
                        </button>
                        @else
                        <span class="text-muted">--</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted opacity-50 mb-3"><i class="fas fa-history fa-3x"></i></div>
                        <p class="text-white-50">{{ __('messages.no_activities_found') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($activities->hasPages())
    <div class="p-3 border-top border-white-05">
        {{ $activities->links() }}
    </div>
    @endif
</div>

<!-- Metadata Modal -->
<div class="modal-overlay" id="metadataModal">
    <div class="modal-glass">
        <div class="modal-header border-0">
            <h3 class="text-white mb-0"><i class="fas fa-database me-2 text-cyan"></i> {{ __('messages.activity_details') }}</h3>
            <button class="btn-close btn-close-white opacity-50" onclick="hideMetadata()"></button>
        </div>
        <div class="modal-body">
            <pre class="bg-black-50 p-3 rounded-4 text-cyan small mb-0" id="metadataContent" style="max-height: 400px; overflow-y: auto;"></pre>
        </div>
        <div class="modal-footer border-0">
            <button class="btn btn-glass-secondary px-4" onclick="hideMetadata()">{{ __('messages.close') }}</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function viewMetadata(data) {
        document.getElementById('metadataContent').textContent = JSON.stringify(data, null, 4);
        document.getElementById('metadataModal').classList.add('show');
    }

    function hideMetadata() {
        document.getElementById('metadataModal').classList.remove('show');
    }
</script>
@endsection
