@extends('layouts.app')

@section('title', __('messages.pipeline'))

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>{{ __('messages.pipeline') }}</h2>
        <p>{{ __('messages.manage_leads_pipeline') }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('leads.index') }}" class="btn btn-ghost">
            <i class="fas fa-list"></i> {{ __('messages.list_view') }}
        </a>
    </div>
</div>

<div class="kanban-container">
    <div class="kanban-board">
        @foreach($statuses as $status)
        <div class="kanban-column" data-status="{{ $status }}">
            <div class="kanban-column-header">
                <div class="header-main">
                    <span class="status-indicator status-{{ $status }}"></span>
                    <h3>{{ __('messages.status_' . $status) }}</h3>
                </div>
                <span class="badg-count">{{ $leads->get($status)?->count() ?? 0 }}</span>
            </div>
            
            <div class="kanban-cards sortable-list" id="list-{{ $status }}" data-status="{{ $status }}">
                @forelse($leads->get($status) ?? [] as $lead)
                <div class="kanban-card" data-id="{{ $lead->id }}">
                    <div class="card-priority priority-{{ $lead->priority ?? 'medium' }}"></div>
                    <div class="card-body">
                        <div class="card-title">
                            <a href="{{ route('leads.show', $lead) }}">{{ $lead->name }}</a>
                        </div>
                        @if($lead->phone)
                        <div class="card-info">
                            <i class="fas fa-phone-alt"></i> <span>{{ $lead->phone }}</span>
                        </div>
                        @endif
                        <div class="card-meta">
                            <div class="meta-item">
                                <i class="fas fa-globe"></i> <span>{{ $lead->company->name ?? '-' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="far fa-clock"></i> <span>{{ $lead->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @if($lead->employee)
                        <div class="card-assignee">
                            <div class="avatar-sm">{{ substr($lead->employee->name, 0, 1) }}</div>
                            <span>{{ $lead->employee->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-column-placeholder">
                    <i class="fas fa-plus"></i>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.kanban-container {
    padding: 10px 0;
    height: calc(100vh - 180px);
    overflow-x: auto;
    overflow-y: hidden;
    position: relative;
}

.kanban-board {
    display: flex;
    gap: 20px;
    height: 100%;
    min-width: max-content;
    padding-bottom: 20px;
}

.kanban-column {
    width: 320px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius);
    display: flex;
    flex-direction: column;
    max-height: 100%;
    backdrop-filter: blur(10px);
}

.kanban-column-header {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--glass-border);
}

.header-main {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.status-new { background-color: var(--info); box-shadow: 0 0 10px var(--info); }
.status-contacted { background-color: var(--warning); box-shadow: 0 0 10px var(--warning); }
.status-interested { background-color: var(--success); box-shadow: 0 0 10px var(--success); }
.status-not_interested { background-color: var(--danger); box-shadow: 0 0 10px var(--danger); }
.status-converted { background-color: var(--primary); box-shadow: 0 0 10px var(--primary); }

.kanban-column-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    text-transform: capitalize;
}

.badg-count {
    background: rgba(255, 255, 255, 0.1);
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 600;
}

.kanban-cards {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 150px;
}

/* Custom Scrollbar for columns */
.kanban-cards::-webkit-scrollbar { width: 5px; }
.kanban-cards::-webkit-scrollbar-track { background: transparent; }
.kanban-cards::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }

.kanban-card {
    background: var(--bg-tertiary);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-sm);
    padding: 15px;
    cursor: grab;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.kanban-card:hover {
    transform: translateY(-4px);
    border-color: var(--primary);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.kanban-card:active {
    cursor: grabbing;
}

.card-priority {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
}

.priority-low { background-color: var(--info); }
.priority-medium { background-color: var(--warning); }
.priority-high { background-color: var(--danger); }
.priority-urgent { background-color: #8b5cf6; }

.card-title {
    margin-bottom: 10px;
}

.card-title a {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: block;
    line-height: 1.4;
}

.card-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary);
    font-size: 12px;
    margin-bottom: 10px;
}

.card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    padding-top: 10px;
    margin-bottom: 12px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--text-muted);
}

.card-assignee {
    display: flex;
    align-items: center;
    gap: 8px;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.card-assignee span {
    font-size: 11px;
    color: var(--text-secondary);
    font-weight: 500;
}

.empty-column-placeholder {
    border: 2px dashed var(--glass-border);
    border-radius: var(--radius-sm);
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    transition: all 0.3s;
}

.sortable-ghost {
    opacity: 0.4;
    background: var(--primary) !important;
}

.sortable-chosen {
    border-color: var(--primary);
}

.sortable-drag {
    cursor: grabbing;
    transform: rotate(2deg);
}

/* RTL Adjustments */
[dir="rtl"] .card-priority {
    left: auto;
    right: 0;
}

[dir="rtl"] .avatar-sm {
    margin-left: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lists = document.querySelectorAll('.sortable-list');
    
    lists.forEach(list => {
        new Sortable(list, {
            group: 'kanban',
            animation: 250,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            draggable: '.kanban-card',
            
            onEnd: function(evt) {
                const leadId = evt.item.getAttribute('data-id');
                const newStatus = evt.to.getAttribute('data-status');
                
                if (evt.from !== evt.to) {
                    updateLeadStatus(leadId, newStatus);
                }
            }
        });
    });

    async function updateLeadStatus(leadId, status) {
        try {
            const response = await fetch('{{ route("leads.updateStatus") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    lead_id: leadId,
                    status: status
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Optional: Update counts or show a small toast
                console.log('Status updated successfully');
                location.reload(); // Refresh to update counts and order, or we could do it with JS
            } else {
                alert('Error updating status');
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update status');
            location.reload();
        }
    }
});
</script>
@endsection
