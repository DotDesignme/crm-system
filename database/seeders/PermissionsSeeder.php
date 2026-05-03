<?php
namespace Database\Seeders;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder {
    public function run(): void {
        $groups = [
            'leads' => ['view-leads', 'create-leads', 'edit-leads', 'delete-leads', 'export-leads'],
            'deals' => ['view-deals', 'create-deals', 'edit-deals', 'delete-deals'],
            'customers' => ['view-customers', 'create-customers', 'edit-customers', 'delete-customers'],
            'companies' => ['view-companies', 'create-companies', 'edit-companies', 'delete-companies'],
            'tasks' => ['view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks'],
            'communications' => ['view-communications', 'create-communications', 'delete-communications'],
            'financial' => ['view-quotations', 'create-quotations', 'delete-quotations', 'view-invoices', 'create-invoices', 'delete-invoices'],
            'contracts' => ['view-contracts', 'create-contracts', 'edit-contracts', 'delete-contracts'],
            'campaigns' => ['view-campaigns', 'create-campaigns', 'edit-campaigns', 'delete-campaigns'],
            'services' => ['view-services', 'manage-services'],
            'employees' => ['view-employees', 'manage-employees', 'manage-roles'],
            'settings' => ['view-settings', 'manage-settings', 'manage-system-branding'],
            'reports' => ['view-reports', 'export-reports'],
            'plugins' => ['manage-plugins']
        ];

        foreach ($groups as $group => $permissions) {
            foreach ($permissions as $slug) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace('-', ' ', $slug)),
                        'group' => $group
                    ]
                );
            }
        }
    }
}
