@php
    $bg = match($announcement->type) {
        'success' => 'rgba(16, 185, 129, 0.2)',
        'warning' => 'rgba(245, 158, 11, 0.2)',
        'danger' => 'rgba(239, 68, 68, 0.2)',
        default => 'rgba(59, 130, 246, 0.2)'
    };
    $border = match($announcement->type) {
        'success' => 'var(--success)',
        'warning' => 'var(--warning)',
        'danger' => 'var(--danger)',
        default => 'var(--primary-light)'
    };
@endphp

<div class="announcement-banner" style="
    background: {{ $bg }};
    backdrop-filter: blur(10px);
    border-bottom: 1px solid {{ $border }};
    color: #fff;
    padding: 10px 32px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    position: relative;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
">
    <i class="fas fa-bullhorn" style="color: {{ $border }}; transform: rotate(-10deg);"></i>
    {{ $announcement->message }}
    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#fff; opacity:0.5; cursor:pointer; margin-left:20px;"><i class="fas fa-times"></i></button>
</div>
