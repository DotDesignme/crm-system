<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\LossReason;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'app_name' => 'Floor-in CRM',
            'company_name' => 'Floor-in Industrial Flooring',
            'company_address' => 'Cairo, Egypt',
            'company_phone' => '+20 123 456 789',
            'company_email' => 'info@floor-in.com',
            'company_tax_id' => '123-456-789',
            'company_cr_number' => '987654',
            'system_currency' => 'EGP',
            'system_currency_symbol' => 'ج.م',
            'system_vat_percentage' => '14',
            'system_wht_percentage' => '1',
            'theme_primary_color' => '#001f3f', // Dark Navy
            'theme_mode' => 'dark',
        ];

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        // Default Loss Reasons and Deal Stages per Page
        $pages = \App\Models\Page::all();
        
        foreach ($pages as $page) {
            $lossReasons = [
                ['reason' => 'سعر مرتفع (High Price)', 'is_active' => true, 'page_id' => $page->id],
                ['reason' => 'اختيار منافس (Competitor Chosen)', 'is_active' => true, 'page_id' => $page->id],
                ['reason' => 'عدم ملاءمة فنية (Technical Unsuitability)', 'is_active' => true, 'page_id' => $page->id],
                ['reason' => 'إلغاء المشروع (Project Canceled)', 'is_active' => true, 'page_id' => $page->id],
                ['reason' => 'عدم الرد (No Response)', 'is_active' => true, 'page_id' => $page->id],
            ];

            foreach ($lossReasons as $lr) {
                LossReason::updateOrCreate(['reason' => $lr['reason'], 'page_id' => $page->id], $lr);
            }

            $dealStages = [
                ['name' => 'ليد جديد (New Lead)', 'color' => '#3b82f6', 'order' => 1, 'is_won' => false, 'is_lost' => false, 'page_id' => $page->id],
                ['name' => 'معاينة الموقع (Site Survey)', 'color' => '#8b5cf6', 'order' => 2, 'is_won' => false, 'is_lost' => false, 'page_id' => $page->id],
                ['name' => 'عرض السعر (Quotation)', 'color' => '#f59e0b', 'order' => 3, 'is_won' => false, 'is_lost' => false, 'page_id' => $page->id],
                ['name' => 'تفاوض (Negotiation)', 'color' => '#0ea5e9', 'order' => 4, 'is_won' => false, 'is_lost' => false, 'page_id' => $page->id],
                ['name' => 'تم التعاقد (Won)', 'color' => '#10b981', 'order' => 5, 'is_won' => true, 'is_lost' => false, 'page_id' => $page->id],
                ['name' => 'خسارة (Lost)', 'color' => '#ef4444', 'order' => 6, 'is_won' => false, 'is_lost' => true, 'page_id' => $page->id],
            ];

            foreach ($dealStages as $ds) {
                \App\Models\DealStage::updateOrCreate(['name' => $ds['name'], 'page_id' => $page->id], $ds);
            }
        }
    }
}
