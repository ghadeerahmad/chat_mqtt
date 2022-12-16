<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('permissions')->truncate();
        Permission::upsert([
            [
                'name' => 'can create users',
                'code' => 'create_user',
                'group' => 'users',
            ],
            [
                'name' => 'can update users',
                'code' => 'update_user',
                'group' => 'users',
            ],
            [
                'name' => 'can delete users',
                'code' => 'delete_user',
                'group' => 'users',
            ],
            [
                'name' => 'can view users',
                'code' => 'view_user',
                'group' => 'users',
            ],
            [
                'name' => 'can view rooms',
                'code' => 'view_room',
                'group' => 'rooms',
            ],
            [
                'name' => 'can create rooms',
                'code' => 'create_room',
                'group' => 'rooms',
            ],
            [
                'name' => 'can update rooms',
                'code' => 'update_room',
                'group' => 'rooms',
            ], [
                'name' => 'can delete rooms',
                'code' => 'delete_room',
                'group' => 'rooms',
            ], [
                'name' => 'can view Admins',
                'code' => 'view_admin',
                'group' => 'Admins',
            ], [
                'name' => 'can create Admins',
                'code' => 'create_admin',
                'group' => 'Admins',
            ], [
                'name' => 'can update Admins',
                'code' => 'update_admin',
                'group' => 'Admins',
            ], [
                'name' => 'can delete Admins',
                'code' => 'delete_admin',
                'group' => 'Admins',
            ], [
                'name' => 'can view IDS',
                'code' => 'view_id',
                'group' => 'IDS',
            ], [
                'name' => 'can create IDS',
                'code' => 'create_id',
                'group' => 'IDS',
            ], [
                'name' => 'can update IDS',
                'code' => 'update_id',
                'group' => 'IDS',
            ], [
                'name' => 'can delete IDS',
                'code' => 'delete_id',
                'group' => 'IDS',
            ], [
                'name' => 'can View Roles',
                'code' => 'view_role',
                'group' => 'Roles',
            ], [
                'name' => 'can create Roles',
                'code' => 'create_role',
                'group' => 'Roles',
            ], [
                'name' => 'can update Roles',
                'code' => 'update_role',
                'group' => 'Roles',
            ], [
                'name' => 'can delete Roles',
                'code' => 'delete_role',
                'group' => 'Roles',
            ],
            [
                'name' => 'can view backgrounds',
                'code' => 'view_backgrounds',
                'group' => 'Backgrounds',
            ],
            [
                'name' => 'can create backgrounds',
                'code' => 'create_backgrounds',
                'group' => 'Backgrounds',
            ],
            [
                'name' => 'can update backgrounds',
                'code' => 'update_backgrounds',
                'group' => 'Backgrounds',
            ],
            [
                'name' => 'can delete backgrounds',
                'code' => 'delete_backgrounds',
                'group' => 'Backgrounds',
            ],
            [
                'name' => 'can view blacklist',
                'code' => 'view_blacklist',
                'group' => 'Blacklist',
            ],
            [
                'name' => 'can create blacklist',
                'code' => 'create_blacklist',
                'group' => 'Blacklist',
            ],
            [
                'name' => 'can delete blacklist',
                'code' => 'delete_blacklist',
                'group' => 'Blacklist',
            ],
        ], ['code']);
    }
}
