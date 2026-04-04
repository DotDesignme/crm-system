<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->string('job_title')->nullable()->after('avatar');
            $table->string('phone_number')->nullable()->after('job_title');
            $table->enum('status', ['available', 'site_visit', 'meeting', 'on_leave'])->default('available')->after('phone_number');
            $table->text('email_signature')->nullable();
            $table->text('quote_signature')->nullable();
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->json('working_hours')->nullable();
            $table->json('notification_preferences')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'job_title', 'phone_number', 'status', 
                'email_signature', 'quote_signature', 'is_active', 
                'working_hours', 'notification_preferences'
            ]);
        });
    }
};
