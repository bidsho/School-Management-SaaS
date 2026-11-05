<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Platform management
            'manage schools',
            'manage subscription plans',
            'manage subscriptions',
            'view platform reports',
            'manage platform settings',

            // School configuration
            'manage school settings',
            'manage school users',
            'manage academic sessions',
            'manage classes',
            'manage subjects',

            // People
            'view students',
            'manage students',
            'view parents',
            'manage parents',
            'view teachers',
            'manage teachers',
            'manage admissions',

            // Academic operations
            'view attendance',
            'mark attendance',
            'manage attendance',
            'view assignments',
            'manage assignments',
            'view timetable',
            'manage timetable',
            'view exams',
            'manage exams',
            'enter exam scores',
            'approve results',
            'view results',

            // Finance
            'view fees',
            'manage fee structures',
            'manage invoices',
            'record payments',
            'view financial reports',

            // Communications and administration
            'view announcements',
            'manage announcements',
            'view audit logs',
        ];

        // Create all permissions.
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | School-Level Permissions
        |--------------------------------------------------------------------------
        |
        | Principal and School Admin receive the same school permissions.
        | Platform permissions remain exclusive to Super Admin.
        |
        */

        $platformPermissions = [
            'manage schools',
            'manage subscription plans',
            'manage subscriptions',
            'view platform reports',
            'manage platform settings',
        ];

        $schoolPermissions = array_values(
            array_diff($permissions, $platformPermissions)
        );

        $rolePermissions = [

            // Super Admin
            'Super Admin' => $platformPermissions,

            // School Admin
            'School Admin' => $schoolPermissions,

            // Principal gets all school-level permissions.
            'Principal' => $schoolPermissions,

            // Teacher
            'Teacher' => [
                'view students',
                'view attendance',
                'mark attendance',
                'view assignments',
                'manage assignments',
                'view timetable',
                'view exams',
                'enter exam scores',
                'view results',
                'view announcements',
            ],

            // Accountant
            'Accountant' => [
                'view students',
                'view fees',
                'manage fee structures',
                'manage invoices',
                'record payments',
                'view financial reports',
            ],

            // Receptionist
            'Receptionist' => [
                'view students',
                'manage students',
                'view parents',
                'manage parents',
                'manage admissions',
                'view announcements',
            ],

            // Student
            'Student' => [
                'view assignments',
                'view timetable',
                'view results',
                'view announcements',
            ],

            // Parent
            'Parent' => [
                'view attendance',
                'view assignments',
                'view timetable',
                'view results',
                'view fees',
                'view announcements',
            ],
        ];

        // Create roles and synchronize their permissions.
        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($permissionNames);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
