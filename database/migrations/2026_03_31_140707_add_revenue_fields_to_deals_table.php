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
        Schema::table('deals', function (Blueprint $table) {
            $table->string('win_reason')->after('loss_notes')->nullable();
            $table->text('win_notes')->after('win_reason')->nullable();
            $table->date('actual_close_date')->after('expected_close_date')->nullable();
            $table->decimal('weighted_value', 15, 2)->after('value')->storedAs('value * (probability / 100.0)');
            $table->string('deal_type')->after('title')->default('new_business'); // new_business, expansion, renewal
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['win_reason', 'win_notes', 'actual_close_date', 'weighted_value', 'deal_type']);
        });
    }
};
