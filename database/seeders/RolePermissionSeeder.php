<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('role_permissions')->truncate();
        $permissions = Permission::all();
        $data = [];
        foreach ($permissions as $permission) {
            array_push($data, ['role_id' => 1, 'permission_id' => $permission->id]);
        }
        RolePermission::insert($data);
    }
}
