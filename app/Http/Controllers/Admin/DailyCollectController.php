<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

            $data['collection_id'] = $collection[0]->id;
            $data['daily_total_value'] = $collection[0]->daily_total_value;
            $data['collection_status'] = $collection[0]->confirm_status;

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

    public function loadEditCollection($id) {

        $user_id = Auth::user()->id;

        $collection = DailyCollection::where('id',$id)->where('confirm_status',0)->limit(1)->get();

        if(count($collection) > 0) {

            $collection_id = $collection[0]->id;

            $data = array();

            $data['collection_id'] = $collection_id;
            $data['daily_total_value'] = $collection[0]->daily_total_value;

            $supplier_collection = DB::table('daily_collection_suppliers AS tdcs')
                                      ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                      ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                      ->join('items AS ti','ti.id','tdcs.item_id')
                                      ->select('tdcs.id','ts.sup_name','tdcs.supplier_id','ti.item_name','tdcs.item_id','tdcs.number_of_units','tdcs.current_units_price','tdcs.delivery_cost_per_unit','tdcs.delivery_cost','tdcs.daily_amount','tdcs.daily_value')
                                      ->where('tdc.id',$collection_id)
                                      ->whereNull('tdcs.deleted_at')
                                      ->whereNull('tdc.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get();
            $data['suppliers'] = $supplier_collection;
            $data['actual_supplier_count'] = count($supplier_collection);

            // dd($data);
            return view('Admin.Loadings.collection-edit')->with('data',$data);

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
        
                        $collection_supplier = new DailyCollectionSupplier();
                        $collection_supplier->collection_id = $collection_id;
                        $collection_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                        $collection_supplier->item_id = $supplier_array[$i]->item_id;
                        $collection_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                        $collection_supplier->current_units_price = $supplier_array[$i]->current_price;
                        $collection_supplier->delivery_cost_per_unit = $supplier_array[$i]->delivery_cost_per_unit;
                        $collection_supplier->delivery_cost = $supplier_array[$i]->delivery_cost;
                        $collection_supplier->daily_amount = $supplier_array[$i]->daily_amount;
                        $collection_supplier->daily_value = $supplier_array[$i]->daily_value;
                        $collection_supplier->save();
                        
                    }
                }

                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily collection data successfully inserted !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily collection data not successfully inserted !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'There is a collection assign this day !',
            ]);
        }

    }
    
    public function editCollection(Request $request) {

        $user_id = Auth::user()->id;
        $collection_id = $request->collection_id;
        $collection_array = $request->collection_array;
        $removed_suppliers = $request->removed_suppliers;

        $is_exist = DailyCollection::where('id',$collection_id)->where('confirm_status',0)->limit(1)->get();

        if(count($is_exist) > 0) {

            try {

                DB::beginTransaction();

                $collection = DailyCollection::find($collection_id);
                $collection->daily_total_value = $request->daily_total_value;
                $collection->edited_status = 1;
                $collection->save();

                /* UPDATE SUPPLIER DAILY COLLECTION DETAILS*/
                $remove_suppliers_array =json_decode($removed_suppliers);

                if(count($remove_suppliers_array) > 0) {
                    for ($j = 0; $j < count($remove_suppliers_array); $j++) {

                        $daily_supplier = DailyCollectionSupplier::find($remove_suppliers_array[$j]);
                        $daily_supplier->deleted_at = Carbon::now();
                        $daily_supplier->save();                        
                    }
                }

                $supplier_array =json_decode($collection_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $sup_collection_id = $supplier_array[$i]->sup_col_id;

                        if ($sup_collection_id == 0) {
                            $collection_supplier = new DailyCollectionSupplier();
                            $collection_supplier->collection_id = $collection_id;
                            $collection_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $collection_supplier->item_id = $supplier_array[$i]->item_id;
                            $collection_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $collection_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $collection_supplier->delivery_cost_per_unit = $supplier_array[$i]->delivery_cost_per_unit;
                            $collection_supplier->delivery_cost = $supplier_array[$i]->delivery_cost;
                            $collection_supplier->daily_amount = $supplier_array[$i]->daily_amount;
                            $collection_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $collection_supplier->save();
                        }
                        else {
                            $collection_supplier = DailyCollectionSupplier::find($sup_collection_id);
                            $collection_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $collection_supplier->item_id = $supplier_array[$i]->item_id;
                            $collection_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $collection_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $collection_supplier->delivery_cost_per_unit = $supplier_array[$i]->delivery_cost_per_unit;
                            $collection_supplier->delivery_cost = $supplier_array[$i]->delivery_cost;
                            $collection_supplier->daily_amount = $supplier_array[$i]->daily_amount;
                            $collection_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $collection_supplier->save();
                        }
                        
                    }
                }
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily collection data successfully edited !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily collection data not successfully edited !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'You can not edit these collection data !',
            ]);
        }

    }
    
    public function confirmCollection(Request $request) {

        $user_id = Auth::user()->id;
        $collection_id = $request->collection_id;

        $is_exist = DailyCollection::where('id',$collection_id)->where('confirm_status',0)->limit(1)->get();

        if(count($is_exist) > 0) {

            try {

                DB::beginTransaction();

                $collection = DailyCollection::find($collection_id);
                $collection->confirm_status = 1;
                $collection->save();
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily collection data successfully confirmed',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily collection data not successfully confirmed',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'This collection is has allready confirmed',
            ]);
        }

    }

}
