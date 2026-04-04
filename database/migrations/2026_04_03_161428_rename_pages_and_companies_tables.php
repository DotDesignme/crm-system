<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
        // 1. Rename tables safely
        if (Schema::hasTable('companies') && !Schema::hasTable('customers')) {
            Schema::rename('companies', 'customers');
        }
        if (Schema::hasTable('pages') && !Schema::hasTable('companies')) {
            Schema::rename('pages', 'companies');
        }

        // 3. Update columns in other tables
        $tablesToRefactor = [
            'leads' => ['business' => 'company_id', 'internal' => 'page_id'],
            'employees' => ['internal' => 'page_id'],
            'deals' => ['business' => 'company_id', 'internal' => 'page_id'],
            'invoices' => ['business' => 'company_id', 'internal' => 'page_id'],
            'quotations' => ['business' => 'company_id', 'internal' => 'page_id'],
            'contracts' => ['business' => 'company_id', 'internal' => 'page_id'],
            'customers' => ['internal' => 'page_id'], // In customers (old companies), page_id should become company_id
        ];

        foreach ($tablesToRefactor as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table, $columns) {
                    // First, rename business client column to customer_id if it exists
                    if (isset($columns['business']) && Schema::hasColumn($table, $columns['business'])) {
                        $t->renameColumn($columns['business'], 'customer_id');
                    }
                    // Then, rename internal branch column to company_id if it exists
                    if (isset($columns['internal']) && Schema::hasColumn($table, $columns['internal'])) {
                        $t->renameColumn($columns['internal'], 'company_id');
                    }
                });
            }
        }

        if (Schema::hasTable('company_contacts')) {
             if (Schema::hasColumn('company_contacts', 'company_id')) {
                Schema::table('company_contacts', function (Blueprint $table) {
                    $table->renameColumn('company_id', 'customer_id');
                });
             }
             Schema::rename('company_contacts', 'customer_contacts');
        }
        
        // 4. Create pivot table for many-to-many relationship
        if (!Schema::hasTable('company_employee')) {
            Schema::create('company_employee', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_employee');
        
        if (Schema::hasTable('customer_contacts')) {
             Schema::rename('customer_contacts', 'company_contacts');
             Schema::table('company_contacts', function (Blueprint $table) {
                $table->renameColumn('customer_id', 'company_id');
            });
        }
        
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'company_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->renameColumn('company_id', 'page_id');
            });
        }
        
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'company_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->renameColumn('company_id', 'page_id');
            });
        }

        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'company_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->renameColumn('company_id', 'page_id');
            });
        }

        if (Schema::hasTable('companies') && !Schema::hasTable('pages')) {
            Schema::rename('companies', 'pages');
        }
        if (Schema::hasTable('customers') && !Schema::hasTable('companies')) {
            Schema::rename('customers', 'companies');
        }
    }
};
