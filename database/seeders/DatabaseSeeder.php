<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SystemSettingsSeeder::class);
        // إنشاء صفحات فيسبوك
        $p1 = Page::updateOrCreate(['name' => 'صفحة فيسبوك 1'], ['url' => 'https://facebook.com/page1']);
        $p2 = Page::updateOrCreate(['name' => 'صفحة فيسبوك 2'], ['url' => 'https://facebook.com/page2']);
        $p3 = Page::updateOrCreate(['name' => 'صفحة فيسبوك 3'], ['url' => 'https://facebook.com/page3']);

        // إنشاء مراحل الصفقات
        $stages = [
            ['name' => 'بروسبكت (Prospecting)', 'color' => '#6366f1', 'order' => 10, 'page_id' => $p1->id],
            ['name' => 'تأهيل (Qualification)', 'color' => '#8b5cf6', 'order' => 20, 'page_id' => $p1->id],
            ['name' => 'مقترح (Proposal)', 'color' => '#ec4899', 'order' => 30, 'page_id' => $p1->id],
            ['name' => 'مفاوضات (Negotiation)', 'color' => '#f59e0b', 'order' => 40, 'page_id' => $p1->id],
            ['name' => 'فوز (Closed Won)', 'color' => '#10b981', 'order' => 50, 'page_id' => $p1->id],
            ['name' => 'خسارة (Closed Lost)', 'color' => '#ef4444', 'order' => 60, 'page_id' => $p1->id],
        ];

        foreach ($stages as $stage) {
            \App\Models\DealStage::updateOrCreate(['name' => $stage['name']], $stage);
        }

        // إنشاء أدمن
        Employee::updateOrCreate(['username' => 'admin'], [
            'name' => 'أدمن',
            'password' => 'admin123',
            'page_id' => $p1->id,
            'is_admin' => true,
        ]);

        // إنشاء موظفين
        Employee::updateOrCreate(['username' => 'emp1'], [
            'name' => 'موظف صفحة 1',
            'password' => '123456',
            'page_id' => $p1->id,
            'is_admin' => false,
        ]);

        Employee::updateOrCreate(['username' => 'emp2'], [
            'name' => 'موظف صفحة 2',
            'password' => '123456',
            'page_id' => $p2->id,
            'is_admin' => false,
        ]);

        Employee::updateOrCreate(['username' => 'emp3'], [
            'name' => 'موظف صفحة 3',
            'password' => '123456',
            'page_id' => $p3->id,
            'is_admin' => false,
        ]);
    }
}
