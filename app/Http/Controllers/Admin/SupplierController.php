<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            $data = Supplier::withTrashed()->select('id',DB::raw('LPAD(id,4,0) AS supplier_id'),'sup_name','sup_no','sup_address','sup_contact','route_id','deleted_at');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data){

                        if (is_null($data->deleted_at)){
                            $btn = '<a class="btn btn-sheding btn-sm" onclick="sendDataToViewModel('.$data->id.')" type="button"><i class="far fa-eye"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->id.')" data-mdb-toggle="modal" data-mdb-target="#edit_model" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="deleteSupplier('.$data->id.')" type="button"><i class="far fa-trash-alt"></i></a>';
                        }
                        else {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="far fa-eye"></i></a>
                                    <a class="btn btn-disabled btn-sm" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-deleted btn-sm" onclick="activateSupplier('.$data->id.')" type="button"><i class="fas fa-trash-restore-alt"></i></a>';
                        }
                        
                        return $btn;
                            
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                            $regex = $request->get('search')['value'];
                            return $query->where(function($queryNew) use($regex){
                                $queryNew->where('sup_no', 'like', '%' . $regex . '%')
                                    ->orWhere('sup_name', 'like', '%' . $regex . '%')
                                    ->orWhere('sup_address', 'like', '%' . $regex . '%')
                                    ->orWhere('sup_contact', 'like', '%' . $regex . '%');
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
    }

    public function supplierInsert(Request $request ) {

        $validator = Validator::make($request->all(), [
            'supplier_no' => 'required|unique:suppliers,sup_no',
            'supplier_name' => 'regex:/^[\pL\s\-]+$/u|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()
            ]);
        }
        else {

            try {

                DB::beginTransaction();

                $supplier = new Supplier();
                $supplier->sup_no = $request->supplier_no;
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

        $validator =Validator::make($request->all(), [
            'supplier_name' => 'regex:/^[a-zA-Z\s\.]+$/u|max:255',
            'supplier_no' => Rule::unique('suppliers', 'sup_no')->ignore($request->supplier_id)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()
            ]);
        }
        else {

            try {

                DB::beginTransaction();

                $supplier = Supplier::find($request->supplier_id);
                $supplier->sup_no = $request->supplier_no;
                $supplier->sup_name = $request->supplier_name;
                $supplier->sup_address = $request->supplier_address;
                $supplier->sup_contact = $request->supplier_contact;
                $supplier->route_id = $request->supplier_route;
                $supplier->save();    
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Supplier data successfully edited',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Supplier data not successfully edited',
                ]);
            }
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

    public function supplierReactivate(Request $request ) {

        try {

            DB::beginTransaction();

            $supplier = Supplier::withTrashed()->find($request->supplier_id);
            $supplier->restore(); 
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Supplier data successfully restored',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Supplier data not successfully restored',
            ]);
        }
    }
}
