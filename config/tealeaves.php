<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Dates
    |--------------------------------------------------------------------------
    |
    | This dates are use to create month end table data seeders and 
    | to stop loop of month end table (in view) getting last month
    | data in yajra dataTables
    |
    */

    'start_date' => env('START_DATE', '2020-11-01'),
    'end_date' => env('END_DATE', '2030-12-01'),
    'previous_month' => env('PREVIOUS_MONTH', '2020-10'),

];
