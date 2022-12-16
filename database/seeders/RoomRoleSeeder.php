<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('room_roles')->truncate();
        DB::table('room_roles')->insert([
            [
                'name' => 'Update Room',
                'code' => 'update_room',
                'group' => 'room',
            ],
            [
                'name' => 'Delete Room',
                'code' => 'delete_room',
                'group' => 'room',
            ],
            [
                'name' => 'Kick Users',
                'code' => 'kick_user',
                'group' => 'room',
            ],
            [
                'name' => 'Ban Users',
                'code' => 'ban_user',
                'group' => 'room',
            ],
            [
                'name' => 'View Blaklist',
                'code' => 'view_blacklist',
                'group' => 'room blacklist',
            ],
            [
                'name' => 'update Blaklist',
                'code' => 'update_blacklist',
                'group' => 'room blacklist',
            ],
            [
                'name' => 'view roles',
                'code' => 'view_roles',
                'group' => 'room roles',
            ],
            [
                'name' => 'update roles',
                'code' => 'update_roles',
                'group' => 'room roles',
            ],
        ]);
    }
}
