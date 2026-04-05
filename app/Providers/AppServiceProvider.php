<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\Deal;
use App\Models\Task;
use App\Observers\LeadObserver;
use App\Observers\DealObserver;
use App\Observers\TaskObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Override Translator to catch missing translations and format them cleanly
        $this->app->extend('translator', function ($translator, $app) {
            return new class($translator->getLoader(), $translator->getLocale()) extends \Illuminate\Translation\Translator {
                public function get($key, array $replace = [], $locale = null, $fallback = true)
                {
                    $result = parent::get($key, $replace, $locale, $fallback);
                    
                    if (is_string($result) && $result === $key && str_starts_with($key, 'messages.')) {
                        $cleanKey = str_replace('messages.', '', $key);
                        return ucwords(str_replace('_', ' ', $cleanKey));
                    }
                    
                    return $result;
                }
            };
        });
    }

    public function boot(): void
    {
        // Dynamic Plugin Registration
        if (!app()->runningInConsole() && \Illuminate\Support\Facades\Schema::hasTable('plugins')) {
            try {
                $activePlugins = \App\Models\Plugin::where('is_active', true)->get();
                foreach ($activePlugins as $plugin) {
                    if (class_exists($plugin->provider_class)) {
                        $this->app->register($plugin->provider_class);
                    }
                }
            } catch (\Exception $e) {
                // Fail silently to avoid breaking the core
            }
        }

        Paginator::defaultView('vendor.pagination.custom');

        // Activity Monitoring Observers
        Lead::observe(LeadObserver::class);
        Deal::observe(DealObserver::class);
        Task::observe(TaskObserver::class);

        // Dynamic System Branding
        $defaultBranding = [
            'system_name' => config('app.name'),
            'system_logo' => null,
            'system_favicon' => null,
            'system_icon' => 'fas fa-layer-group',
            'system_currency' => 'EGP',
            'system_currency_symbol' => 'ج.م',
        ];

        view()->share('system_branding', $defaultBranding);

        if (!app()->runningInConsole() && \Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            try {
                $settings = \App\Models\SystemSetting::allCached();
                
                if (is_array($settings) || $settings instanceof \Illuminate\Support\Collection) {
                    $branding = [
                        'system_name' => $settings['system_name'] ?? $settings['app_name'] ?? $settings['company_name'] ?? config('app.name'),
                        'system_logo' => $settings['system_logo'] ?? $settings['logo_path'] ?? null,
                        'system_favicon' => $settings['system_favicon'] ?? $settings['favicon_path'] ?? null,
                        'system_slogan' => $settings['system_slogan'] ?? null,
                        'primary_color' => $settings['primary_color'] ?? '#1d4ed8',
                        'accent_color' => $settings['accent_color'] ?? '#0ea5e9',
                        'system_icon' => $settings['system_icon'] ?? 'fas fa-layer-group',
                        'system_currency' => $settings['system_currency'] ?? 'EGP',
                        'system_currency_symbol' => $settings['system_currency_symbol'] ?? 'ج.م',
                    ];

                    view()->share('system_branding', $branding);
                    
                    if (isset($branding['system_name'])) {
                        config(['app.name' => $branding['system_name']]);
                    }
                }
            } catch (\Exception $e) {
                // Keep default branding
            }
        }

        \Illuminate\Support\Facades\Gate::before(function ($user) {
            if (isset($user->is_admin) && $user->is_admin) {
                return true;
            }
        });

        // Dynamic Gate check for permissions
        \Illuminate\Support\Facades\Gate::after(function ($user, $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
            return false;
        });
    }
}
