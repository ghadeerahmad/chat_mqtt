<?php

namespace Database\Seeders;

use App\Models\RoomPrivilegeRole;
use App\Models\RoomRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomPrivilegeRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('room_privilege_roles')->truncate();
        $roles = RoomRole::all();
        foreach ($roles as $role) {
            RoomPrivilegeRole::create(['room_role_id' => $role->id, 'room_privilege_id' => 1]);
        }
    }
}
