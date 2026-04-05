<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF');
        }

        $tables = ['notes', 'activities', 'communications', 'tasks', 'attachments', 'deal_stages', 'products', 'automations', 'territories', 'commissions'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'page_id') && !Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->renameColumn('page_id', 'company_id');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['notes', 'activities', 'communications', 'tasks', 'attachments', 'deal_stages', 'products', 'automations', 'territories', 'commissions'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id') && !Schema::hasColumn($table, 'page_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->renameColumn('company_id', 'page_id');
                });
            }
        }
    }
};
