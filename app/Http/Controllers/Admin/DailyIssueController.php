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
use App\Models\DailyIssues;
use App\Models\DailyIssuesSupplier;

class DailyIssueController extends Controller
{
    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Daily Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->select('ts.sup_name','ts.id')
                       ->whereNull('ts.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        $items = DB::table('items AS ti')
                        ->join('item_types AS tit','tit.id','ti.item_type')
                        ->select(DB::raw('CONCAT(ti.item_type, ",", ti.id, ",", ti.unit_price) AS value'),'ti.item_name')
                        ->whereNotIn('tit.id', [1, 5])
                        ->whereNull('ti.deleted_at')
                        ->whereNull('tit.deleted_at')
                        ->get();
        $data['items'] = $items;

        // dd($data);
        return view('Admin.daily-issue')->with('data',$data);
    }

    public function loadInsertIssues($date) {

        $user_id = Auth::user()->id;

        $issue_date = $date;
        $issue = DailyIssues::where('date',$issue_date)->limit(1)->get();

        if(count($issue) == 0) {

            $data = array();

            $suppliers = DB::table('suppliers AS ts')
                        ->select('ts.sup_name','ts.id')
                        ->whereNull('ts.deleted_at')
                        ->get();
            $data['suppliers'] = $suppliers;

            $items = DB::table('items AS ti')
                        ->join('item_types AS tit','tit.id','ti.item_type')
                        ->select(DB::raw('CONCAT(ti.item_type, ",", ti.id, ",", ti.unit_price) AS value'),'ti.item_name')
                        ->whereNotIn('tit.id', [1, 5])
                        ->whereNull('ti.deleted_at')
                        ->whereNull('tit.deleted_at')
                        ->get();
            $data['items'] = $items;

            // dd($data);
            return view('Admin.Loadings.issue-insert')->with('data',$data);

        }
        else {

            $issue_id = $issue[0]->id;

            $data = array();

            $data['issue_id'] = $issue[0]->id;
            $data['daily_total_value'] = $issue[0]->daily_total_value;
            $data['issue_status'] = $issue[0]->confirm_status;

            $supplier_issue = DB::table('daily_issues_suppliers AS tdis')
                                      ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                      ->join('suppliers AS ts','ts.id','tdis.supplier_id')
                                      ->join('items AS ti','ti.id','tdis.item_id')
                                      ->select('ts.sup_name','ti.item_name','tdis.number_of_units','tdis.current_units_price','tdis.daily_value')
                                      ->where('tdi.id',$issue_id)
                                      ->whereNull('tdis.deleted_at')
                                      ->whereNull('tdi.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get();
            $data['suppliers'] = $supplier_issue;
            // dd($data);
            return view('Admin.Loadings.collection-view')->with('data',$data);
        }
    }

}
