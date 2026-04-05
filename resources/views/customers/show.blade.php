@extends('layouts.app')
@section('page-title', $customer->name)

@section('content')
<style>
    .note-item { position: relative; transition: all 0.3s ease; border: 1px solid var(--glass-border) !important; }
    .note-item:hover { background: rgba(255,255,255,0.05) !important; transform: translateY(-2px); border-color: var(--brand-cyan) !important; }
    .note-actions { 
        position: absolute; top: 12px; right: 12px; 
        display: flex; gap: 10px; opacity: 0; transition: all 0.2s ease;
        background: rgba(0,0,0,0.6); padding: 6px 12px; border-radius: 12px; 
        backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.1);
        z-index: 10;
    }
    .note-item:hover .note-actions { opacity: 1; }
    .note-action { color: var(--text-muted); cursor: pointer; font-size: 14px; transition: color 0.2s; }
    .note-action:hover { color: #fff; }
    .note-action.delete:hover { color: var(--danger); }
    
    .note-highlight { animation: highlight-pulse 2s ease-out; }
    @keyframes highlight-pulse {
        0% { box-shadow: 0 0 0 0 rgba(100, 210, 255, 0.4); background: rgba(100, 210, 255, 0.1); }
        70% { box-shadow: 0 0 0 10px rgba(100, 210, 255, 0); }
        100% { background: rgba(255,255,255,0.03); }
    }
</style>
<div class="page-header" style="margin-bottom: 40px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="avatar-xl" style="width: 60px; height: 60px; border-radius: 16px; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; color: var(--brand-cyan); border: 1px solid rgba(255,255,255,0.1);">
            {{ mb_substr($customer->name, 0, 1) }}
        </div>
        <div>
            <h2 class="text-glow" style="margin-bottom: 6px; font-size: 28px; font-weight: 800;">{{ $customer->name }}</h2>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge badge-glass-success" style="font-size: 11px; padding: 6px 14px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);">{{ $customer->industry ?? __('messages.not_specified') }}</span>
                <span style="font-size: 11px; color: var(--text-muted);">
                    <i class="fas fa-map-marker-alt"></i> {{ $customer->city ?? '---' }}, {{ $customer->country ?? '---' }}
                </span>
            </div>
        </div>
        <div style="width: 1px; height: 50px; background: linear-gradient(to bottom, transparent, var(--glass-border), transparent);"></div>
        <div style="text-align: center;">
            @php
                $shadowColor = 'var(--brand-cyan)';
                if(($customer->health_score ?? 'unknown') == 'hot') $shadowColor = 'var(--success)';
                elseif(($customer->health_score ?? 'unknown') == 'warm') $shadowColor = 'var(--warning)';
            @endphp
            <div style="font-size: 24px; font-weight: 900; color: #fff; text-shadow: 0 0 10px {{ $shadowColor }};">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-top: 4px;">{{ __('messages.health_' . ($customer->health_score ?? 'unknown')) }}</div>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button class="btn btn-primary" style="padding: 12px 24px;" onclick="document.getElementById('editCustomerModal').classList.add('show')">
            <i class="fas fa-pen" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.edit') }}
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-ghost" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 3fr 2fr; gap: 24px;">
    <!-- Left Column: Tabs & Contacts -->
    <div>
        <div class="glass-card fade-in" style="padding: 0; overflow: hidden; border: 1px solid var(--glass-border); margin-bottom: 24px;">
            <div style="display: flex; gap: 4px; border-bottom: 1px solid var(--glass-border); padding: 10px 15px; background: rgba(255,255,255,0.03);">
                <button class="btn btn-sm tab-btn active" data-tab="contacts" onclick="switchTab('contacts')" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fas fa-users"></i> {{ __('messages.contacts') }}
                </button>
                <button class="btn btn-ghost btn-sm tab-btn" data-tab="notes" onclick="switchTab('notes')" style="color: var(--text-muted);">
                    <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                </button>
            </div>

            <div style="padding: 30px;">
                <!-- Contacts Tab -->
                <div id="tab-contacts" class="tab-content transition-fade">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="font-size: 15px; font-weight: 700; color: #fff;">{{ __('messages.customer_contacts') }}</h4>
                        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addContactModal').classList.add('show')">
                            <i class="fas fa-plus"></i> {{ __('messages.add_contact') }}
                        </button>
                    </div>

                    @if($customer->contacts->count())
                        <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        @foreach($customer->contacts as $contact)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid var(--glass-border); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--brand-cyan); border: 1px solid rgba(255,255,255,0.1);">
                                        {{ mb_substr($contact->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: #fff; margin-bottom: 4px;">
                                            {{ $contact->name }} 
                                            @if($contact->is_decision_maker)
                                            <i class="fas fa-star" style="color: var(--warning); font-size: 10px; margin-left: 5px;"></i>
                                            @endif
                                        </div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            <i class="fas fa-briefcase"></i> {{ $contact->position ?? '---' }} &middot; 
                                            <i class="fas fa-envelope"></i> {{ $contact->email }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    @if($contact->phone)
                                    <a href="tel:{{ $contact->phone }}" class="btn btn-icon" style="color: var(--brand-cyan); background: rgba(255,255,255,0.05);"><i class="fas fa-phone"></i></a>
                                    @endif
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon" style="color: var(--danger); background: rgba(255,255,255,0.05);"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                    <div style="text-align: center; padding: 60px; opacity: 0.2;">
                        <i class="fas fa-users" style="font-size: 50px; margin-bottom: 20px; display: block;"></i>
                        <p>{{ __('messages.no_contacts_found') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Notes Tab -->
                <div id="tab-notes" class="tab-content transition-fade" style="display: none;">
                    <div style="margin-bottom: 24px;">
                        <form action="{{ route('notes.store') }}" method="POST" style="background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 16px; padding: 15px;">
                            @csrf
                            <input type="hidden" name="noteable_type" value="App\Models\Customer">
                            <input type="hidden" name="noteable_id" value="{{ $customer->id }}">
                            <div class="form-group mb-0">
                                <textarea name="content" class="form-control" rows="3" placeholder="{{ __('messages.add_note_placeholder') }}" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); color: #fff;" required></textarea>
                            </div>
                            <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div style="max-height: 500px; overflow-y: auto; padding-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;">
                        @forelse($customer->notes_list->sortByDesc('created_at') as $note)
                        <div id="note-{{ $note->id }}" class="glass-card note-item" style="padding: 15px; margin-bottom: 15px; background: rgba(255,255,255,0.03);">
                            <!-- Hover Actions -->
                            <div class="note-actions">
                                <span class="note-action" title="{{ __('messages.copy_link') }}" onclick="copyNoteLink({{ $note->id }})"><i class="fas fa-link"></i></span>
                                <span class="note-action" title="{{ __('messages.copy_content') }}" onclick="copyNoteContent('note-text-{{ $note->id }}')"><i class="fas fa-copy"></i></span>
                                <a href="https://wa.me/?text={{ urlencode($note->content) }}" target="_blank" class="note-action" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                @if(Auth::id() == $note->employee_id || Auth::user()->is_admin)
                                <form action="{{ route('notes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_note') }}')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="note-action delete" style="background:none; border:none; padding:0;"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                @endif
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 24px; height: 24px; border-radius: 6px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: var(--brand-cyan);">
                                        {{ mb_substr($note->employee->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; font-weight: 700; color: #fff;">{{ $note->employee->name ?? 'System' }}</div>
                                        <div style="font-size: 10px; color: var(--text-muted);">{{ $note->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div id="note-text-{{ $note->id }}" style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; white-space: pre-line;">
                                {!! nl2br(e($note->content)) !!}
                            </div>
                        </div>
                        @empty
                        <div style="text-align: center; padding: 40px 20px; opacity: 0.5;">
                            <i class="fas fa-sticky-note" style="font-size: 24px; margin-bottom: 10px; color: var(--brand-cyan);"></i>
                            <p style="font-size: 12px; margin: 0;">{{ __('messages.no_notes_available') }}</p>
                        </div>
                        @endforelse

                        @if($customer->notes)
                        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--glass-border);">
                            <h5 style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 12px;">{{ app()->getLocale() == 'ar' ? 'الملاحظات الأساسية' : 'Static Info Notes' }}</h5>
                            <div style="padding: 15px; background: rgba(255,255,255,0.01); border-radius: 12px; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 13px;">
                                {{ $customer->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card" style="border: 1px solid var(--glass-border);">
            <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 20px; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-history" style="color: var(--brand-cyan);"></i> {{ __('messages.activity_timeline') }}
            </h3>
            @if($activities->count())
                <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    @foreach($activities as $activity)
                    <div style="display: flex; gap: 15px; margin-bottom: 20px; position: relative;">
                        <div style="width: 2px; background: var(--glass-border); position: absolute; left: 15px; top: 30px; bottom: -20px; z-index: 1;"></div>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--dashboard-bg); border: 2px solid var(--brand-cyan); display: flex; align-items: center; justify-content: center; z-index: 2; color: var(--brand-cyan); font-size: 12px; flex-shrink: 0;">
                            <i class="fas fa-dot-circle"></i>
                        </div>
                        <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border); flex-grow: 1;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                <span style="font-weight: 700; font-size: 13px; color: #fff;">{{ $activity->subject ?? $activity->description }}</span>
                                <span style="font-size: 11px; color: var(--text-muted);">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-user-circle"></i> {{ $activity->user->name ?? 'System' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px; opacity: 0.5;">
                    <p>{{ __('messages.no_activity_found') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Details & Quick Actions -->
    <div>
        <!-- Stats Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="glass-card" style="padding: 20px; text-align: center; border: 1px solid var(--glass-border);">
                <div style="font-size: 28px; font-weight: 900; color: var(--brand-cyan); margin-bottom: 4px;">{{ $customer->deals->count() }}</div>
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 700;">{{ __('messages.total_deals') }}</div>
            </div>
            <div class="glass-card" style="padding: 20px; text-align: center; border: 1px solid var(--glass-border);">
                <div style="font-size: 28px; font-weight: 900; color: var(--success); margin-bottom: 4px;">{{ $customer->leads->count() }}</div>
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 700;">{{ __('messages.total_leads') }}</div>
            </div>
        </div>

        <div class="glass-card" style="margin-bottom: 24px; border: 1px solid var(--glass-border);">
            <h3 style="font-size: 12px; font-weight: 900; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 2px; color: var(--brand-cyan); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle"></i> {{ __('messages.customer_info') }}
            </h3>
            <table class="detail-list" style="width: 100%;">
                @if($customer->phone)
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.phone') }}</th>
                    <td style="padding: 12px 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-weight: 700; color: #fff;">{{ $customer->phone }}</span>
                            <a href="tel:{{ $customer->phone }}" class="btn btn-icon btn-sm" style="color: var(--brand-cyan); background: rgba(255,255,255,0.05);"><i class="fas fa-phone"></i></a>
                        </div>
                    </td>
                </tr>
                @endif
                @if($customer->email)
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.email') }}</th>
                    <td style="padding: 12px 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 13px; color: var(--text-secondary);">{{ $customer->email }}</span>
                            <a href="mailto:{{ $customer->email }}" class="btn btn-icon btn-sm" style="color: var(--warning); background: rgba(255,255,255,0.05);"><i class="fas fa-envelope"></i></a>
                        </div>
                    </td>
                </tr>
                @endif
                @if($customer->website)
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.website') }}</th>
                    <td style="padding: 12px 0; color: #fff;">
                        <a href="{{ str_contains($customer->website, 'http') ? $customer->website : 'http://'.$customer->website }}" target="_blank" style="color: var(--brand-cyan); text-decoration: none;">
                            {{ $customer->website }}
                        </a>
                    </td>
                </tr>
                @endif
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.assigned_to') }}</th>
                    <td style="padding: 12px 0; color: #fff;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 20px; height: 20px; border-radius: 4px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;">
                                {{ substr($customer->assignedEmployee->name ?? '?', 0, 1) }}
                            </div>
                            {{ $customer->assignedEmployee->name ?? '---' }}
                        </div>
                    </td>
                </tr>
                <tr><th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.status') }}</th><td style="padding: 12px 0;"><span style="color: var(--accent); font-weight: 700; text-transform: capitalize;">{{ $customer->status ?? 'active' }}</span></td></tr>
            </table>
        </div>

        <div class="glass-card" style="border: 1px solid var(--glass-border); margin-bottom: 24px;">
            <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 20px; color: #fff;">{{ __('messages.quick_actions') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; grid-column: span 2; background: rgba(255,158,11,0.1); border: 1px solid rgba(255,158,11,0.2); border-radius: 12px;" onclick="document.getElementById('addDealModal').classList.add('show')">
                    <i class="fas fa-handshake" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--warning);"></i> {{ __('messages.new_deal') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('call')">
                    <i class="fas fa-phone" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--brand-cyan);"></i> {{ __('messages.log_call') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('whatsapp')">
                    <i class="fab fa-whatsapp" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: #25D366;"></i> {{ __('messages.log_wa') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('email')">
                    <i class="fas fa-envelope" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--warning);"></i> {{ __('messages.log_email') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="document.getElementById('quickTaskModal').classList.add('show')">
                    <i class="fas fa-tasks" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--accent);"></i> {{ __('messages.log_task') }}
                </button>
                @stack('plugin-lead-actions')
            </div>
        </div>

        <div class="glass-card" style="border: 1px solid var(--glass-border);">
            <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 20px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-file-signature text-success" style="margin-right: 8px;"></i> {{ __('messages.recent_deals') }}</span>
                <a href="{{ route('deals.index', ['customer_id' => $customer->id]) }}" style="color: var(--brand-cyan); font-size: 11px; text-decoration: none;">View All</a>
            </h3>
            
            @forelse($customer->deals->sortByDesc('created_at')->take(4) as $deal)
            <div style="padding: 12px 15px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-weight: 700; font-size: 13px; color: #fff;">{{ $deal->title }}</span>
                    <span style="font-weight: 700; font-size: 13px; color: var(--success);">{{ number_format($deal->value) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 10px; padding: 4px 8px; border-radius: 4px; background: rgba(255,255,255,0.1); color: #fff;">{{ $deal->stage->name ?? '---' }}</span>
                    <a href="{{ route('deals.show', $deal) }}" style="color: var(--text-muted);"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 20px; opacity: 0.5;">
                <p style="font-size: 12px; margin: 0;">{{ __('messages.no_deals_found') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal-overlay" id="editCustomerModal">
    <div class="modal-glass modal-lg" style="width: 100%; max-width: 800px;">
        <div class="modal-header">
            <h3 style="margin: 0; color: #fff; font-size: 18px; font-weight: 800;"><i class="fas fa-pen-nib" style="margin-right: 10px; color: var(--brand-cyan);"></i> {{ __('messages.edit_customer') }}</h3>
            <button class="btn-close" onclick="document.getElementById('editCustomerModal').classList.remove('show')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>{{ __('messages.customer_name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.industry') }}</label>
                        <input type="text" name="industry" class="form-control" value="{{ $customer->industry }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.health_score') }}</label>
                        <select name="health_score" class="form-control">
                            <option value="hot" {{ $customer->health_score == 'hot' ? 'selected' : '' }}>{{ __('messages.health_hot') }}</option>
                            <option value="warm" {{ $customer->health_score == 'warm' ? 'selected' : '' }}>{{ __('messages.health_warm') }}</option>
                            <option value="cold" {{ $customer->health_score == 'cold' ? 'selected' : '' }}>{{ __('messages.health_cold') }}</option>
                            <option value="churning" {{ $customer->health_score == 'churning' ? 'selected' : '' }}>{{ __('messages.health_churning') }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label>{{ __('messages.notes') }}</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $customer->notes }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('editCustomerModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Contact Modal -->
<div class="modal-overlay" id="addContactModal">
    <div class="modal-glass" style="width: 100%; max-width: 500px;">
        <div class="modal-header">
            <h3 style="margin: 0; color: #fff; font-size: 18px; font-weight: 800;"><i class="fas fa-plus" style="margin-right: 10px; color: var(--success);"></i> {{ __('messages.add_contact') }}</h3>
            <button class="btn-close" onclick="document.getElementById('addContactModal').classList.remove('show')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('customers.contacts.store', $customer) }}">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.name') }}</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.position') }}</label>
                    <input type="text" name="position" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.email') }}</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.phone') }}</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div style="margin-top: 15px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-secondary); font-size: 14px;">
                        <input type="checkbox" name="is_decision_maker" value="1" style="width: 18px; height: 18px; accent-color: var(--brand-cyan);">
                        {{ __('messages.decision_maker') }}
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('addContactModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
            </div>
        </form>
    </div>
</div>

<x-communication-modals :entity="$customer" />

<!-- Add Deal Modal -->
<div class="modal-overlay" id="addDealModal">
    <div class="modal-glass" style="width: 100%; max-width: 500px;">
        <div class="modal-header">
            <h3 style="margin: 0; color: #fff; font-size: 18px; font-weight: 800;"><i class="fas fa-handshake" style="margin-right: 10px; color: var(--warning);"></i> {{ __('messages.add_deal') }}</h3>
            <button class="btn-close" onclick="document.getElementById('addDealModal').classList.remove('show')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('deals.store') }}">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.title') }} *</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Website Development">
                </div>
                <div class="grid-2" style="gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>{{ __('messages.value') }}</label>
                        <input type="number" name="value" class="form-control" step="0.01" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.stage') }} *</label>
                        <select name="deal_stage_id" class="form-control" required>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.description') }}</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('addDealModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Task Modal -->
<div class="modal-overlay" id="quickTaskModal">
    <div class="modal-glass" style="width: 100%; max-width: 450px;">
        <div class="modal-header">
            <h3 style="margin: 0; color: #fff; font-size: 18px; font-weight: 800;"><i class="fas fa-tasks" style="margin-right: 10px; color: var(--accent);"></i> {{ __('messages.add_task') }}</h3>
            <button class="btn-close" onclick="document.getElementById('quickTaskModal').classList.remove('show')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <input type="hidden" name="taskable_type" value="App\Models\Customer">
            <input type="hidden" name="taskable_id" value="{{ $customer->id }}">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>{{ __('messages.title') }} *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Follow up on proposal" required>
                </div>
                <div class="grid-2" style="gap: 12px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>{{ __('messages.priority') }}</label>
                        <select name="priority" class="form-control">
                            <option value="low">{{ __('messages.priority_low') }}</option>
                            <option value="medium" selected>{{ __('messages.priority_medium') }}</option>
                            <option value="high">{{ __('messages.priority_high') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.due_at') }}</label>
                        <input type="datetime-local" name="due_at" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('quickTaskModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.add_task') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--text-muted)';
        btn.style.border = '1px solid transparent';
        if (btn.dataset.tab === tabId) {
            btn.classList.add('active');
            btn.style.background = 'rgba(255,255,255,0.1)';
            btn.style.color = '#fff';
            btn.style.border = '1px solid rgba(255,255,255,0.1)';
        }
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
        content.classList.remove('show');
    });

    const activeContent = document.getElementById('tab-' + tabId);
    activeContent.style.display = 'block';
    
    setTimeout(() => {
        activeContent.classList.add('show');
    }, 10);
}

// Ensure modals close when clicking overlay
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('show');
        }
    });
});
    // Sharing functions
    function copyNoteContent(elementId) {
        const text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            showToast("{{ __('messages.copied_to_clipboard') }}");
        });
    }

    function copyNoteLink(id) {
        const link = window.location.origin + window.location.pathname + '#note-' + id;
        navigator.clipboard.writeText(link).then(() => {
            showToast("{{ __('messages.link_copied') }}");
        });
    }

    function showToast(msg) {
        const toast = document.createElement('div');
        toast.style = "position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: var(--primary); color: #fff; padding: 10px 20px; border-radius: 30px; font-size: 13px; z-index: 9999; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2);";
        toast.innerText = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }

    // Scroll to hash and highlight
    window.onload = function() {
        if (window.location.hash) {
            const id = window.location.hash.substring(1);
            const el = document.getElementById(id);
            if (el) {
                switchTab('notes');
                setTimeout(() => {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('note-highlight');
                }, 500);
            }
        }
    };
</script>
@endsection
