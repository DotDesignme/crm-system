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
        // Re-use PermissionsSeeder logic to ensure all permissions exist
        $this->call(PermissionsSeeder::class);

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
        $managerPermissions = Permission::whereIn('group', ['leads', 'deals', 'customers', 'companies', 'tasks', 'services', 'reports'])->get();
        $manager->permissions()->sync($managerPermissions);

        // Sales Agent
        $agent = Role::firstOrCreate(['slug' => 'agent'], [
            'name' => 'Sales Agent',
            'description' => 'Individual contributor, manages own leads and deals'
        ]);
        $agentPermissions = Permission::whereIn('slug', [
            'view-leads', 'create-leads', 'edit-leads',
            'view-deals', 'create-deals', 'edit-deals',
            'create-customers', 'view-customers',
            'view-services',
            'view-tasks', 'create-tasks', 'edit-tasks'
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
