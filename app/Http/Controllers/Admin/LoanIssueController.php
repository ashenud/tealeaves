<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LoanIssueController extends Controller
{
    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Loan Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->join('routes AS tr','tr.id','ts.route_id')
                       ->select('ts.sup_name','ts.id')
                       ->whereNull('ts.deleted_at')
                       ->whereNull('tr.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        // dd($data);
        return view('Admin.loan-issue')->with('data',$data);
    }
}
