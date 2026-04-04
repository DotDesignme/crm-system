@extends('layouts.app')
@section('page-title', __('messages.tasks'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-blue"><i class="fas fa-tasks"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.tasks') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_tasks_desc') ?? __('messages.manage_tasks') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <button class="filter-btn filter-btn-primary" onclick="document.getElementById('addTaskModal').classList.add('show')">
            <i class="fas fa-plus"></i> {{ __('messages.add_task') }}
        </button>
    </div>
</div>

{{-- STATS ROW --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="g-panel g-panel-p" style="display:flex; align-items:center; gap:20px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-50px; right:-50px; width:150px; height:150px; background:var(--brand-cyan); filter:blur(60px); opacity:0.1; border-radius:50%;"></div>
            <div style="width:64px; height:64px; border-radius:18px; background:rgba(14,165,233,.1); color:var(--brand-cyan); display:flex; align-items:center; justify-content:center; font-size:24px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div style="font-size:32px; font-weight:900; color:#fff; line-height:1;">{{ $pendingCount ?? 0 }}</div>
                <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-top:8px;">{{ __('messages.pending_tasks') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="g-panel g-panel-p" style="display:flex; align-items:center; gap:20px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-50px; right:-50px; width:150px; height:150px; background:#ef4444; filter:blur(60px); opacity:0.1; border-radius:50%;"></div>
            <div style="width:64px; height:64px; border-radius:18px; background:rgba(239,68,68,.1); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:24px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size:32px; font-weight:900; color:#ef4444; line-height:1;">{{ $overdueCount ?? 0 }}</div>
                <div class="t-sub text-uppercase" style="font-weight:800; font-size:11px; margin-top:8px;">{{ __('messages.overdue_tasks') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="g-panel mb-4">
    <form method="GET" class="filter-bar">
        <select name="status" class="filter-select">
            <option value="">{{ __('messages.all_statuses') }}</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.status_pending') }}</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('messages.status_in_progress') }}</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
        </select>
        <select name="priority" class="filter-select">
            <option value="">{{ __('messages.all_priorities') }}</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('messages.priority_low') }}</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('messages.priority_medium') }}</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('messages.priority_high') }}</option>
            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('messages.priority_urgent') }}</option>
        </select>
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-muted); font-size:14px; margin:0 12px; font-weight:600;">
            <input type="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }} onchange="this.form.submit()" style="accent-color:var(--brand-cyan); width:16px; height:16px;">
            {{ __('messages.my_tasks') }}
        </label>
        <button type="submit" class="filter-btn filter-btn-ghost">
            <i class="fas fa-filter"></i> {{ __('messages.filter') }}
        </button>
        @if(request('status') || request('priority') || request('mine'))
        <a href="{{ route('tasks.index') }}" class="filter-btn filter-btn-danger">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </form>
</div>

