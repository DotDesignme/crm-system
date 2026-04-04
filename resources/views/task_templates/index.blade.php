@extends('layouts.app')
@section('page-title', __('messages.task_templates'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-magic"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.task_templates') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_task_templates_desc') ?? 'Create and manage automated task sequences' }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('task-templates.create') }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.create_new_sequence') }}
        </a>
    </div>
</div>

<div class="row g-4">
    @forelse($templates as $template)
    <div class="col-xl-4 col-lg-6">
        <div class="g-panel g-panel-p" style="height:100%; display:flex; flex-direction:column; padding:0; overflow:hidden; transition:.3s; cursor:pointer;" onmouseover="this.style.borderColor='var(--brand-cyan)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor=''; this.style.transform='translateY(0)'">
            <div style="padding:24px; flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                    <div>
                        <h3 class="t-name" style="font-size:18px; margin-bottom:6px;">{{ $template->name }}</h3>
                        <p class="t-sub" style="font-size:13px; line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:40px;">
                            {{ $template->description ?: __('messages.no_description') }}
                        </p>
                    </div>
                    <div style="position:relative;">
                        <button class="g-btn-icon" style="color:var(--text-muted);" onclick="toggleDropdown(event, 'drop-{{$template->id}}')"><i class="fas fa-ellipsis-v"></i></button>
                        <div id="drop-{{$template->id}}" class="action-dropdown" style="display:none; position:absolute; right:0; top:100%; background:var(--bg-secondary); border:1px solid var(--glass-border); padding:8px; border-radius:12px; z-index:10; min-width:140px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                            <a href="{{ route('task-templates.edit', $template) }}" style="display:flex; align-items:center; gap:8px; padding:10px; color:var(--text-secondary); text-decoration:none; font-size:13px; font-weight:600; border-radius:8px; transition:.2s;" onmouseover="this.style.background='rgba(255,255,255,.05)'; this.style.color='#fff'" onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <form action="{{ route('task-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" style="width:100%; display:flex; align-items:center; gap:8px; padding:10px; color:var(--danger); background:transparent; border:none; text-decoration:none; font-size:13px; font-weight:600; border-radius:8px; transition:.2s;" onmouseover="this.style.background='rgba(239,68,68,.1)'" onmouseout="this.style.background=''">
                                    <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Visualization --}}
                <div style="display:flex; align-items:center; gap:8px; margin-top:24px; overflow-x:auto; padding-bottom:8px; scrollbar-width:none;">
                    @foreach($template->items->sortBy('order') as $index => $task)
                        <div style="display:flex; align-items:center;">
                            <div style="width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; transition:.3s; {{ $index == 0 ? 'background:var(--brand-cyan); color:#fff; border:none;' : 'background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:var(--text-muted);' }}" title="{{ $task->title }}">
                                {{ $index + 1 }}
                            </div>
                            @if(!$loop->last)
                                <div style="width:16px; height:2px; background:rgba(255,255,255,.1); margin:0 4px; border-radius:2px;"></div>
                            @endif
                        </div>
                    @endforeach
                    @if($template->items->isEmpty())
                        <span class="t-sub" style="font-size:12px; font-style:italic;">{{ __('messages.no_steps_defined') }}</span>
                    @endif
                </div>
            </div>
            
            <div style="padding:16px 24px; background:rgba(0,0,0,.2); display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,.05);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="font-size:12px; color:var(--text-muted); font-weight:600;">
                        <i class="fas fa-list-check" style="color:var(--brand-cyan); margin-right:6px;"></i>
                        <span style="color:#fff;">{{ $template->items->count() }}</span> {{ __('messages.steps') }}
                    </div>
                    <div style="width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,.2);"></div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600;">
                        {{ $template->updated_at->diffForHumans() }}
                    </div>
                </div>
                <a href="{{ route('task-templates.edit', $template) }}" style="font-size:12px; color:var(--brand-cyan); text-decoration:none; font-weight:800; display:flex; align-items:center; gap:6px;">
                    {{ __('messages.open_builder') }} <i class="fas fa-arrow-right" style="font-size:10px;"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="g-empty">
            <i class="fas fa-magic"></i>
            <h3>{{ __('messages.no_templates_yet') }}</h3>
            <p>{{ __('messages.no_templates_desc') ?? 'Create automated task sequences for standard processes.' }}</p>
            <a href="{{ route('task-templates.create') }}" class="filter-btn filter-btn-primary" style="text-decoration:none; display:inline-flex; align-items:center;">
                <i class="fas fa-plus"></i> {{ __('messages.create_first_template') }}
            </a>
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<script>
function toggleDropdown(e, id) {
    e.stopPropagation();
    const drop = document.getElementById(id);
    const isVisible = drop.style.display === 'block';
    document.querySelectorAll('.action-dropdown').forEach(d => d.style.display = 'none');
    if(!isVisible) drop.style.display = 'block';
}
document.addEventListener('click', () => {
    document.querySelectorAll('.action-dropdown').forEach(d => d.style.display = 'none');
});
</script>
@endsection
