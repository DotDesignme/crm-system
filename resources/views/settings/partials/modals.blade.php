<!-- Stage Modal -->
<div class="gm-overlay" id="stageModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-layer-group" style="color:var(--brand-cyan)"></i>
                {{ __('messages.edit_stage') ?? 'Edit Stage' }}
            </div>
            <button class="gm-close" onclick="closeModal('stageModal')">&#215;</button>
        </div>
        <form id="stageForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="stageMethod" value="POST">
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.stage_name') }}</label>
                    <input type="text" name="name" class="gm-input" required>
                </div>
                <div class="mb-4">
                    <label class="gm-label">{{ __('messages.stage_color') }}</label>
                    <input type="color" name="color" class="gm-input" style="height:50px; padding:4px; border-radius:12px;" value="#0ea5e9">
                </div>
                <div style="display:flex; gap:20px; align-items:center;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-muted); font-size:14px; font-weight:600;">
                        <input type="checkbox" name="is_won" value="1" style="width:16px; height:16px; accent-color:#10b981;">
                        {{ __('messages.is_won_stage') }}
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-muted); font-size:14px; font-weight:600;">
                        <input type="checkbox" name="is_lost" value="1" style="width:16px; height:16px; accent-color:#ef4444;">
                        {{ __('messages.is_lost_stage') }}
                    </label>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('stageModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loss Reason Modal -->
<div class="gm-overlay" id="reasonModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-times-circle" style="color:#ef4444"></i>
                {{ __('messages.edit_loss_reason') ?? 'Loss Reason' }}
            </div>
            <button class="gm-close" onclick="closeModal('reasonModal')">&#215;</button>
        </div>
        <form id="reasonForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="reasonMethod" value="POST">
            <div class="gm-body">
                <div class="mb-4">
                    <label class="gm-label">{{ __('messages.reason_text') }}</label>
                    <input type="text" name="reason" class="gm-input" required>
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:var(--text-muted); font-size:14px; font-weight:600;">
                    <input type="checkbox" name="is_active" value="1" style="width:16px; height:16px; accent-color:var(--brand-cyan);" checked>
                    {{ __('messages.reason_active') ?? 'Active' }}
                </label>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('reasonModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
