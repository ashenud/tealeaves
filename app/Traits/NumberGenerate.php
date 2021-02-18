<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

use App\Models\GoodsReceivedNote;
use App\Models\Item;

trait NumberGenerate {

    public function genereteNumber($type){

        $new_no = "";
        $prefix = config('application.grn_prefix');

        if ($type == config('application.grn_no')) {
            $grn_count = GoodsReceivedNote::withTrashed()->count();
            $formated_number = str_pad($grn_count+1, config('application.number_str_pad',6), '0', STR_PAD_LEFT);
            $new_no = $prefix.$formated_number;
        }
        
        return $new_no;
        
    }

    public function genereteItemCode($type){

        $new_no = "";
        $prefix = "";

        if ($type == config('application.fertilizer_type')) {
            $prefix = config('application.fertilizer_prefix');
        }
        else if ($type == config('application.chemical_type')) {
            $prefix = config('application.chemical_prefix');
        }

        $fer_count = Item::withTrashed()->where('item_type', $type)->count();
        $formated_number = str_pad($fer_count+1, config('application.code_str_pad',3), '0', STR_PAD_LEFT);
        $new_no = $prefix.$formated_number;
        
        return $new_no;
        
    }
}
