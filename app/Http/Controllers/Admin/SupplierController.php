<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Route;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function suppliers() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Suppliers';

        $route = Route::get();
        $data['route'] = $route;

        // dd($data);
        return view('Admin.suppliers')->with('data',$data);
    }

    public function supplierDatatable(Request $request ) {

        if ($request->ajax()) {
            $data = Supplier::select('id','sup_name','sup_address','sup_contact','route_id');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
     
                           $btn = ' <a class="btn btn-sheding btn-sm" onclick="sendDataToViewModel('.$data->id.')" type="button"><i class="far fa-eye"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->id.')" data-mdb-toggle="modal" data-mdb-target="#edit_model" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="deleteSupplier('.$data->id.')" type="button"><i class="far fa-trash-alt"></i></a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        return view('Admin.suppliers');
    }

    public function supplierInsert(Request $request ) {

        try {

            DB::beginTransaction();

            $supplier = new Supplier();
            $supplier->sup_name = $request->supplier_name;
            $supplier->sup_address = $request->supplier_address;
            $supplier->sup_contact = $request->supplier_contact;
            $supplier->route_id = $request->supplier_route;
            $supplier->save();    
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Supplier data successfully inserted',
                'add_class' => 'alert-success',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Supplier data not successfully inserted',
                'add_class' => 'alert-danger',
            ]);
        }
    }

    public function supplierGetData(Request $request ) {

        try {

            $supplier = Supplier::find($request->id); 

            return response()->json([
                'result' => true,
                'data' => $supplier
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'result' => false,
            ]);
        }
    }

    public function supplierEdit(Request $request ) {

        try {

            DB::beginTransaction();

            $supplier = Supplier::find($request->supplier_id);
            $supplier->sup_name = $request->supplier_name;
            $supplier->sup_address = $request->supplier_address;
            $supplier->sup_contact = $request->supplier_contact;
            $supplier->route_id = $request->supplier_route;
            $supplier->save();    
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Supplier data successfully edited',
                'add_class' => 'alert-success',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Supplier data not successfully edited',
                'add_class' => 'alert-danger',
            ]);
        }
    }

    public function supplierDelete(Request $request ) {

        try {

            DB::beginTransaction();

            $supplier = Supplier::find($request->supplier_id);
            $supplier->delete();  
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Supplier data successfully deleted',
                'add_class' => 'alert-success',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Supplier data not successfully deleted',
                'add_class' => 'alert-danger',
            ]);
        }
    }
}
