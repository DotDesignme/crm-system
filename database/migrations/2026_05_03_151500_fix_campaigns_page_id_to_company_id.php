<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'page_id') && !Schema::hasColumn('campaigns', 'company_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->renameColumn('page_id', 'company_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'company_id') && !Schema::hasColumn('campaigns', 'page_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->renameColumn('company_id', 'page_id');
            });
        }
    }
};
