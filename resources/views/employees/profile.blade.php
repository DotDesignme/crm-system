@extends('layouts.app')
@section('page-title', __('messages.profile'))

@section('content')

{{-- PAGE HEADER / HERO --}}
<div class="page-shell" style="margin-bottom: 40px;">
    <div class="page-shell-left">
        <div class="position-relative" style="margin-right:25px;">
            @if($employee->avatar)
                <img src="{{ asset('storage/'.$employee->avatar) }}" style="width:100px; height:100px; border-radius:30px; object-fit:cover; border:2px solid var(--glass-border); box-shadow:0 15px 35px rgba(0,0,0,.4);">
            @else
                <div style="width:100px; height:100px; border-radius:30px; background:linear-gradient(135deg, var(--brand-blue), var(--brand-cyan)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:40px; font-weight:900; border:2px solid var(--glass-border); box-shadow:0 15px 35px rgba(0,0,0,0.4);">
                    {{ mb_substr($employee->name, 0, 1) }}
                </div>
            @endif
            <label for="avatarInput" style="position:absolute; bottom:-2px; right:-2px; width:34px; height:34px; background:var(--brand-cyan); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; border:4px solid var(--bg-primary); z-index:2; transition:.3s; box-shadow: 0 4px 12px rgba(14,165,233,0.4);" onmouseover="this.style.transform='scale(1.15) rotate(15deg)'" onmouseout="this.style.transform='scale(1) rotate(0deg)'">
                <i class="fas fa-camera"></i>
            </label>
            <form action="{{ route('employees.profile.update') }}" method="POST" id="avatarForm" enctype="multipart/form-data" class="d-none">
                @csrf @method('PUT')
                <input type="file" id="avatarInput" name="avatar" onchange="this.form.submit()" accept="image/*">
                <input type="hidden" name="name" value="{{ $employee->name }}">
            </form>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 800; color: var(--brand-cyan); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">{{ __('messages.command_center') ?? 'Command Center' }}</div>
            <h1 class="page-shell-title" style="margin:0; font-size:32px; letter-spacing: -1px;">{{ __('messages.welcome_back') ?? 'Welcome back' }}, {{ explode(' ', $employee->name)[0] }}!</h1>
            <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
                <span style="color:var(--text-muted); font-size:14px; font-weight:600; display:flex; align-items:center; gap:6px;"><i class="fas fa-id-badge" style="color:var(--brand-blue);"></i> {{ $employee->job_title ?? __('messages.employee') }}</span>
                <div style="width:5px; height:5px; border-radius:50%; background:rgba(255,255,255,.15);"></div>
                <span style="color:var(--text-muted); font-size:14px; font-weight:600; display:flex; align-items:center; gap:6px;"><i class="fas fa-shield-halved" style="color:var(--brand-cyan);"></i> {{ $employee->roles->first()->name ?? 'Standard Access' }}</span>
            </div>
        </div>
    </div>
    <div class="page-shell-right">
        <div style="background:rgba(255,255,255,.03); padding:8px; border-radius:18px; display:flex; gap:8px; border:1px solid rgba(255,255,255,.06); backdrop-filter: blur(10px);">
            @foreach(['available', 'site_visit', 'meeting', 'on_leave'] as $status)
                @php 
                    $btnColor = match($status) {
                        'available' => '#22c55e',
                        'site_visit' => '#f59e0b',
                        'meeting' => '#a855f7',
                        'on_leave' => '#ef4444',
                        default => '#0ea5e9'
                    };
                    $isActive = $employee->status === $status;
                @endphp
                <form action="{{ route('employees.profile.status') }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" style="border:none; padding:10px 20px; border-radius:12px; font-size:12px; font-weight:800; transition:.3s; 
                        {{ $isActive ? "background:$btnColor; color:#fff; box-shadow: 0 8px 20px {$btnColor}4d;" : 'background:transparent; color:var(--text-muted);' }}"
                        onmouseover="if(this.style.background==='transparent') { this.style.color='#fff'; this.style.background='rgba(255,255,255,0.05)'; }"
                        onmouseout="if(this.style.background!=='{{ $btnColor }}') { this.style.color='var(--text-muted)'; this.style.background='transparent'; }">
                        {{ __('messages.status_'.$status) }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>

@if(session('success'))
<div class="g-panel g-panel-p mb-4" style="background:rgba(34,197,94,.08); border-color:rgba(34,197,94,.3);">
    <div style="display:flex; align-items:center; gap:12px; color:#34d399;">
        <i class="fas fa-check-circle"></i>
        <span style="font-weight:700;">{{ session('success') }}</span>
    </div>
</div>
@endif

{{-- PERFORMANCE ROW --}}
<div class="row g-5 mb-5">
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="padding: 28px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: var(--brand-blue); border-radius: 50%; filter: blur(40px); opacity: 0.15;"></div>
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:12px; color: var(--brand-blue); letter-spacing: 1px;">{{ __('messages.monthly_goal') }}</div>
            <div style="font-size:32px; font-weight:900; color:#fff;">{{ number_format($target->target_amount ?? 0) }} <span style="font-size:14px; opacity:.4; font-weight: 500;">{{ $system_branding['system_currency_symbol'] ?? 'EGP' }}</span></div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;"><i class="fas fa-calendar-check opacity-50"></i> Period: {{ now()->format('F Y') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="padding: 28px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: var(--success); border-radius: 50%; filter: blur(40px); opacity: 0.15;"></div>
            <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; margin-bottom:12px; color: var(--success); letter-spacing: 1px;">{{ __('messages.actual_achieved') }}</div>
            <div style="font-size:32px; font-weight:900; color:var(--success);">{{ number_format($wonDealsValue ?? 0) }} <span style="font-size:14px; opacity:.4; font-weight: 500;">{{ $system_branding['system_currency_symbol'] ?? 'EGP' }}</span></div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;"><i class="fas fa-arrow-trend-up opacity-50"></i> +{{ number_format($wonDealsValue ?? 0) }} this month</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="g-panel g-panel-p" style="padding: 28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div class="t-sub text-uppercase" style="font-weight:900; font-size:11px; color: var(--brand-cyan); letter-spacing: 1px;">{{ __('messages.performance_index') ?? 'Performance' }}</div>
                <div style="font-size:16px; font-weight:900; color:var(--brand-cyan);">{{ round($performancePercentage ?? 0) }}%</div>
            </div>
            <div style="width:100%; height:12px; background:rgba(255,255,255,.05); border-radius:12px; overflow:hidden; border: 1px solid rgba(255,255,255,0.03);">
                <div style="width:{{ $performancePercentage ?? 0 }}%; height:100%; background:linear-gradient(90deg, var(--brand-blue), var(--brand-cyan)); border-radius:12px; box-shadow:0 0 15px var(--brand-cyan); position: relative;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(0deg, transparent, rgba(255,255,255,0.2), transparent);"></div>
                </div>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 10px; display: flex; justify-content: space-between;">
                <span>0%</span>
                <span>Target: 100%</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-5">
    {{-- TABS SYSTEM --}}
    <div class="col-lg-8">
        <div class="g-panel" style="padding:0; overflow:hidden; border-color: rgba(255,255,255,0.08);">
            <div style="padding:15px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.015); display:flex; gap:10px;">
                <button class="nav-btn active" data-tab="personal-info"><i class="fas fa-id-card"></i> {{ __('messages.personal_info') }}</button>
                <button class="nav-btn" data-tab="signatures"><i class="fas fa-file-signature"></i> {{ __('messages.signatures') }}</button>
                <button class="nav-btn" data-tab="security"><i class="fas fa-key"></i> {{ __('messages.security') }}</button>
            </div>

            {{-- TAB CONTENT: PERSONAL INFO --}}
            <div class="tab-pane active" id="personal-info" style="padding:40px;">
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 8px;">Identity Profile</h3>
                    <p class="t-sub" style="font-size: 13px; margin: 0;">Manage your official business profile and contact details.</p>
                </div>
                <form action="{{ route('employees.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.name') }}</label>
                            <input type="text" name="name" class="gm-input" value="{{ $employee->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.job_title') }}</label>
                            <input type="text" name="job_title" class="gm-input" value="{{ $employee->job_title }}" placeholder="e.g. Sales Executive">
                        </div>
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.phone_number') }}</label>
                            <input type="text" name="phone_number" class="gm-input" value="{{ $employee->phone_number }}" placeholder="+1 234 567 8900">
                        </div>
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.username') }} <i class="fas fa-lock" style="font-size:10px; opacity:.5; margin-left:4px;"></i></label>
                            <input type="text" class="gm-input" style="opacity:.6; background:rgba(0,0,0,.15); cursor:not-allowed;" value="{{ $employee->username }}" readonly>
                        </div>
                        <div class="col-12 mt-4" style="text-align:right;">
                            <button type="submit" class="filter-btn filter-btn-primary" style="padding:12px 30px; font-weight:800;">{{ __('messages.save_changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TAB CONTENT: SIGNATURES --}}
            <div class="tab-pane" id="signatures" style="padding:40px; display:none;">
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 8px;">Business Signatures</h3>
                    <p class="t-sub" style="font-size: 13px; margin: 0;">Define your automated signatures for emails and professional quotes.</p>
                </div>
                <form action="{{ route('employees.profile.signatures') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="gm-label">{{ __('messages.email_signature') }} (HTML)</label>
                            <textarea name="email_signature" class="gm-input" rows="6" style="font-family:monospace; font-size:13px; background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.05);">{{ $employee->email_signature }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="gm-label">{{ __('messages.quote_signature') }} (Terms & Conditions)</label>
                            <textarea name="quote_signature" class="gm-input" rows="6" style="background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.05);">{{ $employee->quote_signature }}</textarea>
                        </div>
                        <div class="col-12 mt-4" style="text-align:right;">
                            <button type="submit" class="filter-btn filter-btn-primary" style="padding:14px 35px; font-weight:800;">{{ __('messages.update_signatures') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TAB CONTENT: SECURITY --}}
            <div class="tab-pane" id="security" style="padding:40px; display:none;">
                <div style="margin-bottom: 30px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 8px;">Security & Access</h3>
                    <p class="t-sub" style="font-size: 13px; margin: 0;">Ensure your account remains safe with strong credentials.</p>
                </div>
                <div style="background:rgba(251,191,36,.05); border:1px solid rgba(251,191,36,.2); border-radius:14px; padding:16px; display:flex; gap:16px; margin-bottom:30px;">
                    <i class="fas fa-exclamation-triangle" style="color:#fbbf24; font-size:20px; padding-top:2px;"></i>
                    <div>
                        <div style="color:#fbbf24; font-weight:800; font-size:14px; margin-bottom:4px;">{{ __('messages.security_warning') }}</div>
                        <p class="t-sub" style="font-size:12px; margin:0;">Changing your password will update your login credentials immediately.</p>
                    </div>
                </div>
                <form action="{{ route('employees.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $employee->name }}">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.new_password') }}</label>
                            <input type="password" name="password" class="gm-input" required minlength="6" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="gm-label">{{ __('messages.confirm_password') }}</label>
                            <input type="password" name="password_confirmation" class="gm-input" required placeholder="••••••••">
                        </div>
                        <div class="col-12 mt-4" style="text-align:right;">
                            <button type="submit" class="filter-btn filter-btn-primary" style="background:#ef4444; color:#fff; border-color:rgba(239,68,68,.3); padding:12px 30px; font-weight:800;">{{ __('messages.change_password') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ACTIVITY FEED --}}
    <div class="col-lg-4">
        <div class="g-panel" style="padding:0; overflow:hidden; height:100%; border-color: rgba(255,255,255,0.08);">
            <div style="padding:24px 28px; border-bottom:1px solid rgba(255,255,255,.05); background:rgba(255,255,255,.015); display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <i class="fas fa-bolt-lightning" style="color:var(--brand-cyan);"></i>
                    <h3 style="margin:0; font-size:16px; font-weight:800; color:#fff;">{{ __('messages.activity_feed') }}</h3>
                </div>
                <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); box-shadow: 0 0 10px var(--success);"></div>
            </div>
            <div style="padding:32px 28px; max-height:700px; overflow-y:auto;">
                @forelse($activities as $activity)
                <div style="display:flex; gap:20px; margin-bottom:30px; position:relative;">
                    @if(!$loop->last)
                        <div style="position:absolute; left:17px; top:38px; bottom:-30px; width:2px; background: linear-gradient(180deg, rgba(255,255,255,.08), transparent);"></div>
                    @endif
                    <div style="width:36px; height:36px; border-radius:12px; background:{{ $activity->activity_type == 'login' ? 'rgba(34,197,94,.1)' : 'rgba(14,165,233,.1)' }}; color:{{ $activity->activity_type == 'login' ? '#34d399' : 'var(--brand-cyan)' }}; display:flex; align-items:center; justify-content:center; z-index:1; border:1px solid rgba(255,255,255,.05); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        <i class="fas {{ $activity->activity_type == 'login' ? 'fa-sign-in-alt' : 'fa-bolt' }}" style="font-size:13px;"></i>
                    </div>
                    <div style="flex:1; padding-top:2px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                            <span class="t-sub text-uppercase" style="font-size:10px; font-weight:900; letter-spacing:1.5px; color:{{ $activity->activity_type == 'login' ? '#34d399' : 'var(--brand-cyan)' }};">{{ $activity->activity_type }}</span>
                            <span class="t-sub" style="font-size:11px; font-weight:600; opacity: 0.7;"><i class="far fa-clock"></i> {{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="color:var(--text-secondary); font-size:14px; font-weight:500; margin:0; line-height:1.6;">{{ $activity->description }}</p>
                    </div>
                </div>
                @empty
                <div class="g-empty" style="padding:60px 10px;">
                    <i class="fas fa-folder-open" style="font-size:40px; margin-bottom:15px; opacity:.2;"></i>
                    <p class="t-sub" style="font-size:14px;">No operational activities logged yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.nav-btn {
    background: transparent;
    border: 1px solid transparent;
    color: var(--text-muted);
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    transition: .2s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.nav-btn:hover { color: #fff; background: rgba(255,255,255,.03); }
.nav-btn.active {
    background: rgba(14,165,233,.1);
    color: var(--brand-cyan);
    border: 1px solid rgba(14,165,233,.2);
}
</style>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tabId = btn.dataset.tab;
        
        // Update Buttons
        document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Update Panes
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';
    });
});
</script>
@endsection
