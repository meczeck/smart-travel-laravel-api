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
                    "update-profile",
                    "change-profile-image",
                    "deactivate-account"
                ]
            ],
            [
                "roleName" => "company-agents-management",
                "permissions" => [
                    "create-company-agent",
                    "update-company-agent",
                    "show-company-agent",
                    "get-company-agents",
                ]
            ],
            
            [
                "roleName" => "roles-management",
                "permissions" => [
                    "create-role",
                    "update-role",
                    "show-role",
                    "get-roles",
                ]
            ],



            [
                "roleName" => "super-admin",
                "permissions" => [

                ]
            ],

            [
                "roleName" => "company-registrars",
                "permissions" => [
                ]
            ],

            [
                "roleName" => "company-admin",
                "permissions" => [
                    "terms-and-conditions-access",
                ]
            ],
            [
                "roleName" => "bus-company-agent",
                "permissions" => [

                ]
            ],
            [
                "roleName" => "main-customer",
                "permissions" => [

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
                "roleName" => "company-verification",
                "permissions" => [
                    "verify-company-registration",
                    "unverify-company-registration",
                ]
            ],



        ];

        foreach ($rolesAndPermissions as $roleAndPermission) {
            $role = Role::create(['guard_name' => 'sanctum', 'name' => $roleAndPermission["roleName"]]);
            // $role->syncPermissions('guard_name' => 'sanctum', $roleAndPermission['permissions']);
            foreach ($roleAndPermission["permissions"] as $permission) {
                $createdPermission = Permission::create(['guard_name' => 'sanctum', 'name' => $permission]);
                $role->givePermissionTo($createdPermission);
            }

        }
    }
}