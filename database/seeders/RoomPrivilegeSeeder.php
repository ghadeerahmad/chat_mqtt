<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('room_privileges')->truncate();
        DB::table('room_privileges')->insert([
            [
                'name' => 'admin',
            ],
        ]);
    }
}
