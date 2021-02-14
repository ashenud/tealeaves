<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

use App\Models\GoodsReceivedNote;

trait NumberGenerate {

    public function genereteNumber($type){

        $new_no = "";

        if ($type == config('application.grn_no')) {
            $grn_count = GoodsReceivedNote::withTrashed()->count();
            $formated_number = str_pad($grn_count+1, config('application.number_str_pad',6), '0', STR_PAD_LEFT);
            $new_no = 'GRN/'.$formated_number;
        }
        
        return $new_no;
        
    }
}
