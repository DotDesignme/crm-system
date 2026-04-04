@extends('layouts.app')
@section('page-title', __('messages.edit_role') . ': ' . $role->name)

@section('content')
<div class="page-header">
    <div>
        <h2 style="margin-bottom: 4px;">{{ __('messages.edit_role') }}: {{ $role->name }}</h2>
        <a href="{{ route('roles.index') }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i> {{ __('messages.back_to_list') }}
        </a>
    </div>
</div>

<form action="{{ route('roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="grid-2" style="grid-template-columns: 1fr 2fr; gap: 24px;">
        <div class="glass-card">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">{{ __('messages.role_details') }}</h3>
            <div class="form-group">
                <label>{{ __('messages.role_name') }} *</label>
                <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label>{{ __('messages.slug') }} *</label>
                <input type="text" name="slug" value="{{ $role->slug }}" class="form-control" required {{ $role->slug == 'admin' ? 'disabled' : '' }}>
                @if($role->slug == 'admin') <input type="hidden" name="slug" value="admin"> @endif
            </div>
            <div class="form-group">
                <label>{{ __('messages.description') }}</label>
                <textarea name="description" class="form-control" rows="3">{{ $role->description }}</textarea>
            </div>
            <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary w-full">{{ __('messages.update_role') }}</button>
            </div>
        </div>

        <div class="glass-card">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">{{ __('messages.assign_permissions') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                @foreach($permissions as $group => $perms)
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 12px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 8px; margin-bottom: 12px;">
                        <h4 style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: var(--brand-cyan); margin: 0;">
                            {{ __('messages.module_' . $group) }}
                        </h4>
                        <label style="font-size: 11px; cursor: pointer; color: var(--text-muted); display:flex; align-items:center; gap:6px;">
                            <input type="checkbox" onchange="toggleModule(this, '{{$group}}')" class="custom-checkbox"> 
                            {{ app()->getLocale() == 'ar' ? 'تحديد الكل' : 'Select All' }}
                        </label>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($perms as $perm)
                        @php
                            $action = explode('_', $perm->name)[0];
                            $isArabic = app()->getLocale() == 'ar';
                            $map = [
                                'view' => $isArabic ? 'عرض' : 'View',
                                'create' => $isArabic ? 'إضافة' : 'Create',
                                'edit' => $isArabic ? 'تعديل' : 'Edit',
                                'delete' => $isArabic ? 'حذف' : 'Delete',
                                'manage' => $isArabic ? 'إدارة' : 'Manage',
                                'export' => $isArabic ? 'تصدير' : 'Export',
                                'import' => $isArabic ? 'استيراد' : 'Import',
                            ];
                            $label = $map[$action] ?? $perm->name;
                        @endphp
                        <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; background: rgba(0,0,0,0.2); padding: 6px 12px; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="custom-checkbox mod-chk-{{$group}}" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                            <span style="font-size: 12px; font-weight: 500;">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</form>

<script>
    function toggleModule(source, group) {
        let checkboxes = document.querySelectorAll('.mod-chk-' + group);
        checkboxes.forEach(cb => cb.checked = source.checked);
    }
</script>
@endsection
