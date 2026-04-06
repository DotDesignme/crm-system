<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign keys temporarily if it's SQLite for the update
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        Schema::table('employees', function (Blueprint $table) {
            // 1. Ensure the column is nullable
            $table->unsignedBigInteger('company_id')->nullable()->change();
            
            // 2. Drop the old foreign key constraint if possible
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    $table->dropForeign(['company_id']);
                    $table->dropForeign(['page_id']);
                } catch (\Exception $e) {}
            }
        });

        // 3. Re-add the foreign key with SET NULL
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['company_id']);
            }
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }
};
