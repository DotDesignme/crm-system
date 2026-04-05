<?php

namespace Plugins\WhatsAppPro\src;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load Views
        if (is_dir(__DIR__ . '/../views')) {
            $this->loadViewsFrom(__DIR__ . '/../views', 'whatsapp-pro');
        }

        // Inject UI Hook: WhatsApp Button in Lead Details
        View::composer('leads.show', function ($view) {
            $lead = $view->getData()['lead'] ?? null;
            if ($lead && $lead->phone) {
                \Illuminate\Support\Facades\View::startPush('plugin-lead-actions', view('whatsapp-pro::lead_button', ['lead' => $lead])->render());
            }
        });

        // Inject UI Hook: WhatsApp Button in Customer Details
        View::composer('customers.show', function ($view) {
            $customer = $view->getData()['customer'] ?? null;
            if ($customer && $customer->phone) {
                \Illuminate\Support\Facades\View::startPush('plugin-lead-actions', view('whatsapp-pro::customer_button', ['customer' => $customer])->render());
            }
        });
    }
}
