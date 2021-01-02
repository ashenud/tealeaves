<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;
use App\Models\Route;
use App\Models\Item;

class DailyCollectController extends Controller
{
    public function index() {

        $user_id = Auth::user()->user_id;
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
}
