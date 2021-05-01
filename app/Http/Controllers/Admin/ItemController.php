<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\NumberGenerate;

use App\Models\ItemType;
use App\Models\Item;
use App\Models\MonthEnd;
use App\Models\DailyCollectionSupplier;

class ItemController extends Controller {

    use NumberGenerate;

    public function items() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Items';

        $item_types = ItemType::get();
        $data['item_types'] = $item_types;

        // dd($data);
        return view('Admin.items')->with('data',$data);
    }

    public function itemsDatatable(Request $request ) {

        if ($request->ajax()) {
            $data = DB::table('items AS ti')
                    ->join('item_types AS tit','tit.id','ti.item_type')
                    ->select('ti.id AS item_id','ti.item_name','tit.id AS type_id','tit.type_name','ti.item_code','ti.unit_price','ti.weight','ti.volume','ti.pack_size','ti.deleted_at')
                    ->whereNull('tit.deleted_at')
                    ->orderBy('tit.id', 'ASC');
            return Datatables::of($data)
                    ->addColumn('action', function($data){

                        if (is_null($data->deleted_at)) {
                            if ($data->type_id == config('application.tealeaves_type') || $data->type_id == config('application.teabag_type') || $data->type_id == config('application.dolamite_type') ) {
                                $btn = '<a class="btn btn-sheding btn-sm" onclick="sendDataToViewModel('.$data->item_id.')" type="button"><i class="far fa-eye"></i></a>
                                        <a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->item_id.')" type="button"><i class="far fa-edit"></i></a>
                                        <a class="btn btn-disabled btn-sm" type="button"><i class="far fa-trash-alt"></i></a>';
                            }
                            else {
                                $btn = '<a class="btn btn-sheding btn-sm" onclick="sendDataToViewModel('.$data->item_id.')" type="button"><i class="far fa-eye"></i></a>
                                        <a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->item_id.')" data-mdb-toggle="modal" data-mdb-target="#edit_model" type="button"><i class="far fa-edit"></i></a>
                                        <a class="btn btn-sheding btn-sm" onclick="deleteItem('.$data->item_id.')" type="button"><i class="far fa-trash-alt"></i></a>';
                            }
                        }
                        else {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="far fa-eye"></i></a>
                                    <a class="btn btn-disabled btn-sm" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-deleted btn-sm" onclick="activateItem('.$data->item_id.')" type="button"><i class="fas fa-trash-restore-alt"></i></a>';
                        }

                        return $btn;
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                            $regex = $request->get('search')['value'];
                            return $query->where(function($queryNew) use($regex){
                                $queryNew->where('ti.item_name', 'like', '%' . $regex . '%')
                                    ->orWhere('tit.type_name', 'like', '%' . $regex . '%')
                                    ->orWhere('ti.unit_price', 'like', '%' . $regex . '%')
                                    ->orWhere('ti.item_code', 'like', '%' . $regex . '%');
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('Admin.items');
    }

    public function itemCodeGenerate(Request $request ) {

        $item_type = $request->selected_type;

        $item_code = $this->genereteItemCode($item_type);

        return response()->json([
            'result' => true,
            'code' => $item_code
        ]);
        
    }

    public function itemInsert(Request $request ) {

        $validator = Validator::make($request->all(), [
            'item_code' => 'required|unique:items',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'The item code has already been taken.',
            ]);
        }
        else {

            try {

                DB::beginTransaction();

                $item = new item();
                $item->item_code = $request->item_code;
                $item->item_name = $request->item_name;
                $item->item_type = $request->item_type;
                $item->unit_price = $request->unit_price;
                $item->save();    
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Item data successfully inserted',
                    'add_class' => 'alert-success',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Item data not successfully inserted',
                    'add_class' => 'alert-danger',
                ]);
            }
        }
    }

    public function itemGetData(Request $request ) {

        try {

            $item = Item::find($request->id); 

            return response()->json([
                'result' => true,
                'data' => $item
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'result' => false,
            ]);
        }
    }

    public function itemEdit(Request $request ) {

        $validator =Validator::make($request->all(), [
            'item_code' => Rule::unique('items')->ignore($request->item_id)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'The item code has already been taken.'
            ]);
        }
        else {

            try {

                DB::beginTransaction();

                $item = Item::find($request->item_id);
                $item->item_code = $request->item_code;
                $item->item_name = $request->item_name;
                $item->unit_price = $request->unit_price;
                $item->save();    
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Item data successfully edited',
                    'add_class' => 'alert-success',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Item data not successfully edited',
                    'add_class' => 'alert-danger',
                ]);
            }
        }
    }

    public function tealeavePriceChange(Request $request ) {

        DB::beginTransaction();

        try {

            $last_month_end = MonthEnd::select(DB::raw('MAX(month) AS last_ended_month'))
                                        ->where('ended_status',1)->first();
            $last_ended_month = $last_month_end->last_ended_month;
            
            $new_tealeave_price = $request->unit_price;

            $daily_collection =DB::table('daily_collection_suppliers AS tdcs')
                                    ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                    ->select('tdcs.*')
                                    ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'>',$last_ended_month)
                                    ->where('tdc.confirm_status', '=', 0)
                                    ->get();

            foreach ($daily_collection as $collection) {
                $collection_line_id = $collection->id;
                $number_of_units = $collection->number_of_units;
                $daily_amount = $number_of_units * $new_tealeave_price;
                $daily_value = $daily_amount - $collection->delivery_cost;

                $collection_line = DailyCollectionSupplier::withTrashed()->find($collection_line_id);
                $collection_line->current_units_price = $new_tealeave_price;
                $collection_line->daily_amount = $daily_amount;
                $collection_line->daily_value = $daily_value;
                $collection_line->save();
            }

            $item = Item::find($request->item_id);
            $item->unit_price = $new_tealeave_price;
            $item->save();    
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Item data successfully edited',
                'add_class' => $collection_line_id,
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Item data not successfully edited',
                'add_class' => $collection_line_id,
            ]);
        }
        
    }

    public function itemDelete(Request $request ) {

        try {

            DB::beginTransaction();

            $item = Item::find($request->item_id);
            $item->delete();  
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Item data successfully deleted',
                'add_class' => 'alert-success',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Item data not successfully deleted',
                'add_class' => 'alert-danger',
            ]);
        }
    }

    public function itemReactivate(Request $request ) {

        try {

            DB::beginTransaction();

            $item = Item::withTrashed()->find($request->item_id);
            $item->restore(); 
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Item data successfully restored',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Item data not successfully restored',
            ]);
        }
    }
}
