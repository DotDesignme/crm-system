@extends('layouts.app')
@section('page-title', __('messages.deals'))
@section('content')

{{-- PAGE HEADER --}}
<div class="page-shell">
    <div class="page-shell-left">
        <div class="page-icon page-icon-rose"><i class="fas fa-handshake"></i></div>
        <div>
            <h1 class="page-shell-title">{{ __('messages.deals') }}</h1>
            <p class="page-shell-sub">{{ __('messages.kanban_board') }}</p>
        </div>
    </div>
    <div class="page-shell-right">
        {{-- Pipeline value badge --}}
        <div style="padding: 10px 18px; background:rgba(14,165,233,.1); border:1px solid rgba(14,165,233,.2); border-radius:12px; text-align:right;">
            <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">
                {{ __('messages.total_pipeline_value') }}
            </div>
            <div style="font-size:17px; font-weight:900; color:#38bdf8;">
                {{ number_format($totalValue, 2) }}
                <span style="font-size:11px; opacity:.6;">{{ $system_branding['system_currency_symbol'] ?? 'EGP' }}</span>
            </div>
        </div>
        <button class="filter-btn filter-btn-primary" onclick="document.getElementById('addDealModal').classList.add('show')">
            <i class="fas fa-plus"></i> {{ __('messages.add_deal') }}
        </button>
    </div>
</div>

{{-- KANBAN BOARD --}}
<div class="kanban-container">
    <div class="kanban-board">
        @foreach($stages as $stage)
        <div class="kanban-column" data-stage-id="{{ $stage->id }}">
            <div class="kanban-column-header">
                <div class="kanban-col-left">
                    <span class="kanban-dot" style="background:{{ $stage->color }}; box-shadow:0 0 10px {{ $stage->color }}55;"></span>
                    <h3>{{ $stage->name }}</h3>
                </div>
                <span class="kanban-count">{{ $stage->deals->count() }}</span>
            </div>

            <div class="kanban-cards sortable-list" id="stage-{{ $stage->id }}" data-stage-id="{{ $stage->id }}">
                @forelse($stage->deals as $deal)
                <div class="kanban-card" data-deal-id="{{ $deal->id }}" onclick="window.location='{{ route('deals.show', $deal) }}'">
                    <div class="kcard-title">{{ $deal->title }}</div>
                    <div class="kcard-value">
                        {{ number_format($deal->value, 2) }}
                        <span style="font-size:11px; opacity:.55; font-weight:500;">{{ $system_branding['system_currency_symbol'] ?? 'EGP' }}</span>
                    </div>

                    @if($deal->company)
                    <div class="kcard-company">
                        <i class="fas fa-building" style="font-size:10px;"></i>
                        {{ $deal->company->name }}
                    </div>
                    @endif

                    <div class="kcard-footer">
                        <div style="display:flex; gap:10px; align-items:center;">
                            @if($deal->probability)
                            <span style="font-size:11px; color:#fbbf24; font-weight:700;">
                                <i class="fas fa-percent" style="font-size:9px;"></i> {{ $deal->probability }}%
                            </span>
                            @endif
                            <span style="font-size:11px; color:var(--text-muted);">
                                <i class="far fa-clock" style="font-size:9px;"></i> {{ $deal->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($deal->assignedTo)
                        <div class="kcard-avatar" title="{{ $deal->assignedTo->name }}">
                            {{ mb_substr($deal->assignedTo->name, 0, 1) }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="kcard-empty" onclick="document.getElementById('dealStageId').value='{{ $stage->id }}'; document.getElementById('addDealModal').classList.add('show')">
                    <i class="fas fa-plus"></i>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ADD DEAL MODAL --}}
<div class="gm-overlay" id="addDealModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-handshake" style="color:#fb7185"></i>
                {{ __('messages.add_deal') }}
            </div>
            <button class="gm-close" onclick="document.getElementById('addDealModal').classList.remove('show')">&#215;</button>
        </div>
        <form method="POST" action="{{ route('deals.store') }}">
            @csrf
            <input type="hidden" name="deal_stage_id" id="dealStageId" value="{{ $stages->first()?->id }}">
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.title') }} *</label>
                    <input type="text" name="title" class="gm-input" placeholder="e.g. New Office Project" required>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="gm-label">{{ __('messages.value') }}</label>
                        <input type="number" name="value" class="gm-input" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-6">
                        <label class="gm-label">{{ __('messages.probability') }} (%)</label>
                        <input type="number" name="probability" class="gm-input" min="0" max="100" value="50">
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.company') }}</label>
                        <select name="customer_id" class="gm-input">
                            <option value="">— {{ __('messages.select_company') }} —</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.expected_close_date') }}</label>
                        <input type="date" name="expected_close_date" class="gm-input">
                    </div>
                    <div class="col-12">
                        <label class="gm-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="gm-input" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="document.getElementById('addDealModal').classList.remove('show')">
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.add') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ===== KANBAN LAYOUT ===== */
.kanban-container {
    height: calc(100vh - 230px);
    overflow-x: auto;
    overflow-y: hidden;
    padding: 4px 0 16px;
}
.kanban-board {
    display: flex;
    gap: 20px;
    height: 100%;
    min-width: max-content;
    padding-bottom: 8px;
}

/* Column */
.kanban-column {
    width: 300px;
    background: rgba(15,23,42,.5);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    max-height: 100%;
    transition: border-color .2s;
}
.kanban-column.drag-over { border-color: rgba(14,165,233,.5); }

.kanban-column-header {
    padding: 16px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,.07);
    flex-shrink: 0;
}
.kanban-col-left { display: flex; align-items: center; gap: 10px; }
.kanban-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.kanban-column-header h3 {
    font-size: 13px; font-weight: 800;
    color: #fff; margin: 0;
    text-transform: uppercase; letter-spacing: .8px;
}
.kanban-count {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
    color: var(--text-muted);
}

