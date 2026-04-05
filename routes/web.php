<?php

use App\Http\Controllers\AdminExecutiveController;

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTemplateController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.show');

    // Core CRM
    Route::get('/leads/kanban', [LeadController::class, 'kanban'])->name('leads.kanban')->middleware('permission:view-leads');
    Route::post('/leads/update-status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus')->middleware('permission:edit-leads');
    Route::resource('leads', LeadController::class)->middleware('permission:view-leads');
    
    // Customers (formerly Companies)
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/contacts', [CustomerContactController::class, 'store'])->name('customers.contacts.store');
    Route::delete('/contacts/{contact}', [CustomerContactController::class, 'destroy'])->name('contacts.destroy');

    Route::resource('deals', DealController::class)->middleware('permission:view-deals');
    Route::post('/deals/{deal}/move', [DealController::class, 'move'])->name('deals.move')->middleware('permission:edit-deals');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Communications & Notes
    Route::post('/communications', [CommunicationController::class, 'store'])->name('communications.store');
    Route::post('/communications/whatsapp/log', [CommunicationController::class, 'logWhatsAppStore'])->name('communications.whatsapp.log');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Services (formerly Products)
    Route::resource('services', ServiceController::class)->except(['create','show','edit'])->middleware('permission:view-services');

    // Quotations
    Route::resource('quotations', QuotationController::class)->except(['edit','update'])->middleware('permission:view-quotations');
    Route::get('quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download')->middleware('permission:view-quotations');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert')->middleware('permission:create-invoices');

    // Invoices
    Route::resource('invoices', InvoiceController::class)->except(['edit','update'])->middleware('permission:view-invoices');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download')->middleware('permission:view-invoices');
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment')->middleware('permission:create-invoices');

    // Contracts
    Route::resource('contracts', ContractController::class)->except(['edit']);

    // Campaigns
    Route::resource('campaigns', CampaignController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Team Performance
    Route::get('/team', [TeamController::class, 'performance'])->name('team.performance');

    // Settings
    Route::get('/settings', [EmployeeProfileController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [EmployeeProfileController::class, 'update'])->name('settings.profile');

    // Export
    Route::get('/export/leads', [ExportController::class, 'leads'])->name('export.leads');
    Route::get('/export/campaigns', [ExportController::class, 'campaigns'])->name('export.campaigns');

    // Admin only
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('companies', CompanyController::class)->except(['create']);
        Route::get('companies/{company}/activity-log', [CompanyController::class, 'activityLog'])->name('companies.activity-log');
        Route::resource('employees', EmployeeController::class)->except(['create', 'show']);
        Route::put('employees/{employee}/admin-update-password', [EmployeeController::class, 'adminUpdatePassword'])->name('employees.admin-update-password');
        Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::post('employees/{employee}/set-target', [EmployeeController::class, 'setTarget'])->name('employees.set-target');
        Route::get('employees/{employee}/activity-log', [EmployeeController::class, 'activityLog'])->name('employees.activity-log');
        Route::resource('roles', RoleController::class);
        
        // Task Templates
        Route::resource('task-templates', TaskTemplateController::class);

        // System Settings
        Route::get('/settings/branding', [SystemSettingsController::class, 'index'])->name('settings.branding');
        Route::post('/settings/branding/update', [SystemSettingsController::class, 'updateBranding'])->name('settings.branding.update');
        Route::post('/settings/company/update', [SystemSettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::post('/settings/financials/update', [SystemSettingsController::class, 'updateFinancials'])->name('settings.financials.update');
        Route::post('/settings/health-score/update', [SystemSettingsController::class, 'updateHealthScore'])->name('settings.health-score.update');

        // Pipeline Stages
        Route::post('/settings/stages', [SystemSettingsController::class, 'storeDealStage'])->name('settings.stages.store');
        Route::put('/settings/stages/{stage}', [SystemSettingsController::class, 'updateDealStage'])->name('settings.stages.update');
        Route::delete('/settings/stages/{stage}', [SystemSettingsController::class, 'destroyDealStage'])->name('settings.stages.destroy');
        Route::post('/settings/stages/reorder', [SystemSettingsController::class, 'reorderDealStages'])->name('settings.stages.reorder');

        // Loss Reasons
        Route::post('/settings/reasons', [SystemSettingsController::class, 'storeLossReason'])->name('settings.reasons.store');
        Route::put('/settings/reasons/{reason}', [SystemSettingsController::class, 'updateLossReason'])->name('settings.reasons.update');
        Route::delete('/settings/reasons/{reason}', [SystemSettingsController::class, 'destroyLossReason'])->name('settings.reasons.destroy');
        Route::post('/settings/reasons/reorder', [SystemSettingsController::class, 'reorderLossReasons'])->name('settings.reasons.reorder');
        
        // Workflow / Duplicate Rules
        Route::post('/settings/workflow/update', [SystemSettingsController::class, 'updateWorkflow'])->name('settings.workflow.update');

        // Executive Dashboard
        Route::get('/executive-dashboard', [AdminExecutiveController::class, 'index'])->name('admin.executive');

        // Plugin Management
        Route::get('/settings/plugins', [\App\Http\Controllers\PluginController::class, 'index'])->name('settings.plugins');
        Route::post('/settings/plugins/toggle/{plugin}', [\App\Http\Controllers\PluginController::class, 'toggle'])->name('settings.plugins.toggle');
        Route::post('/settings/plugins/upload', [\App\Http\Controllers\PluginController::class, 'upload'])->name('settings.plugins.upload');
        Route::delete('/settings/plugins/delete/{plugin}', [\App\Http\Controllers\PluginController::class, 'delete'])->name('settings.plugins.delete');
    });

    // Employee Profile & Command Center (Accessible to ALL authenticated employees)
    Route::get('/profile', [EmployeeProfileController::class, 'index'])->name('employees.profile');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('employees.profile.update');
    Route::post('/profile/status', [EmployeeProfileController::class, 'updateStatus'])->name('employees.profile.status');
    Route::post('/profile/signatures', [EmployeeProfileController::class, 'updateSignatures'])->name('employees.profile.signatures');
    Route::post('/profile/preferences', [EmployeeProfileController::class, 'updatePreferences'])->name('employees.profile.preferences');
    
    // Apply template to lead
    Route::get('leads/{lead}/task-templates/{template}/preview', [TaskTemplateController::class, 'preview'])->name('leads.apply-template.preview');
    Route::post('leads/{lead}/apply-template', [TaskTemplateController::class, 'apply'])->name('leads.apply-template');

    // Attachments
    Route::post('attachments', [\App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [\App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');
});