{{-- DATA TABLE --}}
<div class="g-panel">
    @if($tasks->count())
    <div class="g-table-wrap">
        <table class="g-table">
            <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.related_to') ?? 'Related to' }}</th>
                    <th>{{ __('messages.priority') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.due_at') }}</th>
                    <th style="text-align:right">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                @php
                    $isOverdue = $task->status != 'completed' && $task->due_at && $task->due_at->isPast();
                @endphp
                <tr class="{{ $isOverdue ? 'bg-danger-soft' : '' }}">
                    <td>
                        <div class="t-name">{{ $task->title }}</div>
                        <div class="t-sub" style="margin-top:2px;">
                            <i class="{{ $task->icon }}"></i> {{ $task->type_ar }}
                        </div>
                    </td>
                    <td>
                        @if($task->taskable)
                            <a href="{{ route($task->taskable_type == 'App\Models\Lead' ? 'leads.show' : 'deals.show', $task->taskable_id) }}" style="color:var(--brand-cyan); text-decoration:none; display:flex; align-items:center; gap:6px; font-weight:600; font-size:13px;">
                                <i class="fas {{ $task->taskable_type == 'App\Models\Lead' ? 'fa-user-tag' : 'fa-handshake' }}" style="opacity:0.5;"></i>
                                {{ $task->taskable->name ?? $task->taskable->title ?? '-' }}
                            </a>
                        @else
                            <span class="t-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $pClass = match($task->priority) {
                                'urgent' => 'g-pill-overdue',
                                'high' => 'g-pill-contacted',
                                'medium' => 'g-pill-new',
                                default => ''
                            };
                        @endphp
                        <span class="g-pill {{ $pClass }}" @if(!$pClass) style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);" @endif>
                            {{ $task->priority_ar }}
                        </span>
                    </td>
                    <td>
                        <div style="position:relative; display:inline-block;">
                            @php
                                $sClass = match($task->status) {
                                    'completed' => 'g-pill-converted',
                                    'in_progress' => 'g-pill-contacted',
                                    default => 'g-pill-new'
                                };
                            @endphp
                            <button class="g-pill {{ $sClass }}" style="border:none; cursor:pointer;" onclick="toggleStatusDropdown(event, '{{ $task->id }}')">
                                {{ $task->status_ar }}
                                <i class="fas fa-chevron-down" style="font-size:10px; margin-left:4px; opacity:0.6;"></i>
                            </button>
                            <div id="status-dropdown-{{ $task->id }}" class="status-dropdown-menu" style="display:none; position:absolute; top:100%; left:0; z-index:100; min-width:160px; margin-top:8px; padding:8px; background:var(--bg-secondary); border:1px solid var(--glass-border); border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                                <form method="POST" action="{{ route('tasks.updateStatus', $task) }}">
                                    @csrf @method('PATCH')
                                    <button name="status" value="pending" class="dropdown-item {{ $task->status == 'pending' ? 'active' : '' }}">
                                        <i class="fas fa-clock" style="color:var(--brand-cyan); width:20px;"></i> {{ __('messages.status_pending') }}
                                    </button>
                                    <button name="status" value="in_progress" class="dropdown-item {{ $task->status == 'in_progress' ? 'active' : '' }}">
                                        <i class="fas fa-spinner fa-spin" style="color:#fbbf24; width:20px;"></i> {{ __('messages.status_in_progress') }}
                                    </button>
                                    <button name="status" value="completed" class="dropdown-item {{ $task->status == 'completed' ? 'active' : '' }}">
                                        <i class="fas fa-check-circle" style="color:#34d399; width:20px;"></i> {{ __('messages.status_completed') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($task->due_at)
                            <div style="color:var(--text-secondary); font-size:13px; font-weight:600;">{{ $task->due_at->translatedFormat('d M, Y') }}</div>
                            <div class="t-sub">{{ $task->due_at->translatedFormat('H:i') }}</div>
                            @if($isOverdue)
                                <span class="g-pill g-pill-overdue" style="font-size:9px; padding:2px 6px; margin-top:4px;">{{ __('messages.overdue') }}</span>
                            @endif
                        @else
                            <span class="t-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="g-act-row" style="justify-content:flex-end;">
                            @if($task->status != 'completed')
                            <form method="POST" action="{{ route('tasks.complete', $task) }}" style="margin:0;">
                                @csrf @method('PATCH')
                                <button type="submit" class="g-btn-icon" style="color:var(--success); border-color:var(--success);" title="{{ __('messages.complete') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="g-btn-icon g-btn-icon-delete" title="{{ __('messages.delete') }}">
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
    @if(method_exists($tasks, 'links'))
        <div style="margin-top:24px;">{{ $tasks->links() }}</div>
    @endif
    @else
    <div class="g-empty">
        <i class="fas fa-tasks"></i>
        <h3>{{ __('messages.no_tasks') }}</h3>
        <p>{{ __('messages.no_tasks_desc') ?? 'Start by adding tasks for your team to track work efficiently.' }}</p>
        <button class="filter-btn filter-btn-primary" onclick="document.getElementById('addTaskModal').classList.add('show')">
            <i class="fas fa-plus"></i> {{ __('messages.add_task') }}
        </button>
    </div>
    @endif
</div>

{{-- MODALS --}}
<div class="gm-overlay" id="addTaskModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-plus-circle" style="color:var(--brand-cyan);"></i>
                {{ __('messages.add_task') }}
            </div>
            <button class="gm-close" onclick="document.getElementById('addTaskModal').classList.remove('show')">&#215;</button>
        </div>
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.title') }} *</label>
                    <input type="text" name="title" class="gm-input" required placeholder="e.g. Follow up with quotation">
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.description') }}</label>
                    <textarea name="description" class="gm-input" rows="3"></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.type') }} *</label>
                        <select name="type" class="gm-input" required>
                            <option value="follow_up">{{ __('messages.type_follow_up') }}</option>
                            <option value="call">{{ __('messages.type_call') }}</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="email">{{ __('messages.type_email') }}</option>
                            <option value="meeting">{{ __('messages.type_meeting') }}</option>
                            <option value="other">{{ __('messages.type_other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="gm-label">{{ __('messages.priority') }} *</label>
                        <select name="priority" class="gm-input" required>
                            <option value="low">{{ __('messages.priority_low') }}</option>
                            <option value="medium" selected>{{ __('messages.priority_medium') }}</option>
                            <option value="high">{{ __('messages.priority_high') }}</option>
                            <option value="urgent">{{ __('messages.priority_urgent') }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.assigned_to') }} *</label>
                    <select name="assigned_to" class="gm-input" required>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ auth()->id() == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.due_at') }}</label>
                    <input type="datetime-local" name="due_at" class="gm-input">
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="document.getElementById('addTaskModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-check"></i> {{ __('messages.add') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleStatusDropdown(event, taskId) {
    event.stopPropagation();
    const dropdown = document.getElementById('status-dropdown-' + taskId);
    const isOpen = dropdown.style.display === 'block';
    
    // Close all other dropdowns
    document.querySelectorAll('.status-dropdown-menu').forEach(d => d.style.display = 'none');
    
    if (!isOpen) {
        dropdown.style.display = 'block';
    }
}

// Close dropdowns on click outside
document.addEventListener('click', function() {
    document.querySelectorAll('.status-dropdown-menu').forEach(d => d.style.display = 'none');
});

// Modal close on backdrop click
document.querySelectorAll('.gm-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) o.classList.remove('show'); });
});
</script>
<style>
.dropdown-item {
    display:flex; align-items:center; width:100%; padding:10px 14px; background:transparent; border:none; color:var(--text-secondary); font-size:13px; font-weight:600; cursor:pointer; border-radius:8px; transition:all .2s; text-align:start;
}
.dropdown-item:hover { background:rgba(255,255,255,.05); color:#fff; }
.dropdown-item.active { background:rgba(14,165,233,.1); color:var(--brand-cyan); }
</style>
@endsection
