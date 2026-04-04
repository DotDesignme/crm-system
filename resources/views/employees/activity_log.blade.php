@extends('layouts.app')

@section('page-title', __('messages.activity_log'))

@section('content')
<div class="container-fluid py-4">
    <div class="page-header mb-5">
        <div class="d-flex align-items-center gap-4">
            <div class="avatar-lg rounded-4 bg-gradient-brand d-flex align-items-center justify-content-center fw-bold text-white shadow-lg" style="width:70px; height:70px; font-size: 24px;">
                {{ mb_substr($employee->name, 0, 1) }}
            </div>
            <div>
                <h1 class="display-6 fw-bold text-glow mb-1">{{ $employee->name }}</h1>
                <p class="text-secondary fs-5 mb-0">{{ __('messages.activity_history') ?? 'Comprehensive Activity History' }}</p>
            </div>
        </div>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table glass-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.type') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th class="text-end">{{ __('messages.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td class="text-secondary small">
                            <i class="far fa-clock me-2"></i>{{ $activity->created_at->format('M d, Y H:i') }}
                            <div class="mt-1" style="font-size: 10px;">{{ $activity->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-primary-fade text-primary border-0 px-3 py-2" style="font-size: 11px; text-transform: capitalize;">
                                <i class="fas {{ $activity->activity_type == 'login' ? 'fa-sign-in-alt' : 'fa-bolt' }} me-1"></i>
                                {{ str_replace('_', ' ', $activity->activity_type) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-white">{{ $activity->description }}</div>
                        </td>
                        <td class="text-end">
                            @if($activity->metadata)
                            <button class="btn btn-glass-sm" onclick="showMetadata(@json($activity->metadata))">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-history fs-1 mb-3 opacity-25"></i>
                                <h4>{{ __('messages.no_activity') }}</h4>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>

<!-- Metadata Modal -->
<div class="modal fade" id="metadataModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="glass-card modal-content p-0 border-0 shadow-2xl">
            <div class="modal-header border-white-10 p-4">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Activity Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <pre id="metadata-content" class="text-secondary p-3 rounded-4 bg-black-20" style="white-space: pre-wrap; font-family: monospace; font-size: 13px;"></pre>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-black-20 { background: rgba(0,0,0,0.2); }
    .glass-table { border-collapse: separate; border-spacing: 0 8px; width: 100%; }
    .glass-table thead th { border: none; color: var(--text-secondary); font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; padding: 20px; }
    .glass-table tbody td { background: rgba(255,255,255,0.01); border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px 20px; vertical-align: middle; }
    .glass-table tbody td:first-child { border-left: 1px solid rgba(255,255,255,0.05); border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    .glass-table tbody td:last-child { border-right: 1px solid rgba(255,255,255,0.05); border-top-right-radius: 15px; border-bottom-right-radius: 15px; }
    
    .btn-glass-sm {
        width: 38px; height: 38px; border-radius: 12px;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: white; display: inline-flex; align-items: center; justify-content: center;
        transition: 0.3s;
    }
    .btn-glass-sm:hover { background: rgba(255,255,255,0.15); transform: translateY(-2px); }

    .bg-gradient-brand {
        background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
    }
    .text-glow {
        text-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
    }
    .bg-primary-fade { background: rgba(14, 165, 233, 0.1); }
</style>

<script>
    function showMetadata(data) {
        document.getElementById('metadata-content').innerText = JSON.stringify(data, null, 4);
        new bootstrap.Modal(document.getElementById('metadataModal')).show();
    }
</script>
@endsection
