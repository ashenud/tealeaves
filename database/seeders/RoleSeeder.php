<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => md5('1'),
            'role_name' => 'Admin',
            'read' => 1,
            'write' => 1,
            'update' => 1,
            'delete' => 1,
            'usermanage' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}