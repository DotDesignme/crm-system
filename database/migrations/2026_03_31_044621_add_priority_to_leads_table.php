<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('status');
            $table->string('source')->nullable()->after('priority');
            $table->timestamp('follow_up_at')->nullable()->after('source');
            $table->string('tag')->nullable()->after('follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['priority', 'source', 'follow_up_at', 'tag']);
        });
    }
};
