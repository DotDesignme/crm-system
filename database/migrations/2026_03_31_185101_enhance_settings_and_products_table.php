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
        // 1. Create Loss Reasons Table
        if (!Schema::hasTable('loss_reasons')) {
            Schema::create('loss_reasons', function (Blueprint $table) {
                $table->id();
                $table->string('reason');
                $table->boolean('is_active')->default(true);
                $table->foreignId('page_id')->nullable()->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 2. Enhance Deals Table
        if (!Schema::hasColumn('deals', 'loss_reason_id')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->foreignId('loss_reason_id')->nullable()->constrained('loss_reasons')->nullOnDelete();
            });
        }

        // 3. Enhance Products (Services) Table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'thickness')) {
                $table->string('thickness')->nullable();
            }
            if (!Schema::hasColumn('products', 'material_type')) {
                $table->string('material_type')->nullable(); // Epoxy, Ucrete, etc.
            }
            if (!Schema::hasColumn('products', 'application_method')) {
                $table->string('application_method')->nullable();
            }
            if (!Schema::hasColumn('products', 'unit_type')) {
                $table->string('unit_type')->default('piece'); // m2, lm, piece
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['thickness', 'material_type', 'application_method', 'unit_type']);
        });

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'loss_reason_id')) {
                $table->dropForeign(['loss_reason_id']);
                $table->dropColumn('loss_reason_id');
            }
        });

        Schema::dropIfExists('loss_reasons');
    }
};
