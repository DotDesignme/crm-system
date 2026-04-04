@php
    $typeColors = [
        'deal_created' => 'var(--brand-blue)',
        'created' => 'var(--brand-blue)',
        'deal_moved' => 'var(--accent)',
        'status_changed' => 'var(--accent)',
        'note_added' => 'var(--warning)',
        'call' => 'var(--brand-cyan)',
        'whatsapp' => '#25D366',
        'email' => '#10b881',
        'meeting' => '#f97316',
        'invoice_generated' => '#3b82f6',
        'quotation_accepted' => 'var(--success)',
        'quotation_rejected' => 'var(--danger)',
        'template_applied' => 'var(--brand-blue)',
    ];
@endphp

<div class="timeline-container">
    @forelse($activities as $activity)
    <div class="timeline-item fade-in">
        <div class="timeline-marker" style="--marker-color: {{ $typeColors[$activity->type] ?? 'rgba(255,255,255,0.2)' }};">
            <i class="{{ $activity->icon }}"></i>
        </div>
        <div class="timeline-content glass-card">
            <div class="timeline-header">
                <h4 class="m-0">{{ $activity->subject }}</h4>
                <span class="text-muted">{{ $activity->created_at->diffForHumans() }}</span>
            </div>
            @if($activity->description)
            <p class="activity-desc">{{ $activity->description }}</p>
            @endif
            <div class="timeline-footer">
                <div class="user-pill">
                    <div class="user-avatar">{{ substr($activity->employee->name, 0, 1) }}</div>
                    <span>{{ $activity->employee->name }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-timeline">
        <div class="empty-icon"><i class="fas fa-history"></i></div>
        <p>{{ __('messages.no_activity') }}</p>
    </div>
    @endforelse
</div>

<style>
.timeline-container {
    position: relative;
    padding: 10px 0;
}
.timeline-container::before {
    content: '';
    position: absolute;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: linear-gradient(to bottom, 
        rgba(255,255,255,0.08) 0%, 
        rgba(255,255,255,0.02) 50%,
        rgba(255,255,255,0.08) 100%
    );
     {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;
}

.timeline-item {
    position: relative;
    margin-bottom: 28px;
    padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 45px;
}

.timeline-marker {
    position: absolute;
    top: 6px;
    {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: var(--marker-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 13px;
    z-index: 1;
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 0 15px var(--marker-color), inset 0 0 5px rgba(255,255,255,0.3);
}

.timeline-content {
    padding: 16px 20px !important;
    border: 1px solid var(--glass-border) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-item:hover .timeline-content {
    border-color: rgba(255,255,255,0.15) !important;
    background: rgba(255,255,255,0.05);
    transform: translateX({{ app()->getLocale() == 'ar' ? '-6px' : '6px' }});
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.timeline-header h4 {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
}

.timeline-header span {
    font-size: 11px;
    opacity: 0.6;
}

.activity-desc {
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

.timeline-footer {
    margin-top: 14px;
    display: flex;
}

.user-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.04);
    padding: 4px 10px 4px 4px;
    border-radius: 8px;
    font-size: 11px;
    color: var(--text-muted);
}

.user-avatar {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
}

.empty-timeline {
    text-align: center;
    padding: 60px 20px;
    background: rgba(255,255,255,0.01);
    border-radius: 20px;
    border: 1px dashed var(--glass-border);
}

.empty-timeline .empty-icon {
    font-size: 40px;
    opacity: 0.1;
    margin-bottom: 16px;
}
</style>
