@extends('layouts.app')
@section('page-title', __('messages.manage_roles'))

@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-shield-halved"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.roles_permissions') }}</h1>
            <p class="page-shell-sub">{{ __('messages.roles_subtitle') ?? 'Manage access control and team roles' }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        <a href="{{ route('roles.create') }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-plus"></i> {{ __('messages.create_new_role') }}
        </a>
    </div>
</div>

<div class="row g-4">
    @foreach($roles as $role)
    <div class="col-xl-4 col-lg-6">
        <div class="g-panel g-panel-p" style="height:100%; display:flex; flex-direction:column; justify-content:space-between; transition:.3s; cursor:pointer;" onmouseover="this.style.borderColor='var(--brand-cyan)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor=''; this.style.transform='translateY(0)'">
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <div style="width:48px; height:48px; border-radius:14px; background:rgba(14,165,233,.1); color:var(--brand-cyan); display:flex; align-items:center; justify-content:center; font-size:20px;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span class="g-pill g-pill-{{ $role->slug == 'admin' ? 'lost' : 'new' }}">
                        {{ count($role->permissions) }} {{ __('messages.permissions') }}
                    </span>
                </div>
                <h3 class="t-name" style="font-size:18px; margin-bottom:8px;">{{ $role->name }}</h3>
                <p class="t-sub" style="font-size:13px; line-height:1.5; min-height:40px;">
                    {{ $role->description ?? __('messages.no_description') }}
                </p>
            </div>
            
            <div style="margin-top:24px; padding-top:20px; border-top:1px solid rgba(255,255,255,.05); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; gap:6px; flex-wrap:wrap; flex:1;">
                    @foreach($role->permissions->take(3) as $perm)
                    <span style="font-size:10px; padding:4px 8px; border-radius:6px; background:rgba(255,255,255,.05); color:var(--text-muted); white-space:nowrap; border:1px solid rgba(255,255,255,.05);">
                        {{ $perm->slug }}
                    </span>
                    @endforeach
                    @if($role->permissions->count() > 3)
                    <span style="font-size:11px; font-weight:800; color:var(--text-muted); display:flex; align-items:center;">+{{ $role->permissions->count() - 3 }}</span>
                    @endif
                </div>
                <div class="g-act-row" style="margin-left:16px;">
                    <a href="{{ route('roles.edit', $role) }}" class="g-btn-icon g-btn-icon-edit"><i class="fas fa-pen"></i></a>
                    @if($role->slug != 'admin')
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete') ?? '') }}')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="g-btn-icon g-btn-icon-delete"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
