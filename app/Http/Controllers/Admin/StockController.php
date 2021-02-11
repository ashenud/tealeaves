<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ItemType;

class StockController extends Controller
{
    public function index() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Stock';

        $item_types = ItemType::get();
        $data['item_types'] = $item_types;

        // dd($data);
        return view('Admin.stock')->with('data',$data);
    }
}
