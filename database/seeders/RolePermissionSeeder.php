<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        try {

            $superAdminRole = Role::create(['name' => 'superadmin', 'guard_name' => 'api']);

            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin@1234'),
                'email_verified_at' => now()
            ]);

            $user->assignRole($superAdminRole->name);

            $dashboardModule = Module::create([
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'icon_class' => 'mdi-view-dashboard',
                'weight' => 1,
                'route' => '/admin/dashboard'
            ]);

            $authModule = Module::create([
                'name' => 'authorization',
                'display_name' => 'Authorization',
                'icon_class' => 'mdi-shield-lock',
                'weight' => 12
            ]);

            $userModule = Module::create([
                'parent_id' => $authModule->id,
                'name' => 'users',
                'display_name' => 'Users',
                'icon_class' => 'mdi-account-multiple',
                'weight' => 1,
                'route' => '/admin/users'
            ]);

            $roleModule = Module::create([
                'parent_id' => $authModule->id,
                'name' => 'roles',
                'display_name' => 'Roles',
                'icon_class' => 'mdi-account-hard-hat',
                'weight' => 2,
                'route' => '/admin/roles'
            ]);

            $permissionModule = Module::create([
                'parent_id' => $authModule->id,
                'name' => 'permissions',
                'display_name' => 'Permissions',
                'icon_class' => 'mdi-account-key',
                'weight' => 3,
                'route' => '/admin/permissions'
            ]);

            $productModule = Module::create([
                'name' => 'products',
                'display_name' => 'Products',
                'icon_class' => 'mdi-products',
                'weight' => 2
            ]);

            $orderModule = Module::create([
                'name' => 'orders',
                'display_name' => 'Orders',
                'icon_class' => 'mdi-orders',
                'weight' => 3
            ]);

            $paymentModule = Module::create([
                'name' => 'payments',
                'display_name' => 'Payments',
                'icon_class' => 'mdi-payments',
                'weight' => 4
            ]);

            $permissionsArr = [
                [
                    'module_id' => $dashboardModule->id,
                    'permissions' => [
                        'read dashboard',
                    ]
                ],
                [
                    'module_id' => $userModule->id,
                    'permissions' => [
                        'create users',
                        'read users',
                        'update users',
                        'delete users',
                        'assign users role',
                        'revoke users role',
                    ]
                ],
                [
                    'module_id' => $roleModule->id,
                    'permissions' => [
                        'create roles',
                        'read roles',
                        'update roles',
                        'delete roles',
                    ]
                ],
                [
                    'module_id' => $permissionModule->id,
                    'permissions' => [
                        'create permissions',
                        'read permissions',
                        'update permissions',
                        'delete permissions',
                    ]
                ],
                [
                    'module_id' => $productModule->id,
                    'permissions' => [
                        'create products',
                        'read products',
                        'update products',
                        'delete products',
                    ]
                ],
                [
                    'module_id' => $orderModule->id,
                    'permissions' => [
                        'create orders',
                        'read orders',
                        'update orders',
                        'delete orders',
                    ]
                ],
                [
                    'module_id' => $paymentModule->id,
                    'permissions' => [
                        'create payments',
                        'read payments',
                        'update payments',
                        'delete payments',
                    ]
                ],
            ];

            foreach ($permissionsArr as $permission) {
                foreach ($permission['permissions'] as $name) {
                    $permission = Permission::create(['name' => $name, 'guard_name' => 'api', 'module_id' => $permission['module_id']]);
                    $superAdminRole->givePermissionTo($permission);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
    }
}
