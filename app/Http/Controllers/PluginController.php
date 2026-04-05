<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class PluginController extends Controller
{
    public function index()
    {
        $plugins = Plugin::all();
        $activeTab = 'plugins';
        
        // Load general branding settings for the layout
        $settings = \App\Models\SystemSetting::allCached();
        
        return view('settings.branding', compact('plugins', 'activeTab', 'settings'));
    }

    public function toggle(Plugin $plugin)
    {
        $plugin->is_active = !$plugin->is_active;
        $plugin->save();

        return back()->with('success', $plugin->is_active ? "Plugin '{$plugin->name}' activated." : "Plugin '{$plugin->name}' deactivated.");
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip|max:10240',
        ]);

        $zipFile = $request->file('plugin_zip');
        $zip = new ZipArchive;

        if ($zip->open($zipFile->getPathname()) === TRUE) {
            $tempPath = storage_path('app/temp_plugin_' . Str::random(10));
            $zip->extractTo($tempPath);
            $zip->close();

            // Smart Search for plugin.json (handles nested folders in Zip)
            $allFiles = File::allFiles($tempPath);
            $jsonFile = collect($allFiles)->first(fn($file) => $file->getFilename() === 'plugin.json');

            if (!$jsonFile) {
                File::deleteDirectory($tempPath);
                return back()->with('error', 'Invalid plugin: missing plugin.json');
            }

            $currentPath = $jsonFile->getPath();
            $metadata = json_decode(File::get($jsonFile->getPathname()), true);
            $slug = $metadata['slug'] ?? Str::slug($metadata['name']);
            $pluginPath = base_path('plugins/' . $slug);

            if (File::exists($pluginPath)) {
                File::deleteDirectory($tempPath);
                return back()->with('error', 'Plugin already exists.');
            }

            // Move only the content of the plugin directory
            File::moveDirectory($currentPath, $pluginPath);
            File::deleteDirectory($tempPath);

            // Register in DB
            Plugin::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $metadata['name'],
                    'version' => $metadata['version'] ?? '1.0.0',
                    'description' => $metadata['description'] ?? '',
                    'provider_class' => $metadata['provider_class'],
                    'is_active' => false
                ]
            );

            return back()->with('success', 'Plugin uploaded successfully. You can now activate it.');
        }

        return back()->with('error', 'Could not open ZIP file.');
    }

    public function delete(Plugin $plugin)
    {
        $pluginPath = base_path('plugins/' . $plugin->slug);
        if (File::exists($pluginPath)) {
            File::deleteDirectory($pluginPath);
        }

        $plugin->delete();

        return back()->with('success', 'Plugin deleted.');
    }
}
