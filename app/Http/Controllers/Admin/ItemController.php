<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

use App\Models\ItemType;
use App\Models\Item;

class ItemController extends Controller
{
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
                    ->select('ti.id AS item_id','ti.item_name','tit.id AS type_id','tit.type_name','ti.item_code','ti.unit_price','ti.weight','ti.volume','ti.pack_size')
                    ->whereNull('ti.deleted_at')
                    ->whereNull('tit.deleted_at')
                    ->orderBy('tit.id', 'ASC');
            return Datatables::of($data)
                    ->addColumn('action', function($data){
                        
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
                        return $btn;
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('item_name')) {
                            $query->where('ti.item_name', 'like', "%{$request->get('item_name')}%");
                        }
                        if ($request->has('type_name')) {
                            $query->where('tit.type_name', 'like', "%{$request->get('type_name')}%");
                        }
                        if ($request->has('item_code')) {
                            $query->where('ti.item_code', 'like', "%{$request->get('item_code')}%");
                        }
                        if ($request->has('unit_price')) {
                            $query->where('ti.unit_price', 'like', "%{$request->get('unit_price')}%");
                        }
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

    public function itemInsert(Request $request ) {

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
}
