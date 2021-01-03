<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;
use App\Models\Route;
use App\Models\Item;
use App\Models\DailyCollection;
use App\Models\DailyCollectionSupplier;

class DailyCollectController extends Controller
{

    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Daily Collect';

        $suppliers = DB::table('suppliers AS ts')
                       ->join('routes AS tr','tr.id','ts.route_id')
                       ->select('ts.sup_name',DB::raw('CONCAT(ts.id, ",", tr.delivery_cost) AS value'))
                       ->whereNull('ts.deleted_at')
                       ->whereNull('tr.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        $items = Item::where('id',1)->get();
        $data['item_id'] = $items[0]->id;
        $data['item_name'] = $items[0]->item_name;
        $data['item_price'] = $items[0]->unit_price;

        // dd($data);
        return view('Admin.daily-collect')->with('data',$data);
    }

    public function loadInsertCollection($date) {

        $user_id = Auth::user()->id;

        $collection_date = $date;
        $collection = DailyCollection::where('date',$collection_date)->limit(1)->get();

        if(count($collection) == 0) {

            $data = array();

            $suppliers = DB::table('suppliers AS ts')
                        ->join('routes AS tr','tr.id','ts.route_id')
                        ->select('ts.sup_name',DB::raw('CONCAT(ts.id, ",", tr.delivery_cost) AS value'))
                        ->whereNull('ts.deleted_at')
                        ->whereNull('tr.deleted_at')
                        ->get();
            $data['suppliers'] = $suppliers;

            $items = Item::where('id',1)->get();
            $data['item_id'] = $items[0]->id;
            $data['item_name'] = $items[0]->item_name;
            $data['item_price'] = $items[0]->unit_price;

            // dd($data);
            return view('Admin.Loadings.collection-insert')->with('data',$data);

        }
        else {

            $collection_id = $collection[0]->id;

            $data = array();

            $daily_total_value = $collection[0]->daily_total_value;
            $data['daily_total_value'] = $daily_total_value;

            $supplier_collection = DB::table('daily_collection_suppliers AS tdcs')
                                      ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                      ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                      ->join('items AS ti','ti.id','tdcs.item_id')
                                      ->select('ts.sup_name','ti.item_name','tdcs.number_of_units','tdcs.current_units_price','tdcs.delivery_cost','tdcs.daily_value')
                                      ->where('tdc.id',$collection_id)
                                      ->whereNull('tdcs.deleted_at')
                                      ->whereNull('tdc.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get();
            $data['suppliers'] = $supplier_collection;
            // dd($data);
            return view('Admin.Loadings.collection-view')->with('data',$data);
        }
    }
    
    public function insertCollection(Request $request) {

        $user_id = Auth::user()->id;
        $collection_date = $request->collection_date;
        $collection_array = $request->collection_array;

        $is_exist = DailyCollection::where('date',$collection_date)->limit(1)->get();

        if(count($is_exist) == 0) {

            try {

                DB::beginTransaction();

                $collection = new DailyCollection();
                $collection->date = $collection_date;
                $collection->daily_total_value = $request->daily_total_value;
                $collection->user_id = $user_id;
                $collection->save();
                
                $collection_id = $collection->id;

                /* INSERT SUPPLIER DAILY COLLECTION DETAILS*/
                $supplier_array =json_decode($collection_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $supplier_id = $supplier_array[$i]->supplier_id;
                        $item_id = $supplier_array[$i]->item_id;
                        $current_price = $supplier_array[$i]->current_price;
                        $delivery_cost_per_unit = $supplier_array[$i]->delivery_cost_per_unit;
                        $no_of_units = $supplier_array[$i]->no_of_units;
                        $delivery_cost = $supplier_array[$i]->delivery_cost;
                        $daily_amount = $supplier_array[$i]->daily_amount;
                        $daily_value = $supplier_array[$i]->daily_value;
        
                        $collection_supplier = new DailyCollectionSupplier();
                        $collection_supplier->collection_id = $collection_id;
                        $collection_supplier->supplier_id = $supplier_id;
                        $collection_supplier->item_id = $item_id;
                        $collection_supplier->number_of_units = $no_of_units;
                        $collection_supplier->current_units_price = $current_price;
                        $collection_supplier->delivery_cost_per_unit = $delivery_cost_per_unit;
                        $collection_supplier->delivery_cost = $delivery_cost;
                        $collection_supplier->daily_amount = $daily_amount;
                        $collection_supplier->daily_value = $daily_value;
                        $collection_supplier->save();
                        
                    }
                }

                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily collection data successfully inserted',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily collection data not successfully inserted',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'complte',
            ]);
        }

    }

}
