@extends('layouts.app')
@section('page-title', $lead->name)

@section('content')
<div class="page-header" style="margin-bottom: 40px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div>
            <h2 class="text-glow" style="margin-bottom: 6px; font-size: 28px; font-weight: 800;">{{ $lead->name }}</h2>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge badge-{{ $lead->status }}" style="font-size: 11px; padding: 6px 14px; border-radius: 10px;">{{ $lead->status_ar }}</span>
                <span class="badge badge-{{ $lead->priority }}" style="font-size: 10px; opacity: 0.7; border: 1px solid rgba(255,255,255,0.1);">{{ $lead->priority_ar }}</span>
            </div>
        </div>
        <div style="width: 1px; height: 50px; background: linear-gradient(to bottom, transparent, var(--glass-border), transparent);"></div>
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: 900; color: #fff; text-shadow: 0 0 10px {{ $lead->score > 70 ? 'var(--success)' : ($lead->score > 40 ? 'var(--warning)' : 'rgba(255,255,255,0.2)') }};">
                {{ $lead->score }}%
            </div>
            <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">{{ __('messages.health_score') }}</div>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        @can('edit-leads')
        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary" style="padding: 12px 24px;">
            <i class="fas fa-pen" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.edit') }}
        </a>
        @endcan
        @can('delete-leads')
        @if(Auth::user()->is_admin || $lead->added_by === Auth::id())
        <form action="{{ route('leads.destroy', $lead) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" style="padding: 12px 24px; background: var(--danger); border: none;">
                <i class="fas fa-trash" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.delete') }}
            </button>
        </form>
        @endif
        @endcan
        <a href="{{ route('leads.index') }}" class="btn btn-ghost" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 3fr 2fr; gap: 24px;">
    <!-- Left Column: Tabs & Timeline -->
    <div>
        <div class="glass-card fade-in" style="padding: 0; overflow: hidden; border: 1px solid var(--glass-border);">
            <div style="display: flex; gap: 4px; border-bottom: 1px solid var(--glass-border); padding: 10px 15px; background: rgba(255,255,255,0.03);">
                <button class="btn btn-sm tab-btn active" data-tab="activities" onclick="switchTab('activities')" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fas fa-clock-rotate-left"></i> {{ __('messages.activities') }}
                </button>
                <button class="btn btn-ghost btn-sm tab-btn" data-tab="tasks" onclick="switchTab('tasks')" style="color: var(--text-muted);">
                    <i class="fas fa-tasks"></i> {{ __('messages.tasks') }}
                </button>
                <button class="btn btn-ghost btn-sm tab-btn" data-tab="notes" onclick="switchTab('notes')" style="color: var(--text-muted);">
                    <i class="fas fa-sticky-note"></i> {{ __('messages.notes') }}
                </button>
                <button class="btn btn-ghost btn-sm tab-btn" data-tab="communications" onclick="switchTab('communications')" style="color: var(--text-muted);">
                    <i class="fas fa-comments"></i> {{ __('messages.communications') }}
                </button>
                <button class="btn btn-ghost btn-sm tab-btn" data-tab="files" onclick="switchTab('files')" style="color: var(--text-muted);">
                    <i class="fas fa-file-alt"></i> {{ __('messages.files') }}
                </button>
            </div>

            <div style="padding: 30px;">
                <!-- Activities Tab -->
                <div id="tab-activities" class="tab-content transition-fade">
                    <x-timeline :activities="$lead->activities" />
                </div>

                <!-- Tasks Tab -->
                <div id="tab-tasks" class="tab-content transition-fade" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="font-size: 15px; font-weight: 700; color: #fff;">{{ __('messages.pending_tasks') }}</h4>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-ghost btn-sm" style="background: rgba(255,255,255,0.05);" onclick="document.getElementById('applyTemplateModal').classList.add('show')">
                                <i class="fas fa-magic"></i> {{ __('messages.use_template') }}
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="document.getElementById('quickTaskModal').classList.add('show')">
                                <i class="fas fa-plus"></i> {{ __('messages.add_task') }}
                            </button>
                        </div>
                    </div>
                    @if($lead->tasks->count())
                        @foreach($lead->tasks->where('status', '!=', 'completed') as $task)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px; background: rgba(0,0,0,0.2); border-radius: 16px; margin-bottom: 12px; border: 1px solid var(--glass-border);">
                            <div>
                                <div style="font-weight: 700; font-size: 15px; color: #fff;">{{ $task->title }}</div>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                    <i class="fas fa-calendar-alt" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 6px;"></i> {{ $task->due_at ? $task->due_at->translatedFormat('d M, Y') : __('messages.no_due_date') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('tasks.update', $task) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success); width: 40px; height: 40px; border-radius: 12px;"><i class="fas fa-check"></i></button>
                            </form>
                        </div>
                        @endforeach
                    @else
                    <div style="text-align: center; padding: 40px; opacity: 0.5;">
                        <p>{{ __('messages.no_pending_tasks') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Notes Tab -->
                <div id="tab-notes" class="tab-content transition-fade" style="display: none;">
                    @if($lead->notes_list && $lead->notes_list->count())
                        @foreach($lead->notes_list as $note)
                        <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 16px;">
                            <div style="font-size: 14px; line-height: 1.7; color: var(--text-secondary);">{{ $note->content }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 15px; display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
                                <span style="display: flex; align-items: center; gap: 6px;"><i class="fas fa-user-circle" style="font-size: 14px;"></i> {{ $note->employee->name }}</span>
                                <span>{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    <form method="POST" action="{{ route('notes.store') }}" style="margin-top: 24px;">
                        @csrf
                        <input type="hidden" name="noteable_type" value="App\Models\Lead">
                        <input type="hidden" name="noteable_id" value="{{ $lead->id }}">
                        <textarea name="content" class="form-control" placeholder="{{ __('messages.add_note_placeholder') }}" rows="4" style="margin-bottom: 16px;"></textarea>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-plus" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.save_note') }}
                        </button>
                    </form>
                </div>

                <!-- Communications Tab -->
                <div id="tab-communications" class="tab-content transition-fade" style="display: none;">
                     @if($lead->communications->count())
                        @foreach($lead->communications as $comm)
                        <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <div style="font-weight: 800; font-size: 14px; color: var(--brand-cyan); text-transform: uppercase; letter-spacing: 0.5px;">{{ $comm->type_label }}</div>
                                <span style="font-size: 11px; color: var(--text-muted); background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px;">{{ $comm->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 14px; line-height: 1.6; color: var(--text-secondary);">{{ $comm->content }}</div>
                        </div>
                        @endforeach
                    @else
                    <div style="text-align: center; padding: 50px; opacity: 0.3;">
                        <i class="fas fa-comments" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                        <p>{{ __('messages.no_communications') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Files Tab -->
                <div id="tab-files" class="tab-content transition-fade" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h4 style="font-size: 15px; font-weight: 700; color: #fff;">{{ __('messages.attached_files') }}</h4>
                        <form id="uploadForm" action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px;">
                            @csrf
                            <input type="hidden" name="attachable_type" value="App\Models\Lead">
                            <input type="hidden" name="attachable_id" value="{{ $lead->id }}">
                            <input type="file" name="file" id="fileInput" style="display: none;" onchange="document.getElementById('uploadForm').submit()">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('fileInput').click()" style="border-radius: 10px;">
                                <i class="fas fa-upload" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px;"></i> {{ __('messages.upload') }}
                            </button>
                        </form>
                    </div>
                    @if($lead->attachments->count())
                        <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        @foreach($lead->attachments as $file)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid var(--glass-border); transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: var(--brand-cyan); border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="fas fa-file-alt" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: #fff;">{{ $file->file_name }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ number_format($file->file_size / 1024, 1) }} KB &middot; {{ $file->created_at->translatedFormat('d M, Y') }}</div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('attachments.download', $file) }}" class="btn btn-icon" style="color: var(--brand-cyan); background: rgba(255,255,255,0.05);"><i class="fas fa-download"></i></a>
                                    <form action="{{ route('attachments.destroy', $file) }}" method="POST" onsubmit="return confirm('{{ addslashes(__('messages.confirm_delete')) }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon" style="color: var(--danger); background: rgba(255,255,255,0.05);"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                    <div style="text-align: center; padding: 60px; opacity: 0.2;">
                        <i class="fas fa-folder-open" style="font-size: 50px; margin-bottom: 20px; display: block;"></i>
                        <p>{{ __('messages.no_files') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details & Quick Actions -->
    <div>
        <div class="glass-card" style="margin-bottom: 24px; border: 1px solid var(--glass-border);">
            <h3 style="font-size: 12px; font-weight: 900; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 2px; color: var(--brand-cyan); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle"></i> {{ __('messages.lead_info') }}
            </h3>
            <table class="detail-list" style="width: 100%;">
                @if($lead->phone)
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.phone') }}</th>
                    <td style="padding: 12px 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-weight: 700; color: #fff;">{{ $lead->phone }}</span>
                            <div style="display: flex; gap: 6px;">
                                <a href="tel:{{ $lead->phone }}" class="btn btn-icon btn-sm" style="color: var(--brand-cyan); background: rgba(255,255,255,0.05);"><i class="fas fa-phone"></i></a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" 
                                   target="_blank" 
                                   class="btn btn-icon btn-sm" 
                                   style="color: #25D366; background: rgba(37, 211, 102, 0.1);"
                                   onclick="logWhatsApp('{{ $lead->phone }}', 'App\\Models\\Lead', '{{ $lead->id }}')">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                @if($lead->email)
                <tr>
                    <th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.email') }}</th>
                    <td style="padding: 12px 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 13px; color: var(--text-secondary);">{{ $lead->email }}</span>
                            <a href="mailto:{{ $lead->email }}" class="btn btn-icon btn-sm" style="color: var(--warning); background: rgba(255,255,255,0.05);"><i class="fas fa-envelope"></i></a>
                        </div>
                    </td>
                </tr>
                @endif
                <tr><th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.source') }}</th><td style="padding: 12px 0; color: #fff;">{{ $lead->source ?? '-' }}</td></tr>
                <tr><th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.page') }}</th><td style="padding: 12px 0;"><span style="color: var(--accent); font-weight: 700;">{{ $lead->company->name }}</span></td></tr>
                <tr><th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.added_by') }}</th><td style="padding: 12px 0; color: #fff;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; border-radius: 4px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;">{{ substr($lead->employee->name, 0, 1) }}</div>
                        {{ $lead->employee->name }}
                    </div>
                </td></tr>
                <tr><th style="color: var(--text-muted); font-weight: 500; font-size: 13px; padding: 12px 0;">{{ __('messages.created') }}</th><td style="padding: 12px 0; color: var(--text-muted); font-size: 12px;">{{ $lead->created_at->translatedFormat('d M Y, H:i') }}</td></tr>
            </table>
        </div>

        @if($lead->tag)
        <div class="glass-card" style="margin-bottom: 24px; border: 1px solid var(--glass-border);">
            <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 20px; color: #fff;">{{ __('messages.tags') }}</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach(explode(',', $lead->tag) as $tag)
                <span class="tag-badge" style="background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 8px; font-size: 11px; border: 1px solid var(--glass-border); color: var(--text-secondary); display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-tag" style="font-size: 9px; color: var(--brand-cyan);"></i> {{ trim($tag) }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="glass-card" style="border: 1px solid var(--glass-border);">
            <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 20px; color: #fff;">{{ __('messages.quick_actions') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('call')">
                    <i class="fas fa-phone" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--brand-cyan);"></i> {{ __('messages.log_call') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('whatsapp')">
                    <i class="fab fa-whatsapp" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: #25D366;"></i> {{ __('messages.log_wa') }}
                </button>
                <button class="btn btn-ghost btn-sm" style="justify-content: center; height: 44px; grid-column: span 2; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px;" onclick="openLogModal('email')">
                    <i class="fas fa-envelope" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 8px; color: var(--warning);"></i> {{ __('messages.log_email') }}
                </button>
                @stack('plugin-lead-actions')
            </div>
        </div>
    </div>
</div>

<x-communication-modals :entity="$lead" />

<!-- Quick Task Modal -->
<div class="modal-overlay" id="quickTaskModal">
    <div class="modal" style="max-width: 450px;">
        <div class="modal-header">
            <h3>{{ __('messages.add_task') }}</h3>
            <button class="modal-close" onclick="document.getElementById('quickTaskModal').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <input type="hidden" name="taskable_type" value="App\Models\Lead">
            <input type="hidden" name="taskable_id" value="{{ $lead->id }}">
            <div class="form-group">
                <label>{{ __('messages.title') }} *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Follow up on proposal" required>
            </div>
            <div class="grid-2" style="gap: 12px;">
                <div class="form-group">
                    <label>{{ __('messages.priority') }}</label>
                    <select name="priority" class="form-control">
                        <option value="low">{{ __('messages.priority_low') }}</option>
                        <option value="medium" selected>{{ __('messages.priority_medium') }}</option>
                        <option value="high">{{ __('messages.priority_high') }}</option>
                        <option value="urgent">{{ __('messages.priority_urgent') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.due_at') }}</label>
                    <input type="datetime-local" name="due_at" class="form-control">
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('quickTaskModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.add_task') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

<!-- Apply Template Modal -->
<div class="modal-overlay" id="applyTemplateModal">
    <div class="modal" id="applyTemplateStep1" style="max-width: 450px;">
        <div class="modal-header">
            <h3>{{ __('messages.apply_task_template') }}</h3>
            <button class="modal-close" onclick="closeApplyTemplateModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <div class="form-group">
                <label>{{ __('messages.select_template') }} *</label>
                <select id="tpl_template_id" class="form-control">
                    <option value="">{{ __('messages.choose_template_placeholder') }}</option>
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->items_count }} {{ __('messages.tasks') }})</option>
                    @endforeach
                </select>
                <p class="text-muted small mt-2" style="font-size: 11px;">{{ __('messages.apply_template_help') }}</p>
            </div>
            
            <div class="form-group border-top pt-3" style="border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 16px;">
                <label>{{ __('messages.assign_to_employee') }} ({{ __('messages.optional') }})</label>
                <select id="tpl_assigned_to" class="form-control">
                    <option value="">{{ __('messages.default_assigned_to') }}</option>
                    @foreach($lead->company->employees as $emp)
                        <option value="{{ $emp->id }}" {{ $emp->id == $lead->added_by ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-ghost" onclick="closeApplyTemplateModal()">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="loadTemplatePreview()">{{ __('messages.next_step') }} <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i></button>
            </div>
        </div>
    </div>

    <!-- Step 2: Preview & Edit -->
    <div class="modal" id="applyTemplateStep2" style="max-width: 650px; display: none;">
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button type="button" class="btn btn-icon btn-sm" onclick="showStep(1)" style="background: rgba(255,255,255,0.05);"><i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i></button>
                <h3>{{ __('messages.preview_tasks') }}</h3>
            </div>
            <button class="modal-close" onclick="closeApplyTemplateModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">{{ __('messages.edit_tasks_help') }}</p>
            
            <div id="previewTaskContainer" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                <!-- Tasks will be loaded here -->
                <div class="loading-spinner" style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary-light);"></i>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--glass-border);">
                <button type="button" class="btn btn-ghost" onclick="showStep(1)">{{ __('messages.previous_step') }}</button>
                <button type="button" id="confirmApplyBtn" class="btn btn-primary" onclick="confirmApplyTemplate()">
                    <i class="fas fa-magic"></i> {{ __('messages.confirm_and_apply') }}
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let leadEmployees = @json($lead->company->employees->map->only(['id', 'name']));
let currentPreviewItems = [];

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.style.background = 'transparent';
        el.style.color = 'var(--text-secondary)';
        el.classList.remove('active');
    });
    document.getElementById('tab-' + tab).style.display = 'block';
    const btn = document.querySelector('[data-tab="' + tab + '"]');
    if (btn) {
        btn.style.background = 'rgba(99,102,241,0.15)';
        btn.style.color = 'var(--primary-light)';
        btn.classList.add('active');
    }
}

function closeApplyTemplateModal() {
    document.getElementById('applyTemplateModal').classList.remove('show');
    showStep(1);
}

function showStep(step) {
    document.getElementById('applyTemplateStep1').style.display = step === 1 ? 'block' : 'none';
    document.getElementById('applyTemplateStep2').style.display = step === 2 ? 'block' : 'none';
}

function loadTemplatePreview() {
    const templateId = document.getElementById('tpl_template_id').value;
    const assignedTo = document.getElementById('tpl_assigned_to').value;

    if (!templateId) {
        alert("{{ __('messages.choose_template_placeholder') }}");
        return;
    }

    showStep(2);
    const container = document.getElementById('previewTaskContainer');
    container.innerHTML = `<div class="loading-spinner" style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary-light);"></i></div>`;

    fetch(`/leads/{{ $lead->id }}/task-templates/${templateId}/preview`)
        .then(response => response.json())
        .then(data => {
            currentPreviewItems = data.items;
            renderPreview(data.items, assignedTo);
        })
        .catch(error => {
            console.error('Error fetching preview:', error);
            container.innerHTML = `<p class="text-danger">Error loading preview.</p>`;
        });
}

function renderPreview(items, defaultAssignee) {
    const container = document.getElementById('previewTaskContainer');
    let html = '';

    items.forEach((item, index) => {
        let assigneeOptions = `<option value="">{{ __('messages.default_assigned_to') }}</option>`;
        leadEmployees.forEach(emp => {
            let selected = emp.id == defaultAssignee ? 'selected' : '';
            assigneeOptions += `<option value="${emp.id}" ${selected}>${emp.name}</option>`;
        });

        html += `
            <div class="preview-task-item" id="task_row_${index}" style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 12px; padding: 16px; margin-bottom: 12px; position: relative;">
                <button type="button" onclick="removePreviewTask(${index})" style="position: absolute; top: 12px; {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 12px; background: none; border: none; color: var(--danger); opacity: 0.6; cursor: pointer;"><i class="fas fa-trash"></i></button>
                
                <div class="form-group mb-3">
                    <label style="font-size: 11px; text-transform: uppercase;">{{ __('messages.title') }}</label>
                    <input type="text" class="form-control form-control-sm preview-title" value="${item.title}" style="background: rgba(0,0,0,0.2);">
                </div>

                <div class="grid-2" style="gap: 12px;">
                    <div class="form-group mb-0">
                        <label style="font-size: 11px; text-transform: uppercase;">{{ __('messages.due_at') }}</label>
                        <input type="datetime-local" class="form-control form-control-sm preview-due-at" value="${item.due_at}" style="background: rgba(0,0,0,0.2);">
                    </div>
                    <div class="form-group mb-0">
                        <label style="font-size: 11px; text-transform: uppercase;">{{ __('messages.assign_to_employee') }}</label>
                        <select class="form-control form-control-sm preview-assigned-to" style="background: rgba(0,0,0,0.2);">
                            ${assigneeOptions}
                        </select>
                    </div>
                </div>
                <input type="hidden" class="preview-description" value="${item.description || ''}">
                <input type="hidden" class="preview-type" value="${item.type || 'follow_up'}">
                <input type="hidden" class="preview-priority" value="${item.priority || 'medium'}">
            </div>
        `;
    });

    container.innerHTML = html;
}

function removePreviewTask(index) {
    const row = document.getElementById(`task_row_${index}`);
    if (row) row.remove();
}

function confirmApplyTemplate() {
    const btn = document.getElementById('confirmApplyBtn');
    const templateId = document.getElementById('tpl_template_id').value;
    const taskItems = [];
    
    document.querySelectorAll('.preview-task-item').forEach(el => {
        taskItems.push({
            title: el.querySelector('.preview-title').value,
            due_at: el.querySelector('.preview-due-at').value,
            assigned_to: el.querySelector('.preview-assigned-to').value,
            description: el.querySelector('.preview-description').value,
            type: el.querySelector('.preview-type').value,
            priority: el.querySelector('.preview-priority').value,
        });
    });

    if (taskItems.length === 0) {
        alert("Please include at least one task.");
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Processing...`;

    fetch(`{{ route('leads.apply-template', $lead) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            template_id: templateId,
            items: taskItems
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || "An error occurred.");
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-magic"></i> {{ __('messages.confirm_and_apply') }}`;
        }
    })
    .catch(error => {
        console.error('Error applying template:', error);
        alert("Communication error.");
        btn.disabled = false;
        btn.innerHTML = `<i class="fas fa-magic"></i> {{ __('messages.confirm_and_apply') }}`;
    });
}
</script>
@endsection
