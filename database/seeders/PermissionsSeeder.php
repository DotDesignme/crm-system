<?php
namespace Database\Seeders;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder {
    public function run(): void {
        $groups = [
            'leads' => ['view-leads', 'create-leads', 'edit-leads', 'delete-leads', 'export-leads'],
            'deals' => ['view-deals', 'create-deals', 'edit-deals', 'delete-deals'],
            'tasks' => ['view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks'],
            'communications' => ['view-communications', 'create-communications'],
            'financial' => ['view-quotations', 'create-quotations', 'view-invoices', 'create-invoices'],
            'employees' => ['view-employees', 'manage-employees', 'manage-roles'],
            'settings' => ['view-settings', 'manage-settings']
        ];

        foreach ($groups as $group => $permissions) {
            foreach ($permissions as $slug) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace(['-', 'view', 'manage'], [' ', 'عرض', 'إدارة'], $slug)),
                        'group' => $group
                    ]
                );
            }
        }
    }
}
