# CRM Plugin Development Guide 🚀

Welcome to the modular CRM platform. You can extend this system without modifying core files by following this guide.

## 📁 Directory Structure
All plugins must live in the `/plugins` directory:
```text
plugins/
└── your-plugin-slug/
    ├── src/
    │   └── PluginServiceProvider.php  (Main logic entry point)
    ├── views/
    │   └── widget.blade.php           (UI components)
    ├── routes/
    │   └── web.php                   (Custom plugin routes)
    ├── database/
    │   └── migrations/               (Optional: DB tables)
    └── plugin.json                   (Metadata - REQUIRED)
```

## 📝 The `plugin.json` File
This file is used by the system to identify and register your plugin.
```json
{
    "name": "My Great Plugin",
    "slug": "my-plugin",
    "version": "1.0.0",
    "description": "Explains what this plugin does for the admin.",
    "provider_class": "Plugins\\MyPlugin\\src\\PluginServiceProvider"
}
```

## 🔗 UI Hooks (Stacks)
We provide specific "Hooks" in the core layout. You can "Push" content into these stacks from your ServiceProvider or View.

| Hook Name | Location | Usage |
|-----------|----------|-------|
| `@stack('plugin-global-notice')` | Top of every page | Global banners, emergency alerts. |
| `@stack('plugin-scripts')` | Bottom of `<body>` | Non-blocking JS scripts. |
| `@stack('plugin-lead-actions')` | Lead Profile Sidebar | Adding action buttons (WhatsApp, Email, etc). |

### Example Hook Usage (in ServiceProvider):
```php
View::composer('layouts.app', function ($view) {
    View::startPush('plugin-global-notice', view('my-plugin::banner'));
});
```

## 🔌 Best Practices
1. **Namespace**: Always use `Plugins\{YourSlug}\src` for your code.
2. **Migrations**: Use `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')` to keep your tables modular.
3. **Asset Handling**: Store CSS/JS in `views` or serve them via dynamic routes to avoid manual `public/` symlinking.
4. **Safety**: Always wrap DB calls or experimental logic in `try-catch` inside the ServiceProvider to prevent a broken plugin from crashing the entire CRM.

---
*Created by Antigravity AI for a modular future.*