/* Cards list */
.kanban-cards {
    flex: 1;
    padding: 14px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 80px;
}
.kanban-cards::-webkit-scrollbar { width: 4px; }
.kanban-cards::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 8px; }

/* Individual card */
.kanban-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 14px;
    padding: 16px;
    cursor: pointer;
    transition: all .2s;
    position: relative;
}
.kanban-card:hover {
    background: rgba(255,255,255,.08);
    border-color: rgba(14,165,233,.4);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
}
.kcard-title {
    font-size: 14px; font-weight: 700;
    color: #fff; margin-bottom: 8px;
    line-height: 1.4;
}
.kcard-value {
    font-size: 18px; font-weight: 900;
    color: #38bdf8; margin-bottom: 10px;
    letter-spacing: -.3px;
}
.kcard-company {
    display: inline-flex;
    align-items: center; gap: 6px;
    font-size: 12px; color: var(--text-muted);
    background: rgba(255,255,255,.05);
    padding: 3px 10px; border-radius: 8px;
    margin-bottom: 12px;
}
.kcard-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255,255,255,.06);
    padding-top: 10px;
    margin-top: 4px;
}
.kcard-avatar {
    width: 22px; height: 22px;
    background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
    border-radius: 6px;
    color: #fff;
    font-size: 10px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(14,165,233,.3);
}
.kcard-empty {
    border: 2px dashed rgba(255,255,255,.08);
    border-radius: 12px;
    height: 80px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 18px;
    cursor: pointer;
    transition: all .2s;
}
.kcard-empty:hover {
    border-color: rgba(14,165,233,.4);
    color: #38bdf8;
    background: rgba(14,165,233,.05);
}

/* Sortable states */
.sortable-ghost { opacity: .25; transform: scale(.96); }
.sortable-drag  { cursor: grabbing; transform: rotate(1.5deg) scale(1.04); box-shadow: 0 24px 60px rgba(0,0,0,.5) !important; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lists = document.querySelectorAll('.sortable-list');
    lists.forEach(list => {
        new Sortable(list, {
            group: 'deals-kanban',
            animation: 250,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            draggable: '.kanban-card',
            onEnd: function(evt) {
                const dealId = evt.item.getAttribute('data-deal-id');
                const newStageId = evt.to.getAttribute('data-stage-id');
                if (evt.from !== evt.to) updateDealStage(dealId, newStageId);
            }
        });
    });

    async function updateDealStage(dealId, stageId) {
        try {
            const resp = await fetch(`/deals/${dealId}/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ deal_stage_id: stageId })
            });
            const data = await resp.json();
            if (!data.success) location.reload();
        } catch(e) { location.reload(); }
    }

    document.querySelectorAll('.gm-overlay').forEach(o => {
        o.addEventListener('click', e => { if(e.target === o) o.classList.remove('show'); });
    });
});
</script>
@endsection
@endsection
