<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Employee;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'leads' => ['view-any-lead', 'view-own-lead', 'create-lead', 'edit-lead', 'delete-lead'],
            'deals' => ['view-any-deal', 'view-own-deal', 'create-deal', 'edit-deal', 'delete-deal'],
            'companies' => ['view-any-company', 'create-company', 'edit-company', 'delete-company'],
            'services' => ['view-services', 'manage-services'],
            'tasks' => ['view-any-task', 'view-own-task', 'create-task', 'edit-task', 'delete-task'],
            'settings' => ['manage-employees', 'manage-roles', 'view-reports'],
        ];

        foreach ($permissions as $group => $slugs) {
            foreach ($slugs as $slug) {
                Permission::firstOrCreate(['slug' => $slug], [
                    'name' => ucwords(str_replace('-', ' ', $slug)),
                    'group' => $group
                ]);
            }
        }

        // --- Create Roles ---
        
        // Super Admin
        $superAdmin = Role::firstOrCreate(['slug' => 'super-admin'], [
            'name' => 'Super Admin',
            'description' => 'Full access to everything'
        ]);
        $superAdmin->permissions()->sync(Permission::all());

        // Manager
        $manager = Role::firstOrCreate(['slug' => 'manager'], [
            'name' => 'Manager',
            'description' => 'Manage teams, view reports, and handle high-level CRM records'
        ]);
        $managerPermissions = Permission::whereIn('group', ['leads', 'deals', 'companies', 'services', 'tasks'])
            ->orWhere('slug', 'view-reports')
            ->get();
        $manager->permissions()->sync($managerPermissions);

        // Sales Agent
        $agent = Role::firstOrCreate(['slug' => 'agent'], [
            'name' => 'Sales Agent',
            'description' => 'Individual contributor, manages own leads and deals'
        ]);
        $agentPermissions = Permission::whereIn('slug', [
            'view-own-lead', 'create-lead', 'edit-lead',
            'view-own-deal', 'create-deal', 'edit-deal',
            'create-company', 'view-any-company',
            'view-services',
            'view-own-task', 'create-task', 'edit-task'
        ])->get();
        $agent->permissions()->sync($agentPermissions);

        // --- Assign Super Admin to current Admins ---
        $admins = Employee::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->roles()->syncWithoutDetaching([$superAdmin->id]);
        }
        
        // Assign Agent to others (if none assigned)
        $employees = Employee::where('is_admin', false)->get();
        foreach ($employees as $emp) {
            if ($emp->roles()->count() == 0) {
                $emp->roles()->sync([$agent->id]);
            }
        }
    }
}
