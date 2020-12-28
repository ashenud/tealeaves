<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('routes')->insert([
            'route_name' => 'Teppanawa', 'delivery_cost' => 2 , 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()
        ]);
    }
}
