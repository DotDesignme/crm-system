<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id')->nullable()->after('page_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->decimal('total_spend', 15, 2)->default(0)->after('budget');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('total_spend');
        });
    }
};
