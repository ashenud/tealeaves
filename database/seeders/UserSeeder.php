<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'System Admin',
            'username' => 'admin',
            'role_id' => md5('1'),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('123'),
            'status' => 1,
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now()
        ]);
    }
}
