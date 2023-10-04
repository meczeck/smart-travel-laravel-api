<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesAndPermissions = [
            [
                "roleName" => "user-management",
                "permissions" => [
                    "create-user",
                    "edit-user",
                    "delete-user",
                    "activate-user",
                    "deactivate-user"
                ]
            ],
            [
                "roleName" => "post-management",
                "permissions" => [
                    "create-post",
                    "delete-post",
                    "update-post",
                    "share-post"
                ]
            ],
            [
                "roleName" => "profile-management",
                "permissions" => [
                    "change-password",
                    "change-email",
                    "change-profile-image",
                    "deactivate-account"
                ]
            ],



            [
                "roleName" => "super-admin",
                "permissions" => [
                    "browser-access",
                ]
            ],

            [
                "roleName" => "dashboard-user",
                "permissions" => [
                    "dashboard-access",
                ]
            ],

            [
                "roleName" => "mobile-user",
                "permissions" => [
                    "mobile-access",
                ]
            ],

            [
                "roleName" => "company-admin",
                "permissions" => [
                    "terms-and-conditions-access",
                ]
            ],
        ];

        foreach ($rolesAndPermissions as $roleAndPermission) {
            $role = Role::create(['name' => $roleAndPermission["roleName"]]);
            // $role->syncPermissions($roleAndPermission["permissions"]);

            foreach ($roleAndPermission["permissions"] as $permission) {
                $createdPermission = Permission::create(['name' => $permission]);
                $role->givePermissionTo($createdPermission);
            }

        }
    }
}