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
use App\Models\MonthEnd;

class DailyIssueController extends Controller {

    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Daily Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),DB::raw('CONCAT(ts.id, ",", ts.sup_name) AS value'))
                       ->whereNull('ts.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        $not_in = config('application.tealeaves_type').','.config('application.fertilizer_type');
        $query="SELECT
                    ti.item_name,
                    ti.item_code,
                    @cuurentStock := IFNULL(
                        (
                        SELECT
                            SUM(tcs.current_quantity)
                        FROM
                            current_stocks tcs
                        WHERE
                            tcs.item_id = ti.id
                            AND tcs.deleted_at IS NULL
                    ), 0 ) AS 'cuurentStock',
                    @usedStock := IFNULL(
                        (
                        SELECT
                            SUM(tdis.number_of_units)
                        FROM
                            daily_issues_suppliers tdis
                                INNER JOIN
                            daily_issues tdi ON tdi.id = tdis.issue_id
                        WHERE
                            tdis.item_id = ti.id
                            AND tdi.confirm_status = 0
                            AND tdi.deleted_at IS NULL
                            AND tdis.deleted_at IS NULL
                    ), 0 ) AS 'usedStock',
                    CONCAT(ti.item_type, ',', ti.id, ',', ti.unit_price, ',', IF((@cuurentStock-@usedStock)>0,(@cuurentStock-@usedStock),0)) AS value
                FROM
                    items ti
                        INNER JOIN 
                    item_types tit ON tit.id = ti.item_type
                WHERE
                    ti.deleted_at IS NULL
                    AND tit.deleted_at IS NULL
                    AND tit.id NOT IN ($not_in)
                ORDER BY
                    ti.id";
        
        $items = DB::select(DB::raw($query));

        $data['items'] = $items;

        // dd($data);
        return view('Admin.daily-issue')->with('data',$data);
    }

    public function loadInsertIssues($date) {

        $user_id = Auth::user()->id;
        
        $requested_date = $date;
        $requested_month = date("Y-m", strtotime($requested_date));

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $issue_date = $date;
            $issue = DailyIssues::where('date',$issue_date)->limit(1)->get();

            if(count($issue) == 0) {

                $month_end = MonthEnd::where('month',$requested_month)->limit(1)->get();

                if ($month_end[0]->ended_status == 0) {

                    $data = array();

                    $suppliers = DB::table('suppliers AS ts')
                                ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),DB::raw('CONCAT(ts.id, ",", ts.sup_name) AS value'))
                                ->whereNull('ts.deleted_at')
                                ->get();
                    $data['suppliers'] = $suppliers;

                    $not_in = config('application.tealeaves_type').','.config('application.fertilizer_type');
                    $query="SELECT
                                ti.item_name,
                                ti.item_code,
                                @cuurentStock := IFNULL(
                                    (
                                    SELECT
                                        SUM(tcs.current_quantity)
                                    FROM
                                        current_stocks tcs
                                    WHERE
                                        tcs.item_id = ti.id
                                        AND tcs.deleted_at IS NULL
                                ), 0 ) AS 'cuurentStock',
                                @usedStock := IFNULL(
                                    (
                                    SELECT
                                        SUM(tdis.number_of_units)
                                    FROM
                                        daily_issues_suppliers tdis
                                            INNER JOIN
                                        daily_issues tdi ON tdi.id = tdis.issue_id
                                    WHERE
                                        tdis.item_id = ti.id
                                        AND tdi.confirm_status = 0
                                        AND tdi.deleted_at IS NULL
                                        AND tdis.deleted_at IS NULL
                                ), 0 ) AS 'usedStock',
                                CONCAT(ti.item_type, ',', ti.id, ',', ti.unit_price, ',', IF((@cuurentStock-@usedStock)>0,(@cuurentStock-@usedStock),0)) AS value
                            FROM
                                items ti
                                    INNER JOIN 
                                item_types tit ON tit.id = ti.item_type
                            WHERE
                                ti.deleted_at IS NULL
                                AND tit.deleted_at IS NULL
                                AND tit.id NOT IN ($not_in)
                            ORDER BY
                                ti.id";
                    
                    $items = DB::select(DB::raw($query));

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
                                        ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),'ts.sup_name','ti.item_name','ti.item_code','tdis.number_of_units','tdis.current_units_price','tdis.daily_value')
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
        else {
            return view('Admin.Loadings.no-data');
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

            $query="SELECT
                        tdis.id,
                        LPAD(ts.id,4,0) AS sup_id,
                        ts.sup_name,
                        tdis.supplier_id,
                        ti.item_name,
                        ti.item_code,
                        tdis.item_type,
                        tdis.item_id,
                        tdis.number_of_units,
                        tdis.current_units_price,
                        tdis.daily_value,
                        @cuurentStock := IFNULL(
                            (
                            SELECT
                                SUM(tcs.current_quantity)
                            FROM
                                current_stocks tcs
                            WHERE
                                tcs.item_id = ti.id
                                AND tcs.deleted_at IS NULL
                        ), 0 ) AS 'cuurentStock',
                        @usedStock := IFNULL(
                            (
                            SELECT
                                SUM(tdis2.number_of_units)
                            FROM
                                daily_issues_suppliers tdis2
                                    INNER JOIN
                                daily_issues tdi2 ON tdi2.id = tdis2.issue_id
                            WHERE
                                tdis2.item_id = ti.id
                                AND tdi2.confirm_status = 0
                                AND tdi2.deleted_at IS NULL
                                AND tdis2.deleted_at IS NULL
                                AND tdi2.id != $issue_id
                        ), 0 ) AS 'usedStock',
                        IF((@cuurentStock-@usedStock)>0,(@cuurentStock-@usedStock),0) AS actual_current_stock
                        FROM
                            items ti
                                INNER JOIN 
                            daily_issues_suppliers tdis ON tdis.item_id = ti.id
                                INNER JOIN
                            daily_issues tdi ON tdi.id = tdis.issue_id
                                INNER JOIN
                            suppliers ts ON ts.id = tdis.supplier_id
                        WHERE
                            ti.deleted_at IS NULL
                            AND tdis.deleted_at IS NULL
                            AND tdi.deleted_at IS NULL
                            AND ts.deleted_at IS NULL
                            AND tdi.id = $issue_id
                        ORDER BY
                            ti.id";
            
            $supplier_issue = DB::select(DB::raw($query));

            /* $supplier_issue = DB::table('daily_issues_suppliers AS tdis')
                                      ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                      ->join('suppliers AS ts','ts.id','tdis.supplier_id')
                                      ->join('items AS ti','ti.id','tdis.item_id')
                                      ->select('tdis.id',DB::raw('LPAD(ts.id,4,0) AS sup_id'),'ts.sup_name','tdis.supplier_id','ti.item_name','tdis.item_type','tdis.item_id','tdis.number_of_units','tdis.current_units_price','tdis.daily_value')
                                      ->where('tdi.id',$issue_id)
                                      ->whereNull('tdis.deleted_at')
                                      ->whereNull('tdi.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get(); */

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
