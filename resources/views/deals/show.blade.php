@extends('layouts.app')
@section('page-title', $deal->title)

@section('content')
<div class="page-shell">
    <div class="page-shell-left">
        <a href="{{ route('deals.index') }}" class="btn btn-icon btn-sm" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
        </a>
        <div style="display:flex; flex-direction:column; gap:4px;">
            <h2 class="text-glow" style="margin:0; font-size: 24px; font-weight: 800;">{{ $deal->title }}</h2>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:13px; color:var(--text-muted);">{{ __('messages.deal_id') }}: #{{ $deal->id }}</span>
                <span class="g-pill" style="background: {{ $deal->stage->color ?? '#a855f7' }}22; color: {{ $deal->stage->color ?? '#a855f7' }}; border: 1px solid {{ $deal->stage->color ?? '#a855f7' }}44; padding:2px 8px; font-size:11px;">
                    {{ $deal->stage->name ?? '-' }}
                </span>
            </div>
        </div>
    </div>
    <div class="page-shell-right" style="gap:10px;">
        @if(!$deal->stage->is_won && !$deal->stage->is_lost)
            <form action="{{ route('deals.update', $deal) }}" method="POST" style="display: inline;">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $deal->title }}">
                <input type="hidden" name="deal_stage_id" value="{{ \App\Models\DealStage::where('is_won', true)->first()?->id }}">
                <button type="submit" class="filter-btn" style="background: linear-gradient(135deg, #10b981, #059669); color:#fff; border:none; box-shadow:0 0 15px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-check-circle"></i> {{ __('messages.mark_as_won') }}
                </button>
            </form>
            <button class="filter-btn" data-bs-toggle="modal" data-bs-target="#lossDealModal" style="background: linear-gradient(135deg, #ef4444, #dc2626); color:#fff; border:none; box-shadow:0 0 15px rgba(239, 68, 68, 0.3);">
                <i class="fas fa-times-circle"></i> {{ __('messages.mark_as_lost') }}
            </button>
        @endif
        <button class="filter-btn filter-btn-ghost" onclick="window.print()">
            <i class="fas fa-print"></i>
        </button>
        <a href="{{ route('deals.edit', $deal) }}" class="filter-btn filter-btn-primary">
            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
        </a>
    </div>
</div>

