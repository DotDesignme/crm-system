@extends('layouts.app')
@section('page-title', __('messages.settings'))

@section('content')
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-cyan"><i class="fas fa-user-edit"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.settings') }}</h1>
            <p class="page-shell-sub">{{ __('messages.manage_profile') ?? 'Update your profile identity' }}</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="g-panel g-panel-p mb-4" style="border-color:rgba(34,197,94,.3); background:rgba(34,197,94,.08);">
    <div style="display:flex; align-items:center; gap:12px; color:#34d399;">
        <i class="fas fa-check-circle" style="font-size:18px;"></i>
        <span style="font-weight:600;">{{ session('success') }}</span>
    </div>
</div>
@endif

<div class="grid-2">
    {{-- Personal Info --}}
    <div class="g-panel g-panel-p">
        <form action="{{ route('settings.profile') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:28px;">
                <div class="page-icon page-icon-blue"><i class="fas fa-id-card"></i></div>
                <div>
                    <h3 style="color:#fff; font-size:16px; font-weight:800; margin:0;">{{ __('messages.personal_info') ?? 'Profile details' }}</h3>
                    <div class="t-sub">{{ __('Identity and basic info') }}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="gm-label">{{ __('messages.name') }}</label>
                <div class="filter-search" style="width:100%; max-width:100%; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02);">
                    <i class="fas fa-signature"></i>
                    <input type="text" name="name" class="" style="background:transparent; border:none; color:#fff; width:100%; outline:none;" value="{{ old('name', $user->name) }}" required placeholder="e.g. John Doe">
                </div>
                @error('name')<span style="color:#f87171; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="gm-label">{{ __('messages.username') }}</label>
                <div class="filter-search" style="width:100%; max-width:100%; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02);">
                    <i class="fas fa-at"></i>
                    <input type="text" name="username" class="" style="background:transparent; border:none; color:var(--brand-cyan); width:100%; outline:none; font-family:'JetBrains Mono', monospace;" value="{{ old('username', $user->username) }}" required placeholder="username">
                </div>
                @error('username')<span style="color:#f87171; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="filter-btn filter-btn-primary" style="width:100%; justify-content:center; padding:14px; font-size:14px;">
                <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
            </button>
        </form>
    </div>

    {{-- Security --}}
    <div class="g-panel g-panel-p">
        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:28px;">
                <div class="page-icon page-icon-rose"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <h3 style="color:#fff; font-size:16px; font-weight:800; margin:0;">{{ __('messages.security') ?? 'Security' }}</h3>
                    <div class="t-sub">{{ __('Access control') }}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="gm-label">{{ __('messages.current_password') }}</label>
                <div class="filter-search" style="width:100%; max-width:100%; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02);">
                    <i class="fas fa-key"></i>
                    <input type="password" name="current_password" style="background:transparent; border:none; color:#fff; width:100%; outline:none;" required placeholder="••••••••">
                </div>
                @error('current_password')<span style="color:#f87171; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="mb-3">
                <label class="gm-label">{{ __('messages.new_password') }}</label>
                <div class="filter-search" style="width:100%; max-width:100%; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02);">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" style="background:transparent; border:none; color:#fff; width:100%; outline:none;" required placeholder="Min 8 characters">
                </div>
                @error('password')<span style="color:#f87171; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $message }}</span>@enderror
            </div>

            <div class="mb-4">
                <label class="gm-label">{{ __('messages.confirm_password') }}</label>
                <div class="filter-search" style="width:100%; max-width:100%; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02);">
                    <i class="fas fa-lock-open"></i>
                    <input type="password" name="password_confirmation" style="background:transparent; border:none; color:#fff; width:100%; outline:none;" required placeholder="Repeat new password">
                </div>
            </div>

            <button type="submit" class="filter-btn filter-btn-primary" style="width:100%; justify-content:center; padding:14px; font-size:14px; background:rgba(225,29,72,.15); color:#f43f5e;">
                <i class="fas fa-user-shield"></i> {{ __('messages.update_password') }}
            </button>
        </form>
    </div>
</div>
@endsection
