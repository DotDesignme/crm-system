<?php

namespace Plugins\Announcements\src;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AnnouncementServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load migrations from plugin directory
        if (is_dir(__DIR__ . '/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        // Load Views
        if (is_dir(__DIR__ . '/../views')) {
            $this->loadViewsFrom(__DIR__ . '/../views', 'announcements');
        }

        // Load Routes
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            Route::middleware('web')->group(__DIR__ . '/../routes/web.php');
        }

        // Inject UI Hook: Global Announcement Bar
        if (Schema::hasTable('announcements')) {
            try {
                $activeAnnouncement = \Illuminate\Support\Facades\DB::table('announcements')
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($activeAnnouncement) {
                    View::composer('layouts.app', function ($view) use ($activeAnnouncement) {
                        // Push to the stack
                        View::startPush('plugin-global-notice', view('announcements::banner', ['announcement' => $activeAnnouncement]));
                    });
                }
            } catch (\Exception $e) {
                // Silently fail if DB not ready
            }
        }
    }
}
