<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;

class MonthEndSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        DB::table('month_ends')->insert([
            'month' => '2020-10',
            'ended_status' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        $start    = new DateTime('2020-11-01');
        $start->modify('first day of this month');
        $end      = new DateTime('2030-01-01');
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {

            DB::table('month_ends')->insert([
                'month' => $dt->format("Y-m"),
                'ended_status' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

    }
}
