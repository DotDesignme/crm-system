<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== COMPANIES / ACCOUNTS =====
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Egypt');
            $table->string('logo_path')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('status')->default('active');
            $table->string('health_score')->default('warm');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== COMPANY CONTACTS =====
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('role')->default('contact');
            $table->boolean('is_decision_maker')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== DEAL STAGES =====
        Schema::create('deal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#6366f1');
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->timestamps();
        });

        // ===== DEALS =====
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->date('expected_close_date')->nullable();
            $table->unsignedInteger('probability')->default(50);
            $table->text('description')->nullable();
            $table->string('loss_reason')->nullable();
            $table->text('loss_notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });

        // ===== TASKS =====
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('follow_up');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');
            $table->foreignId('assigned_to')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();
            $table->nullableMorphs('taskable');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== ACTIVITIES TIMELINE =====
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->nullableMorphs('activitiable');
            $table->json('metadata')->nullable();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== NOTES =====
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('noteable');
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== FILES / ATTACHMENTS =====
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('attachable');
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== PRODUCTS / SERVICES =====
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->string('unit')->default('piece');
            $table->boolean('is_service')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== PRODUCT PRICING TIERS =====
        Schema::create('product_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('tier_name');
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('min_quantity')->default(1);
            $table->timestamps();
        });

        // ===== QUOTATIONS =====
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('company_contacts')->nullOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });

        // ===== QUOTATION ITEMS =====
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        // ===== INVOICES =====
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('company_contacts')->nullOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== INVOICE ITEMS =====
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });

        // ===== CONTRACTS =====
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->unsignedInteger('renewal_period_months')->nullable();
            $table->date('renewal_date')->nullable();
            $table->string('file_path')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== COMMUNICATIONS LOG =====
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('direction')->default('outbound');
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->string('channel')->nullable();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('communicable');
            $table->timestamp('communicated_at')->nullable();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== WORKFLOW AUTOMATIONS =====
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_type');
            $table->json('trigger_conditions')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== TERRITORIES =====
        Schema::create('territories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region')->nullable();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== TERRITORY EMPLOYEES =====
        Schema::create('territory_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('territory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== COMMISSIONS =====
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('deal_value', 15, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->string('status')->default('pending');
            $table->date('paid_at')->nullable();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ===== LEAD SCORES =====
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedInteger('score')->default(0)->after('priority');
            $table->foreignId('company_id')->nullable()->after('score')->constrained()->nullOnDelete();
        });

        // Add indexes
        Schema::table('deals', function (Blueprint $table) {
            $table->index('status');
            $table->index('deal_stage_id');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('territory_employee');
        Schema::dropIfExists('territories');
        Schema::dropIfExists('automations');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('product_pricings');
        Schema::dropIfExists('products');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('deal_stages');
        Schema::dropIfExists('company_contacts');
        Schema::dropIfExists('companies');
    }
};
