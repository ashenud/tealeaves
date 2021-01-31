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

class DailyIssueController extends Controller {

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

            $current_month = '2020-12';
            $timestamp = strtotime($issue_date);
            $cuurent_issue_month = date("Y-m", $timestamp);

            if ($current_month == $cuurent_issue_month) {
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

                // dd($cuurent_issue_month);
                return view('Admin.Loadings.issue-insert')->with('data',$data);

            }
            else {
                return view('Admin.Loadings.no-data');
            }

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
            return view('Admin.Loadings.issue-view')->with('data',$data);
        }
    }

    public function loadEditIssues($id) {

        $user_id = Auth::user()->id;

        $issue = DailyIssues::where('id',$id)->where('confirm_status',0)->limit(1)->get();

        if(count($issue) > 0) {

            $issue_id = $issue[0]->id;

            $data = array();

            $data['issue_id'] = $issue_id;
            $data['daily_total_value'] = $issue[0]->daily_total_value;

            $supplier_issue = DB::table('daily_issues_suppliers AS tdis')
                                      ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                      ->join('suppliers AS ts','ts.id','tdis.supplier_id')
                                      ->join('items AS ti','ti.id','tdis.item_id')
                                      ->select('tdis.id','ts.sup_name','tdis.supplier_id','ti.item_name','tdis.item_type','tdis.item_id','tdis.number_of_units','tdis.current_units_price','tdis.daily_value')
                                      ->where('tdi.id',$issue_id)
                                      ->whereNull('tdis.deleted_at')
                                      ->whereNull('tdi.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get();
            $data['supplier_issues'] = $supplier_issue;
            $data['actual_supplier_count'] = count($supplier_issue);

            // dd($data);
            return view('Admin.Loadings.issue-edit')->with('data',$data);

        }

    }
    
    public function insertIssues(Request $request) {

        $user_id = Auth::user()->id;
        $issue_date = $request->issue_date;
        $issue_array = $request->issue_array;

        $is_exist = DailyIssues::where('date',$issue_date)->limit(1)->get();

        if(count($is_exist) == 0) {

            try {

                DB::beginTransaction();

                $issue = new DailyIssues();
                $issue->date = $issue_date;
                $issue->daily_total_value = $request->daily_total_value;
                $issue->user_id = $user_id;
                $issue->save();
                
                $issue_id = $issue->id;

                /* INSERT SUPPLIER DAILY ISSUE DETAILS*/
                $supplier_array =json_decode($issue_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $issue_supplier = new DailyIssuesSupplier();
                        $issue_supplier->issue_id = $issue_id;
                        $issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                        $issue_supplier->item_type = $supplier_array[$i]->item_type;
                        $issue_supplier->item_id = $supplier_array[$i]->item_id;
                        $issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                        $issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                        $issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                        $issue_supplier->save();
                        
                    }
                }

                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily issue data successfully inserted !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily issue data not successfully inserted !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'There is a issue assign this day !',
            ]);
        }

    }
    
    public function editIssues(Request $request) {

        $user_id = Auth::user()->id;
        $issue_id = $request->issue_id;
        $issue_array = $request->issue_array;
        $removed_supplier_issues = $request->removed_supplier_issues;

        $is_exist = DailyIssues::where('id',$issue_id)->where('confirm_status',0)->limit(1)->get();

        if(count($is_exist) > 0) {

            try {

                DB::beginTransaction();

                $issue = DailyIssues::find($issue_id);
                $issue->daily_total_value = $request->daily_total_value;
                $issue->edited_status = 1;
                $issue->save();

                /* UPDATE SUPPLIER DAILY ISSUES DETAILS*/
                $remove_supplier_issues_array =json_decode($removed_supplier_issues);

                if(count($remove_supplier_issues_array) > 0) {
                    for ($j = 0; $j < count($remove_supplier_issues_array); $j++) {

                        $daily_supplier = DailyIssuesSupplier::find($remove_supplier_issues_array[$j]);
                        $daily_supplier->deleted_at = Carbon::now();
                        $daily_supplier->save();                        
                    }
                }

                $supplier_array =json_decode($issue_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $sup_issue_id = $supplier_array[$i]->sup_col_id;

                        if ($sup_issue_id == 0) {
                            $issue_supplier = new DailyIssuesSupplier();
                            $issue_supplier->issue_id = $issue_id;
                            $issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $issue_supplier->item_type = $supplier_array[$i]->item_type;
                            $issue_supplier->item_id = $supplier_array[$i]->item_id;
                            $issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $issue_supplier->save();
                        }
                        else {
                            $issue_supplier = DailyIssuesSupplier::find($sup_issue_id);
                            $issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $issue_supplier->item_type = $supplier_array[$i]->item_type;
                            $issue_supplier->item_id = $supplier_array[$i]->item_id;
                            $issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $issue_supplier->save();
                        }
                        
                    }
                }
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily issue data successfully edited !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily issue data not successfully edited !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'You can not edit these issue data !',
            ]);
        }

    }
    
    public function confirmIssues(Request $request) {

        $user_id = Auth::user()->id;
        $issue_id = $request->issue_id;

        $is_exist = DailyIssues::where('id',$issue_id)->where('confirm_status',0)->limit(1)->get();

        if(count($is_exist) > 0) {

            try {

                DB::beginTransaction();

                $issue = DailyIssues::find($issue_id);
                $issue->confirm_status = 1;
                $issue->save();
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily issue data successfully confirmed',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily issue data not successfully confirmed',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'This issue is has allready confirmed',
            ]);
        }

    }

}
