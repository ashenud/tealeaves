<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            ['sup_name' => 'Akash Tharaka','sup_no' => '0001', 'sup_address' => '26/B, Teppanawa, Awissawella', 'sup_contact' => '0712547823', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Ashan Weerawardhana','sup_no' => '0002',  'sup_address' => '11/L, Teppanawa, Awissawella', 'sup_contact' => '0712482201', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Ashen Udithamal','sup_no' => '0003',  'sup_address' => '66/A, Teppanawa, Awissawella', 'sup_contact' => '0712782201', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Chaluka Madhuranga','sup_no' => '0004',  'sup_address' => '85/X, Teppanawa, Awissawella', 'sup_contact' => '0771258640', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Chamara Herath','sup_no' => '0005',  'sup_address' => '99/O, Teppanawa, Awissawella', 'sup_contact' => '0711245523', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Hasitha Hiran','sup_no' => '0006',  'sup_address' => '23/B, Teppanawa, Awissawella', 'sup_contact' => '0712785124', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Janith Satharasinha','sup_no' => '0007',  'sup_address' => '46/P, Teppanawa, Awissawella', 'sup_contact' => '0712548975', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Malith Wimalasiri','sup_no' => '0008',  'sup_address' => '72/K, Teppanawa, Awissawella', 'sup_contact' => '0712547854', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Nandun Achalanka','sup_no' => '0009',  'sup_address' => '93/R, Teppanawa, Awissawella', 'sup_contact' => '0712587520', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Nandun Dilshan','sup_no' => '0010',  'sup_address' => '26/J, Teppanawa, Awissawella', 'sup_contact' => '0717452125	', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Nilusha Supun','sup_no' => '0011',  'sup_address' => '15/B, Teppanawa, Awissawella', 'sup_contact' => '0712548501', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Prabhodha Indunil','sup_no' => '0012',  'sup_address' => '12/W, Teppanawa, Awissawella', 'sup_contact' => '0712594820', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Ranga Lakshan','sup_no' => '0013',  'sup_address' => '20/B, Teppanawa, Awissawella', 'sup_contact' => '0701254780', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Rashmi Ishuwara','sup_no' => '0014',  'sup_address' => '36/D, Teppanawa, Awissawella', 'sup_contact' => '0712548701', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Ridma Kanchana','sup_no' => '0015',  'sup_address' => '96/H, Teppanawa, Awissawella', 'sup_contact' => '0712365478', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Samith Perera','sup_no' => '0016',  'sup_address' => '34/F, Teppanawa, Awissawella', 'sup_contact' => '0714268541', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Sudharshana Ramesh','sup_no' => '0017',  'sup_address' => '29/T, Teppanawa, Awissawella', 'sup_contact' => '0761248412', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Udesh Ishanka','sup_no' => '0018',  'sup_address' => '78/E, Teppanawa, Awissawella', 'sup_contact' => '0712584424', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Avishka Silva','sup_no' => '0019',  'sup_address' => '46/H, Teppanawa, Awissawella', 'sup_contact' => '0715893365', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Sanath Jayasooriya','sup_no' => '0020',  'sup_address' => '85/C, Teppanawa, Awissawella', 'sup_contact' => '0724522203', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Kumar Sangakkara','sup_no' => '0021',  'sup_address' => '71/O, Teppanawa, Awissawella', 'sup_contact' => '0725469874', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sup_name' => 'Mahela Jayawardhane','sup_no' => '0022',  'sup_address' => '19/I, Teppanawa, Awissawella', 'sup_contact' => '0725852203', 'route_id' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}