<!-- Pipeline Progress Bar -->
<div class="g-panel g-panel-p" style="margin-bottom:24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 class="rpt-heading" style="margin:0;"><i class="fas fa-stream" style="color:#22d3ee;"></i> {{ __('messages.pipeline_status') }}</h3>
        <div style="font-weight: 800; color: #22d3ee; font-size: 20px;">{{ number_format($deal->value, 2) }} <span style="font-size: 13px; opacity: 0.6;">{{ $system_branding['system_currency_symbol'] }}</span></div>
    </div>
    <div style="display: flex; gap: 8px; height: 14px;">
        @php
            $foundCurrent = false;
            $stages_list = \App\Models\DealStage::orderBy('order')->get();
        @endphp
        @foreach($stages_list as $stage)
            @php
                $isCurrent = $deal->deal_stage_id == $stage->id;
                $isPast = !$foundCurrent && !$isCurrent;
                if ($isCurrent) $foundCurrent = true;
                
                $bgColor = 'rgba(255,255,255,0.05)';
                if ($isPast) $bgColor = 'linear-gradient(90deg, #22d3ee, #0ea5e9)';
                if ($isCurrent) $bgColor = 'linear-gradient(90deg, #8b5cf6, #d946ef)';
            @endphp
            <div style="flex: 1; background: {!! $bgColor !!}; border-radius: 10px; position: relative; {{ $isCurrent ? 'box-shadow: 0 0 15px rgba(217, 70, 239, 0.5); opacity: 1;' : 'opacity: 0.5;' }}">
                @if($isCurrent)
                    <div style="position: absolute; top: -32px; left: 50%; transform: translateX(-50%); white-space: nowrap; font-size: 11px; font-weight: 800; color: #fdf4ff; background:rgba(192, 38, 211, 0.4); padding:4px 10px; border-radius:12px; border:1px solid rgba(217, 70, 239, 0.5);">{{ $stage->name }}</div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 3fr 1.5fr; gap: 24px;">
    <!-- Main Content -->
    <div style="display:flex; flex-direction:column; gap:24px;">
        <div class="g-panel">
            <div style="display: flex; gap: 12px; padding: 16px; border-bottom: 1px solid var(--glass-border); overflow-x:auto;">
                <button class="filter-btn filter-btn-primary tab-btn transition-all active" data-tab="activities" onclick="switchTab('activities')" style="border-radius:12px;">
                    <i class="fas fa-bolt" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.activities') }}
                </button>
                <button class="filter-btn filter-btn-ghost tab-btn transition-all" data-tab="notes" onclick="switchTab('notes')" style="border-radius:12px;">
                    <i class="fas fa-sticky-note" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.notes') }}
                </button>
                <button class="filter-btn filter-btn-ghost tab-btn transition-all" data-tab="communications" onclick="switchTab('communications')" style="border-radius:12px;">
                    <i class="fas fa-comments" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.communications') }}
                </button>
                <button class="filter-btn filter-btn-ghost tab-btn transition-all" data-tab="files" onclick="switchTab('files')" style="border-radius:12px;">
                    <i class="fas fa-folder" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.files') }}
                </button>
            </div>

            <div class="g-panel-p" style="min-height:500px;">
                <div id="tab-activities" class="tab-content transition-fade">
                    <x-timeline :activities="$deal->activities" />
                </div>

                <div id="tab-notes" class="tab-content transition-fade" style="display: none;">
                    @if($deal->notes_list->count())
                    @foreach($deal->notes_list as $note)
                    <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <span style="font-size: 11px; color: var(--text-muted);"><i class="far fa-clock"></i> {{ $note->created_at->diffForHumans() }}</span>
                            <div class="g-pill g-pill-new"><i class="fas fa-user-circle"></i> {{ $note->employee->name ?? '-' }}</div>
                        </div>
                        <div style="line-height: 1.6; font-size: 14px; color: #fff;">{{ $note->content }}</div>
                    </div>
                    @endforeach
                    @else
                    <div class="g-empty"><i class="fas fa-sticky-note"></i><h3>{{ __('messages.no_notes') }}</h3></div>
                    @endif
                    
                    <form method="POST" action="{{ route('notes.store') }}" style="margin-top: 24px; padding: 20px; background: rgba(0,0,0,0.2); border-radius: 16px; border:1px dashed var(--glass-border);">
                        @csrf
                        <input type="hidden" name="noteable_type" value="App\Models\Deal">
                        <input type="hidden" name="noteable_id" value="{{ $deal->id }}">
                        <div class="form-group">
                            <textarea name="content" class="gm-input" placeholder="{{ __('messages.add_note') }}" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="filter-btn filter-btn-primary" style="margin-top: 10px;">
                            <i class="fas fa-paper-plane"></i> {{ __('messages.add_note') }}
                        </button>
                    </form>
                </div>

                <div id="tab-communications" class="tab-content transition-fade" style="display: none;">
                    @if($deal->communications->count())
                    @foreach($deal->communications as $comm)
                    <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <div class="g-pill" style="background:rgba(168,85,247,0.1); color:#d946ef; border:1px solid rgba(217,70,239,0.3);"><i class="fas fa-bullhorn"></i> {{ $comm->type_label ?? $comm->type }}</div>
                            <span style="font-size: 11px; color: var(--text-muted);"><i class="far fa-clock"></i> {{ $comm->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-weight: 700; margin-bottom: 8px; color:#fff;">{{ $comm->subject }}</div>
                        <div style="line-height: 1.6; font-size: 14px; color: var(--text-secondary);">{{ $comm->content }}</div>
                    </div>
                    @endforeach
                    @else
                    <div class="g-empty"><i class="fas fa-comments"></i><h3>{{ __('messages.no_communications') }}</h3></div>
                    @endif
                </div>

                <div id="tab-files" class="tab-content transition-fade" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="font-size: 15px; font-weight: 700; color: #fff; margin:0;">{{ __('messages.attached_files') }}</h4>
                        <form id="uploadForm" action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px;">
                            @csrf
                            <input type="hidden" name="attachable_type" value="App\Models\Deal">
                            <input type="hidden" name="attachable_id" value="{{ $deal->id }}">
                            <input type="file" name="file" id="fileInput" style="display: none;" onchange="document.getElementById('uploadForm').submit()">
                            <button type="button" class="filter-btn filter-btn-primary" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-upload"></i> {{ __('messages.upload') }}
                            </button>
                        </form>
                    </div>
                    @if($deal->attachments->count())
                        <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        @foreach($deal->attachments as $file)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid var(--glass-border);">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, rgba(34,211,238,0.2), rgba(14,165,233,0.2)); display: flex; align-items: center; justify-content: center; color: #22d3ee; border: 1px solid rgba(34,211,238,0.3);">
                                        <i class="fas fa-file-alt" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: #fff;">{{ $file->file_name }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ number_format($file->file_size / 1024, 1) }} KB &middot; {{ $file->created_at->translatedFormat('d M, Y') }}</div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('attachments.download', $file) }}" class="btn btn-icon" style="color: #22d3ee; background: rgba(255,255,255,0.05);"><i class="fas fa-download"></i></a>
                                    <form action="{{ route('attachments.destroy', $file) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon" style="color: var(--danger); background: rgba(255,255,255,0.05);"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                    <div class="g-empty"><i class="fas fa-folder-open"></i><h3>{{ __('messages.no_files') }}</h3></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display:flex; flex-direction:column; gap:24px;">
        <div class="g-panel g-panel-p">
            <h3 class="rpt-heading"><i class="fas fa-info-circle" style="color: #3b82f6;"></i> {{ __('messages.deal_info') }}</h3>
            <table class="detail-list" style="width: 100%; font-size:13px;">
                @if($deal->company)
                <tr>
                    <td style="color: var(--text-muted); padding: 12px 0;">{{ __('messages.company') }}</td>
                    <td style="padding: 12px 0; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">
                        <a href="{{ route('customers.show', $deal->company) }}" style="color: #3b82f6; text-decoration: none; font-weight: 700;">{{ $deal->company->name }}</a>
                    </td>
                </tr>
                @endif
                @if($deal->lead)
                <tr><td style="color: var(--text-muted); padding: 12px 0; border-top:1px solid var(--glass-border);">{{ __('messages.lead') }}</td><td style="padding: 12px 0; border-top:1px solid var(--glass-border); font-weight: 700; color: #fff; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $deal->lead->name }}</td></tr>
                @endif
                @if($deal->assignedTo)
                <tr><td style="color: var(--text-muted); padding: 12px 0; border-top:1px solid var(--glass-border);">{{ __('messages.assigned_to') }}</td><td style="padding: 12px 0; border-top:1px solid var(--glass-border); color: #fff; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $deal->assignedTo->name }}</td></tr>
                @endif
                <tr><td style="color: var(--text-muted); padding: 12px 0; border-top:1px solid var(--glass-border);">{{ __('messages.expected_close_date') }}</td><td style="padding: 12px 0; border-top:1px solid var(--glass-border); color: #fbbf24; font-weight: 700; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};"><i class="far fa-calendar-alt"></i> {{ $deal->expected_close_date ? $deal->expected_close_date->translatedFormat('d M Y') : '-' }}</td></tr>
                <tr><td style="color: var(--text-muted); padding: 12px 0; border-top:1px solid var(--glass-border);">{{ __('messages.source') }}</td><td style="padding: 12px 0; border-top:1px solid var(--glass-border); color: #fff; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $deal->source ?? '-' }}</td></tr>
                <tr><td style="color: var(--text-muted); padding: 12px 0; border-top:1px solid var(--glass-border);">{{ __('messages.created') }}</td><td style="padding: 12px 0; border-top:1px solid var(--glass-border); color: var(--text-muted); font-size: 12px; text-align:{{ app()->getLocale() == 'ar' ? 'left' : 'right' }};">{{ $deal->created_at->translatedFormat('d M Y H:i') }}</td></tr>
            </table>
        </div>

        @if($deal->quotations->count())
        <div class="g-panel g-panel-p">
            <h3 class="rpt-heading"><i class="fas fa-file-invoice" style="color: #eab308;"></i> {{ __('messages.quotations') }}</h3>
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($deal->quotations as $quotation)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid var(--glass-border); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'">
                    <div>
                        <div style="font-weight: 700; font-size: 13px; color: #fff;">{{ $quotation->title ?? $quotation->quotation_number }}</div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><i class="far fa-calendar-alt"></i> {{ $quotation->created_at->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="g-pill" style="background:rgba(234,179,8,0.1); color:#facc15; border:1px solid rgba(250,204,21,0.2);">${{ number_format($quotation->total, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="g-panel g-panel-p">
            <h3 class="rpt-heading"><i class="fas fa-bolt" style="color:#d946ef;"></i> {{ __('messages.quick_actions') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button class="filter-btn filter-btn-ghost" style="justify-content: center; height: 44px; border: 1px dashed var(--glass-border);" onclick="openLogModal('call')">
                    <i class="fas fa-phone" style="color: #22d3ee;"></i> {{ __('messages.log_call') }}
                </button>
                <button class="filter-btn filter-btn-ghost" style="justify-content: center; height: 44px; border: 1px dashed var(--glass-border);" onclick="openLogModal('whatsapp')">
                    <i class="fab fa-whatsapp" style="color: #22c55e;"></i> {{ __('messages.log_wa') }}
                </button>
                <button class="filter-btn filter-btn-ghost" style="justify-content: center; height: 44px; grid-column: span 2; border: 1px dashed var(--glass-border);" onclick="openLogModal('email')">
                    <i class="fas fa-envelope" style="color: #facc15;"></i> {{ __('messages.log_email') }}
                </button>
            </div>
        </div>
    </div>
</div>

<x-communication-modals :entity="$deal" />
@endsection

@section('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.style.background = 'transparent';
        el.style.color = 'var(--text-secondary)';
        el.classList.remove('active');
    });
    document.getElementById('tab-' + tab).style.display = 'block';
    const btn = document.querySelector('[data-tab="' + tab + '"]');
    if(btn) {
        btn.style.background = 'rgba(99,102,241,0.15)';
        btn.style.color = 'var(--primary-light)';
        btn.classList.add('active');
    }
}

// Loss Modal Logic
function openLossModal() {
    const modal = new bootstrap.Modal(document.getElementById('lossDealModal'));
    modal.show();
}
</script>

<!-- Loss Deal Modal -->
<div class="modal fade" id="lossDealModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="glass-card modal-content" style="border-radius: 28px; border: 1px solid var(--glass-border); background: var(--bg-secondary); padding: 10px;">
            <div class="modal-header" style="border: none; padding: 20px 25px;">
                <h3 class="modal-title" style="color: #fff; font-weight: 800; font-size: 20px;">{{ __('messages.mark_as_lost') }}</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('deals.update', $deal) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $deal->title }}">
                <input type="hidden" name="deal_stage_id" value="{{ \App\Models\DealStage::where('is_lost', true)->first()?->id }}">
                <div class="modal-body" style="padding: 0 25px 25px;">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="label-muted">{{ __('messages.select_loss_reason') }}</label>
                        <select name="loss_reason_id" class="form-control form-control-recessed" required>
                            <option value="">-- {{ __('messages.select_reason') }} --</option>
                            @foreach($lossReasons as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="label-muted">{{ __('messages.loss_notes') }}</label>
                        <textarea name="loss_notes" class="form-control form-control-recessed" rows="3" placeholder="{{ __('messages.extra_details_placeholder') ?? 'Enter details...' }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 0 25px 25px; gap: 12px;">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal" style="color: var(--text-muted); text-decoration: none; font-weight: 700;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-danger btn-glow" style="border-radius: 12px; padding: 10px 30px;">{{ __('messages.confirm_loss') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
