@extends('layouts.app')

@section('page-title', __('messages.add_template'))

@section('content')
<div class="page-header" style="margin-bottom: 30px;">
    <div class="header-content">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('task-templates.index') }}" class="btn btn-icon" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
            </a>
            <div>
                <h2 class="text-glow" style="font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin: 0;">{{ __('messages.add_template') }}</h2>
                <p style="color: var(--text-secondary); font-size: 14px; margin-top: 4px;">{{ __('messages.create_new_sequence_desc') }}</p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('task-templates.store') }}" method="POST" id="templateForm">
    @csrf
    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px; align-items: start;">
        
        <!-- SIDEBAR: BASICS -->
        <div class="glass-card" style="position: sticky; top: 100px; padding: 28px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle" style="color: var(--brand-cyan);"></i>
                {{ __('messages.template_basics') }}
            </h3>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="color: var(--text-muted); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('messages.template_name') }}</label>
                <input type="text" name="name" class="form-control" required placeholder="{{ __('messages.template_name_placeholder') }}" style="background: rgba(0,0,0,0.2); border-color: var(--glass-border);">
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label style="color: var(--text-muted); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('messages.description') }}</label>
                <textarea name="description" class="form-control" rows="4" placeholder="{{ __('messages.template_desc_placeholder') }}" style="background: rgba(0,0,0,0.2); border-color: var(--glass-border);"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 14px; font-weight: 800; font-size: 14px; display: flex; justify-content: center; gap: 10px; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);">
                <i class="fas fa-save"></i>
                {{ __('messages.save_template') }}
            </button>
        </div>

        <!-- MAIN: SEQUENCE BUILDER -->
        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-stream" style="color: var(--brand-cyan);"></i>
                    {{ __('messages.task_sequence') }}
                </h3>
                <button type="button" class="btn btn-success" id="addTaskBtn" style="padding: 8px 18px; border-radius: 10px; font-size: 12px;">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('messages.add_task') }}
                </button>
            </div>

            <div id="tasksContainer" style="position: relative; padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 40px;">
                <!-- Vertical Line -->
                <div style="position: absolute; {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 14px; top: 10px; bottom: 10px; width: 2px; background: linear-gradient(180deg, var(--brand-cyan), transparent); opacity: 0.3;"></div>
                
                <!-- Tasks will be injected here -->
            </div>
        </div>
    </div>
</form>

{{-- JS Row Template --}}
<template id="taskRowTemplate">
    <div class="task-row fade-in" data-index="{index}" style="position: relative; margin-bottom: 30px;">
        <!-- Step Dot -->
        <div class="step-number" style="position: absolute; {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -40px; top: 10px; width: 30px; height: 30px; background: var(--brand-navy-accent); border: 2px solid var(--brand-cyan); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: var(--brand-cyan); z-index: 2; box-shadow: 0 0 15px rgba(14, 165, 233, 0.3);">
            {number}
        </div>

        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 20px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="type-icon" style="width: 40px; height: 40px; background: rgba(14, 165, 233, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--brand-cyan);">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 style="margin: 0; font-size: 15px; font-weight: 700; color: #fff;">{{ __('messages.step_details') }}</h5>
                </div>
                <button type="button" class="btn btn-icon delete-row" style="background: rgba(239, 68, 68, 0.1); color: #f87171; width: 32px; height: 32px;">
                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                </button>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="font-size: 11px; margin-bottom: 6px;">{{ __('messages.task_title') }}</label>
                    <input type="text" name="items[{index}][title]" class="form-control" required style="background: rgba(0,0,0,0.2);">
                </div>
                <div class="form-group">
                    <label style="font-size: 11px; margin-bottom: 6px;">{{ __('messages.task_type') }}</label>
                    <select name="items[{index}][type]" class="form-control type-selector" style="background: rgba(0,0,0,0.2);">
                        <option value="follow_up">{{ __('messages.follow_up') }}</option>
                        <option value="call">{{ __('messages.call') }}</option>
                        <option value="whatsapp">{{ __('messages.whatsapp') }}</option>
                        <option value="email">{{ __('messages.email') }}</option>
                        <option value="meeting">{{ __('messages.meeting') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-size: 11px; margin-bottom: 6px;">{{ __('messages.priority') }}</label>
                    <select name="items[{index}][priority]" class="form-control" style="background: rgba(0,0,0,0.2);">
                        <option value="low">{{ __('messages.low') }}</option>
                        <option value="medium" selected>{{ __('messages.medium') }}</option>
                        <option value="high">{{ __('messages.high') }}</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div class="form-group">
                    <label style="font-size: 11px; margin-bottom: 6px;">{{ __('messages.delay') }}</label>
                    <div style="display: flex; align-items: center; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid var(--glass-border); overflow: hidden;">
                        <span style="padding: 0 12px; font-size: 11px; color: var(--text-muted);">{{ __('messages.after') }}</span>
                        <input type="number" name="items[{index}][delay_days]" class="form-control" value="0" min="0" style="border: none; background: transparent; text-align: center; padding: 10px 0;">
                        <span style="padding: 0 12px; font-size: 11px; color: var(--text-muted);">{{ __('messages.days') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-size: 11px; margin-bottom: 6px;">{{ __('messages.task_description') }}</label>
                    <textarea name="items[{index}][description]" class="form-control" rows="1" style="background: rgba(0,0,0,0.2);"></textarea>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('tasksContainer');
    const template = document.getElementById('taskRowTemplate').innerHTML;
    const addBtn = document.getElementById('addTaskBtn');
    let rowCount = 0;

    const iconMap = {
        'follow_up': 'fa-tasks',
        'call': 'fa-phone-alt',
        'whatsapp': 'fab fa-whatsapp',
        'email': 'fa-envelope',
        'meeting': 'fa-handshake'
    };

    function addTaskRow() {
        const html = template
            .replace(/{index}/g, rowCount)
            .replace(/{number}/g, rowCount + 1);
        
        const div = document.createElement('div');
        div.innerHTML = html;
        const row = div.firstElementChild;
        
        container.appendChild(row);

        // Handle icons
        const typeSelector = row.querySelector('.type-selector');
        const icon = row.querySelector('.type-icon i');
        
        typeSelector.addEventListener('change', function() {
            const val = this.value;
            icon.className = (val === 'whatsapp') ? iconMap[val] : `fas ${iconMap[val]}`;
        });

        // Handle delete
        row.querySelector('.delete-row').addEventListener('click', function() {
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => {
                row.remove();
                updateRowNumbers();
            }, 300);
        });

        rowCount++;
    }

    function updateRowNumbers() {
        const rows = container.querySelectorAll('.task-row');
        rows.forEach((row, idx) => {
            row.querySelector('.step-number').textContent = idx + 1;
        });
        rowCount = rows.length;
    }

    addBtn.addEventListener('click', addTaskRow);
    
    // Initial row
    addTaskRow();
});
</script>

<style>
    .task-row { transition: all 0.3s ease; }
    .form-control:focus { outline: none; border-color: var(--brand-cyan) !important; }
</style>
@endsection
