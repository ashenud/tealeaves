<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\NumberGenerate;

use App\Models\GoodsReceivedNote;
use App\Models\CurrentStock;

class StockController extends Controller {

    use NumberGenerate;

    public function index() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Stock';

        $items = DB::table('items AS ti')
                    ->join('item_types AS tit','tit.id','ti.item_type')
                    ->select(DB::raw('CONCAT(ti.id, ",", ti.item_name) AS value'),'ti.item_code')
                    ->whereNotIn('tit.id', [config('application.tealeaves_type')])
                    ->whereNull('ti.deleted_at')
                    ->whereNull('tit.deleted_at')
                    ->get();
        $data['items'] = $items;

        $grn_no = $this->genereteNumber(config('application.grn_no'));
        $data['grn_no'] = $grn_no;

        // dd($data);
        return view('Admin.stock')->with('data',$data);
    }

    public function stockDatatable(Request $request ) {

        if ($request->ajax()) {
            $data = DB::table('items AS ti')
                    ->join('item_types AS tit','tit.id','ti.item_type')
                    ->leftJoin('current_stocks AS tcs','tcs.item_id','ti.id')
                    ->select('ti.id AS item_id','ti.item_name','tit.id AS type_id','tit.type_name','ti.item_code','ti.unit_price',DB::raw('IFNULL(SUM(tcs.current_quantity),0) AS current_quantity'))
                    ->whereNull('ti.deleted_at')
                    ->whereNull('tit.deleted_at')
                    ->whereNull('tcs.deleted_at')
                    ->groupBy('ti.id')
                    ->orderBy('tit.id', 'ASC');
            return Datatables::of($data)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                            $regex = $request->get('search')['value'];
                            return $query->where(function($queryNew) use($regex){
                                $queryNew->where('ti.item_name', 'like', '%' . $regex . '%')
                                    ->orWhere('ti.item_code', 'like', '%' . $regex . '%');
                            });
                        }
                    })
                    ->make(true);
        }
        
        return view('Admin.stock');
    }
    
    public function insertGrn(Request $request) {

        $user_id = Auth::user()->id;
        $grn_date = $request->grn_date;
        $items_array = $request->item_array;

        $validator = Validator::make($request->all(), [
            'grn_no' => 'required|unique:goods_received_notes',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'GRN No. is already exist. please retry !',
            ]);
        }
        else {
            try {

                DB::beginTransaction();

                $grn = new GoodsReceivedNote();
                $grn->date = $grn_date;
                $grn->grn_no = $request->grn_no;
                $grn->user_id = $user_id;
                $grn->save();
                
                $grn_id = $grn->id;

                /* INSERT SUPPLIER DAILY ISSUE DETAILS*/
                $item_array =json_decode($items_array);

                if (count($item_array) > 0) {

                    for ($i = 0; $i < count($item_array); $i++) {
        
                        $current_stock = new CurrentStock();
                        $current_stock->grn_id = $grn_id;
                        $current_stock->item_id = $item_array[$i]->item_id;
                        $current_stock->grn_quantity = $item_array[$i]->grn_quantity;
                        $current_stock->current_quantity = $item_array[$i]->grn_quantity;
                        $current_stock->save();
                    }
                }
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'GRN data successfully inserted !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'GRN data not successfully inserted !',
                    'error' => $e,
                ]);
            }
        }

    }

}
