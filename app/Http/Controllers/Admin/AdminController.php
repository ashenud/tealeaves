<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index() {

        $user_id = Auth::user()->user_id;
        $data = array();

        // dd($data);
        return view('Admin.dashboard')->with('data',$data);
    }
}
