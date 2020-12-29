<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Route;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function suppliers() {

        $user_id = Auth::user()->user_id;
        $route = Route::get();
        $data = array();

        $data['route'] = $route;

        // dd($data);
        return view('Admin.suppliers')->with('data',$data);
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
}
