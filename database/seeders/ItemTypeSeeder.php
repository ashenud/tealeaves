<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('item_types')->insert([
            ['type_name' => 'Tea Leaves', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type_name' => 'Tea Bags', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type_name' => 'Dolamite', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type_name' => 'Chemicals', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['type_name' => 'Fertilizers', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}