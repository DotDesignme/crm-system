<!-- Reset Password Modal -->
<div class="gm-overlay" id="resetPasswordModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-key" style="color:#fbbf24;"></i>
                Reset Password: <span id="reset-emp-name" style="color:#fbbf24; margin-left:6px;"></span>
            </div>
            <button class="gm-close" onclick="closeModal('resetPasswordModal')">&#215;</button>
        </div>
        <form id="resetPasswordForm" method="POST">
            @csrf @method('PUT')
            <div class="gm-body">
                <div class="mb-4">
                    <label class="gm-label">{{ __('messages.new_password') }}</label>
                    <input type="password" name="password" class="gm-input" required minlength="6" placeholder="••••••••">
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.confirm_new_password') ?? 'Confirm New Password' }}</label>
                    <input type="password" name="password_confirmation" class="gm-input" required minlength="6" placeholder="••••••••">
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('resetPasswordModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.update_password') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create/Edit Employee Modal -->
<div class="gm-overlay" id="employeeModal">
    <div class="gm-box gm-box-lg">
        <div class="gm-header">
            <div class="gm-title" id="empModalTitle">
                <i class="fas fa-user" style="color:var(--brand-cyan);"></i>
            </div>
            <button class="gm-close" onclick="closeModal('employeeModal')">&#215;</button>
        </div>
        <form id="employeeForm" method="POST">
            @csrf <div id="emp-method"></div>
            <div class="gm-body">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="gm-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="emp-name" class="gm-input" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="gm-label">{{ __('messages.username') }}</label>
                        <input type="text" name="username" id="emp-username" class="gm-input" required>
                    </div>
                    <div class="col-md-12 mb-3" id="password-group">
                        <label class="gm-label">{{ __('messages.password') }}</label>
                        <input type="password" name="password" id="emp-password" class="gm-input">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="gm-label">{{ __('messages.company') }}</label>
                        <select name="companies[]" id="emp-company" class="gm-input" multiple style="height:100px; padding:10px;" required>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                            @endforeach
                        </select>
                        <div class="t-sub" style="margin-top:6px; font-size:11px;">Hold Command/Ctrl to select multiple companies.</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="gm-label">{{ __('messages.roles') }}</label>
                        <select name="roles[]" id="emp-roles" class="gm-input" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('employeeModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Target Modal -->
<div class="gm-overlay" id="targetModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title">
                <i class="fas fa-bullseye" style="color:var(--success);"></i>
                {{ __('messages.set_target') }}: <span id="target-emp-name" style="color:var(--success); margin-left:6px;"></span>
            </div>
            <button class="gm-close" onclick="closeModal('targetModal')">&#215;</button>
        </div>
        <form id="targetForm" method="POST">
            @csrf
            <div class="gm-body">
                <div class="row g-3">
                    <div class="col-6 mb-3">
                        <label class="gm-label">{{ __('messages.month') }}</label>
                        <select name="month" class="gm-input">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{$m}}" {{ $m == now()->month ? 'selected' : '' }}>{{ date('F', mktime(0,0,0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="gm-label">{{ __('messages.year') }}</label>
                        <input type="number" name="year" class="gm-input" value="{{ now()->year }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.target_amount') }}</label>
                    <div style="display:flex;">
                        <input type="number" name="target_amount" id="target-amount" class="gm-input" style="border-top-right-radius:0; border-bottom-right-radius:0;" required>
                        <span class="gm-input" style="width:auto; border-top-left-radius:0; border-bottom-left-radius:0; background:rgba(255,255,255,.05); border-left:none;">{{ $system_branding['system_currency_symbol'] ?? 'EGP' }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.commission_percentage') }} (%)</label>
                    <input type="number" name="commission_percentage" id="target-comm" class="gm-input" required step="0.1">
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('targetModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Company Modal -->
<div class="gm-overlay" id="companyModal">
    <div class="gm-box">
        <div class="gm-header">
            <div class="gm-title" id="compModalTitle">
                <i class="fas fa-building" style="color:var(--brand-cyan);"></i>
            </div>
            <button class="gm-close" onclick="closeModal('companyModal')">&#215;</button>
        </div>
        <form id="companyForm" method="POST">
            @csrf <div id="comp-method"></div>
            <div class="gm-body">
                <div class="mb-3">
                    <label class="gm-label">{{ __('messages.company_name') }}</label>
                    <input type="text" name="name" id="comp-name" class="gm-input" required>
                </div>
            </div>
            <div class="gm-footer">
                <button type="button" class="filter-btn filter-btn-ghost" onclick="closeModal('companyModal')">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    function openCreateModal() {
        if(typeof activeMainTab !== 'undefined' && activeMainTab === 'employees') {
            document.getElementById('empModalTitle').innerHTML = '<i class="fas fa-user" style="color:var(--brand-cyan);"></i> {{ __("messages.add_employee") }}';
            document.getElementById('employeeForm').action = "{{ route('employees.store') }}";
            document.getElementById('emp-method').innerHTML = "";
            document.getElementById('password-group').style.display = 'block';
            document.getElementById('emp-password').required = true;
            document.getElementById('employeeForm').reset();
            document.getElementById('employeeModal').classList.add('show');
        } else {
            document.getElementById('compModalTitle').innerHTML = '<i class="fas fa-building" style="color:var(--brand-cyan);"></i> {{ __("messages.add_company") }}';
            document.getElementById('companyForm').action = "{{ route('companies.store') }}";
            document.getElementById('comp-method').innerHTML = "";
            document.getElementById('companyForm').reset();
            document.getElementById('companyModal').classList.add('show');
        }
    }

    function editEmployee(emp) {
        document.getElementById('empModalTitle').innerHTML = '<i class="fas fa-user-edit" style="color:var(--brand-cyan);"></i> {{ __("messages.edit_employee") }}';
        document.getElementById('employeeForm').action = "/employees/" + emp.id;
        document.getElementById('emp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('emp-name').value = emp.name;
        document.getElementById('emp-username').value = emp.username;
        document.getElementById('password-group').style.display = 'none';
        document.getElementById('emp-password').required = false;
        
        // Setup Companies (Multi)
        const compSelect = document.getElementById('emp-company');
        if (compSelect) {
            const compIds = emp.companies && emp.companies.length > 0 
                ? emp.companies.map(c => c.id) 
                : (emp.company_id ? [emp.company_id] : []);
            Array.from(compSelect.options).forEach(opt => {
                opt.selected = compIds.includes(parseInt(opt.value));
            });
        }

        // Setup Roles (Single)
        const rolesSelect = document.getElementById('emp-roles');
        if (rolesSelect && emp.roles && emp.roles.length > 0) {
            rolesSelect.value = emp.roles[0].id;
        }

        document.getElementById('employeeModal').classList.add('show');
    }

    function editCompany(comp) {
        document.getElementById('compModalTitle').innerHTML = '<i class="fas fa-building" style="color:var(--brand-cyan);"></i> {{ __("messages.edit_company") }}';
        document.getElementById('companyForm').action = "/companies/" + comp.id;
        document.getElementById('comp-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('comp-name').value = comp.name;
        document.getElementById('companyModal').classList.add('show');
    }

    function openResetPasswordModal(id, name) {
        document.getElementById('reset-emp-name').innerText = name;
        document.getElementById('resetPasswordForm').action = "/employees/" + id + "/admin-update-password";
        document.getElementById('resetPasswordForm').reset();
        document.getElementById('resetPasswordModal').classList.add('show');
    }

    function openTargetModal(id, name, amount, comm) {
        document.getElementById('target-emp-name').innerText = name;
        document.getElementById('targetForm').action = "/employees/" + id + "/set-target";
        document.getElementById('target-amount').value = amount;
        document.getElementById('target-comm').value = comm;
        document.getElementById('targetModal').classList.add('show');
    }

    // Close on backdrop click
    document.querySelectorAll('.gm-overlay').forEach(o => {
        o.addEventListener('click', e => { if(e.target === o) closeModal(o.id); });
    });
</script>
