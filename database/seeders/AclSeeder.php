<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Permission;
use App\Models\Role;

class AclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'guard_name' => 'dashboard',
            ],
            [
                'name' => 'superadmin',
                'guard_name' => 'dashboard',
            ],
            [
                'name' => 'manager',
                'guard_name' => 'dashboard',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrNew(Arr::only($role, ['name']), $role)->save();
        }

        $permissions = [
            [
                'name' => 'dashboard.login',
                'description' => 'Login dashboard',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'menu.post',
                'description' => 'Menu create/update',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'menu.delete',
                'description' => 'Delete menu item',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'setting.post',
                'description' => 'Setting item create/edit',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'setting.delete',
                'description' => 'Setting delete',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'article.post',
                'description' => 'Article item create/edit',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
            [
                'name' => 'article.delete',
                'description' => 'Article delete',
                'guard_name' => 'dashboard',
                'roles' => ['superadmin', 'admin'],
            ],
        ];

        $rolesByName = Role::all()->pluck('id', 'name')->toArray();

        foreach ($permissions as $permissionRaw) {
            $permissionAttrs = Arr::only($permissionRaw, ['name', 'description', 'guard_name']);
            $permission = Permission::firstOrNew(Arr::only($permissionRaw, ['name']), $permissionAttrs)->save();
            $inputRoles = array_flip(Arr::get($permissionRaw, 'roles', []);
            $rolesId = array_values(array_intersect_key($rolesByName, $inputRoles));
            $permission->syncRoles($rolesId);
        }
    }
}
