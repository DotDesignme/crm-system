<?php

namespace Plugins\HelloWorld\src;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class HelloServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Load plugin routes
        if (file_exists(base_path('plugins/hello-world/routes/web.php'))) {
            Route::middleware('web')
                ->group(base_path('plugins/hello-world/routes/web.php'));
        }

        // Load plugin views
        if (is_dir(base_path('plugins/hello-world/views'))) {
            $this->loadViewsFrom(base_path('plugins/hello-world/views'), 'hello-world');
        }
    }
}
