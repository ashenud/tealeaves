<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class Item extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            ['item_type' => '1', 'item_name' => 'Tea Leaves', 'item_code' => 'TEA001', 'unit_price' => '121.50', 'weight' => '1.000', 'volume' => null, 'pack_size' => '22', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '2', 'item_name' => 'Tea Bags', 'item_code' => 'TBG001', 'unit_price' => '481.60', 'weight' => '0.400', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '3', 'item_name' => 'Dolamite', 'item_code' => 'DOL001', 'unit_price' => '757.20', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '4', 'item_name' => 'Triethanolamine', 'item_code' => 'CHE001', 'unit_price' => '297.50', 'weight' => null, 'volume' => '0.350', 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '4', 'item_name' => 'Herbicides', 'item_code' => 'CHE002', 'unit_price' => '324.00', 'weight' => null, 'volume' => '0.350', 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '5', 'item_name' => 'U 709', 'item_code' => 'FER001', 'unit_price' => '784.00', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '5', 'item_name' => 'ST/UVA 435', 'item_code' => 'FER002', 'unit_price' => '970.50', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '5', 'item_name' => 'P/LC 880', 'item_code' => 'FER003', 'unit_price' => '725.00', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '5', 'item_name' => 'T 200', 'item_code' => 'FER004', 'unit_price' => '500.00', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['item_type' => '5', 'item_name' => 'T 750', 'item_code' => 'FER005', 'unit_price' => '700.00', 'weight' => '25.000', 'volume' => null, 'pack_size' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
