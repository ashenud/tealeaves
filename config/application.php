<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | This use to get ajax request when app run without domain 
    |
    */

    'base_url' => env('BASE_URL', '/tealeaves'),

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

    /*
    |--------------------------------------------------------------------------
    | Item IDs
    |--------------------------------------------------------------------------
    |
    | Database auto increment id of items table
    |
    */

    'tealeaves' => env('TEALEAVES', 1),

    /*
    |--------------------------------------------------------------------------
    | Item Type IDs
    |--------------------------------------------------------------------------
    |
    | Database auto increment id of item_types table
    |
    */

    'tealeaves_type' => env('TEALEAVES_TYPE', 1),
    'teabag_type' => env('TEABAG_TYPE', 2),
    'dolamite_type' => env('DOLAMITE_TYPE', 3),
    'chemical_type' => env('CHEMICAL_TYPE', 4),
    'fertilizer_type' => env('FERTILIZER_TYPE', 5),
    

    /*
    |--------------------------------------------------------------------------
    | str_pad counts
    |--------------------------------------------------------------------------
    |
    | Use to create IDs and Numbers using DB auto increment ids 
    |
    */

    'supplier_str_pad' => env('SUPPLIER_STR_PAD', 4),
    'number_str_pad' => env('NUMBER_STR_PAD', 6),
    'code_str_pad' => env('CODE_STR_PAD', 3),
    

    /*
    |--------------------------------------------------------------------------
    | Number Format Types
    |--------------------------------------------------------------------------
    |
    | Use to create auto increment numbers 
    |
    */

    'advanced_no' => env('ADVANCED_NO', 1),
    'loan_no' => env('LOAN_NO', 2),
    'grn_no' => env('GRN_NO', 3),
    

    /*
    |--------------------------------------------------------------------------
    | Prefix Types
    |--------------------------------------------------------------------------
    |
    | Use to create auto increment numbers 
    |
    */

    'advanced_prefix' => env('ADVANCED_PREFIX', 'ADV'),
    'loan_prefix' => env('LOAN_PREFIX', 'LON'),
    'grn_prefix' => env('GRN_PREFIX', 'GRN'),
    'fertilizer_prefix' => env('FERTILIZER_PREFIX', 'FER'),
    'chemical_prefix' => env('CHEMICAL_PREFIX', 'CHE'),

];
