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
use App\Models\FertilizerIssues;
use App\Models\FertilizerIssuesSupplier;
use App\Models\MonthlyInstallment;
use App\Models\MonthEnd;

class FertilizerIssueController extends Controller {

    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Fertilizer Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),DB::raw('CONCAT(ts.id, ",", ts.sup_name) AS value'))
                       ->whereNull('ts.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        $items = DB::table('items AS ti')
                        ->join('item_types AS tit','tit.id','ti.item_type')
                        ->select(DB::raw('CONCAT(ti.item_type, ",", ti.id, ",", ti.unit_price) AS value'),'ti.item_name')
                        ->whereIn('tit.id', [config('application.fertilizer_type')])
                        ->whereNull('ti.deleted_at')
                        ->whereNull('tit.deleted_at')
                        ->get();
        $data['items'] = $items;

        // dd($data);
        return view('Admin.fertilizer-issue')->with('data',$data);
    }

    public function loadInsertFertilizerIssues($date) {

        $user_id = Auth::user()->id;

        $requested_date = $date;
        $requested_month = date("Y-m", strtotime($requested_date));

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $fertilizer_issue_date = $date;
            $fertilizer_issue = FertilizerIssues::where('date',$fertilizer_issue_date)->limit(1)->get();

            if(count($fertilizer_issue) == 0) {

                $month_end = MonthEnd::where('month',$requested_month)->limit(1)->get();

                if ($month_end[0]->ended_status == 0) {
                    
                    $data = array();

                    $suppliers = DB::table('suppliers AS ts')
                                ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),DB::raw('CONCAT(ts.id, ",", ts.sup_name) AS value'))
                                ->whereNull('ts.deleted_at')
                                ->get();
                    $data['suppliers'] = $suppliers;

                    $items = DB::table('items AS ti')
                                ->join('item_types AS tit','tit.id','ti.item_type')
                                ->select(DB::raw('CONCAT(ti.item_type, ",", ti.id, ",", ti.unit_price) AS value'),'ti.item_name')
                                ->whereIn('tit.id', [config('application.fertilizer_type')])
                                ->whereNull('ti.deleted_at')
                                ->whereNull('tit.deleted_at')
                                ->get();
                    $data['items'] = $items;

                    // dd($current_issue_month);
                    return view('Admin.Loadings.fertilizer-issue-insert')->with('data',$data);

                }
                else {
                    return view('Admin.Loadings.no-data');
                }

            }
            else {

                $fertilizer_issue_id = $fertilizer_issue[0]->id;

                $data = array();

                $data['fertilizer_issue_id'] = $fertilizer_issue[0]->id;
                $data['daily_total_value'] = $fertilizer_issue[0]->daily_total_value;
                $data['fertilizer_issue_status'] = $fertilizer_issue[0]->confirm_status;

                $supplier_issue = DB::table('fertilizer_issues_suppliers AS tfis')
                                        ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                        ->join('suppliers AS ts','ts.id','tfis.supplier_id')
                                        ->join('items AS ti','ti.id','tfis.item_id')
                                        ->select(DB::raw('LPAD(ts.id,4,0) AS sup_id'),'ts.sup_name','ti.item_name','tfis.number_of_units','tfis.current_units_price','tfis.daily_value','tfis.payment_frequency')
                                        ->where('tfi.id',$fertilizer_issue_id)
                                        ->whereNull('tfis.deleted_at')
                                        ->whereNull('tfi.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('ti.deleted_at')
                                        ->get();
                $data['suppliers'] = $supplier_issue;
                // dd($data);
                return view('Admin.Loadings.fertilizer-issue-view')->with('data',$data);
            }
        }
        else {
            return view('Admin.Loadings.no-data');
        }
    }

    public function loadEditFertilizerIssues($id) {

        $user_id = Auth::user()->id;

        $fertilizer_issue = FertilizerIssues::where('id',$id)->where('confirm_status',0)->limit(1)->get();

        if(count($fertilizer_issue) > 0) {

            $fertilizer_issue_id = $fertilizer_issue[0]->id;

            $data = array();

            $data['fertilizer_issue_id'] = $fertilizer_issue_id;
            $data['daily_total_value'] = $fertilizer_issue[0]->daily_total_value;

            $supplier_issue = DB::table('fertilizer_issues_suppliers AS tfis')
                                      ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                      ->join('suppliers AS ts','ts.id','tfis.supplier_id')
                                      ->join('items AS ti','ti.id','tfis.item_id')
                                      ->select('tfis.id',DB::raw('LPAD(ts.id,4,0) AS sup_id'),'ts.sup_name','tfis.supplier_id','ti.item_name','tfis.item_type','tfis.item_id','tfis.number_of_units','tfis.current_units_price','tfis.daily_value','tfis.payment_frequency',
                                                DB::raw('IF(tfis.payment_frequency=1, "One Month", IF(tfis.payment_frequency=2, "Two Months", IF(tfis.payment_frequency=3, "Three Months", "-"))) AS frequency'))
                                      ->where('tfi.id',$fertilizer_issue_id)
                                      ->whereNull('tfis.deleted_at')
                                      ->whereNull('tfi.deleted_at')
                                      ->whereNull('ts.deleted_at')
                                      ->whereNull('ti.deleted_at')
                                      ->get();
            $data['fertilizer_supplier_issues'] = $supplier_issue;
            $data['actual_supplier_count'] = count($supplier_issue);

            // dd($data);
            return view('Admin.Loadings.fertilizer-issue-edit')->with('data',$data);

        }

    }
    
    public function insertFertilizerIssues(Request $request) {

        $user_id = Auth::user()->id;
        $fertilizer_issue_date = $request->fertilizer_issue_date;
        $fertilizer_issue_array = $request->fertilizer_issue_array;

        $is_exist = FertilizerIssues::where('date',$fertilizer_issue_date)->limit(1)->get();

        if(count($is_exist) == 0) {

            try {

                DB::beginTransaction();

                $issue = new FertilizerIssues();
                $issue->date = $fertilizer_issue_date;
                $issue->daily_total_value = $request->daily_total_value;
                $issue->user_id = $user_id;
                $issue->save();
                
                $issue_id = $issue->id;

                //  INSERT SUPPLIER DAILY ISSUE DETAILS
                $supplier_array =json_decode($fertilizer_issue_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $fertilizer_issue_supplier = new FertilizerIssuesSupplier();
                        $fertilizer_issue_supplier->fertilizer_issue_id = $issue_id;
                        $fertilizer_issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                        $fertilizer_issue_supplier->item_type = $supplier_array[$i]->item_type;
                        $fertilizer_issue_supplier->item_id = $supplier_array[$i]->item_id;
                        $fertilizer_issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                        $fertilizer_issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                        $fertilizer_issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                        $fertilizer_issue_supplier->payment_frequency = $supplier_array[$i]->payment_frequency;
                        $fertilizer_issue_supplier->save();
                        
                    }
                }

                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily fertilizer issue data successfully inserted !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily fertilizer issue data not successfully inserted !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'There is a fertilizer issue assign this day !',
            ]);
        }

    }
    
    public function editFertilizerIssues(Request $request) {

        $user_id = Auth::user()->id;
        $fertilizer_issue_id = $request->fertilizer_issue_id;
        $fertilizer_issue_array = $request->fertilizer_issue_array;
        $removed_supplier_issues = $request->removed_supplier_issues;

        $is_exist = FertilizerIssues::where('id',$fertilizer_issue_id)->where('confirm_status',0)->limit(1)->get();

        if(count($is_exist) > 0) {

            try {

                DB::beginTransaction();

                $fertilizer_issue = FertilizerIssues::find($fertilizer_issue_id);
                $fertilizer_issue->daily_total_value = $request->daily_total_value;
                $fertilizer_issue->edited_status = 1;
                $fertilizer_issue->save();

                //  UPDATE SUPPLIER DAILY ISSUES DETAILS
                $remove_supplier_issues_array =json_decode($removed_supplier_issues);

                if(count($remove_supplier_issues_array) > 0) {
                    for ($j = 0; $j < count($remove_supplier_issues_array); $j++) {

                        $daily_supplier = FertilizerIssuesSupplier::find($remove_supplier_issues_array[$j]);
                        $daily_supplier->deleted_at = Carbon::now();
                        $daily_supplier->save();                        
                    }
                }

                $supplier_array =json_decode($fertilizer_issue_array);

                if (count($supplier_array) > 0) {

                    for ($i = 0; $i < count($supplier_array); $i++) {
        
                        $sup_fertilizer_issue_id = $supplier_array[$i]->sup_col_id;

                        if ($sup_fertilizer_issue_id == 0) {
                            $fertilizer_issue_supplier = new FertilizerIssuesSupplier();
                            $fertilizer_issue_supplier->fertilizer_issue_id = $fertilizer_issue_id;
                            $fertilizer_issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $fertilizer_issue_supplier->item_type = $supplier_array[$i]->item_type;
                            $fertilizer_issue_supplier->item_id = $supplier_array[$i]->item_id;
                            $fertilizer_issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $fertilizer_issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $fertilizer_issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $fertilizer_issue_supplier->payment_frequency = $supplier_array[$i]->payment_frequency;
                            $fertilizer_issue_supplier->save();
                        }
                        else {
                            $fertilizer_issue_supplier = FertilizerIssuesSupplier::find($sup_fertilizer_issue_id);
                            $fertilizer_issue_supplier->supplier_id = $supplier_array[$i]->supplier_id;
                            $fertilizer_issue_supplier->item_type = $supplier_array[$i]->item_type;
                            $fertilizer_issue_supplier->item_id = $supplier_array[$i]->item_id;
                            $fertilizer_issue_supplier->current_units_price = $supplier_array[$i]->current_price;
                            $fertilizer_issue_supplier->number_of_units = $supplier_array[$i]->no_of_units;
                            $fertilizer_issue_supplier->daily_value = $supplier_array[$i]->daily_value;
                            $fertilizer_issue_supplier->payment_frequency = $supplier_array[$i]->payment_frequency;
                            $fertilizer_issue_supplier->save();
                        }
                        
                    }
                }
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily fertilizer issue data successfully edited !',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily fertilizer issue data not successfully edited !',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'You can not edit these fertilizer issue data !',
            ]);
        }

    }
    
    public function confirmFertilizerIssues(Request $request) {

        $user_id = Auth::user()->id;
        $fertilizer_issue_id = $request->fertilizer_issue_id;

        $current_issue = FertilizerIssues::where('id',$fertilizer_issue_id)->where('confirm_status',0)->limit(1)->get();

        if(count($current_issue) > 0) {

            $ending_month = $current_issue[0]->date;

            try {

                DB::beginTransaction();

                $month = array();
                $month[1] = date("Y-m", strtotime($ending_month));
                $month[2] = date('Y-m',strtotime('first day of +1 month',strtotime($ending_month)));
                $month[3] = date('Y-m',strtotime('first day of +2 month',strtotime($ending_month)));

                $sup_fertilizer_issues =DB::table('fertilizer_issues_suppliers AS tfis')
                                    ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                    ->select('tfis.id','tfis.supplier_id','tfis.daily_value','tfis.payment_frequency')
                                    ->where('tfi.id',$fertilizer_issue_id)
                                    ->whereNull('tfis.deleted_at')
                                    ->whereNull('tfi.deleted_at')
                                    ->get();

                foreach ($sup_fertilizer_issues as $issue) {

                    $supplier = $issue->supplier_id;
                    $frequency = $issue->payment_frequency;
                    $reference = $issue->id;
                    $remarks = "fertilizer";
                    $value =  $issue->daily_value;

                    for ($i=1; $i <= $frequency; $i++) { 
                        $monthly_installment = new MonthlyInstallment();
                        $monthly_installment->supplier_id = $supplier;
                        $monthly_installment->month = $month[$i];
                        $monthly_installment->installment = ($value/$frequency);
                        $monthly_installment->reference = $reference;
                        $monthly_installment->remarks = $remarks;
                        $monthly_installment->deducted_status = 0;
                        $monthly_installment->save();
                    }

                }

                $fertilizer_issue = FertilizerIssues::find($fertilizer_issue_id);
                $fertilizer_issue->confirm_status = 1;
                $fertilizer_issue->save();
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Daily fertilizer issue data successfully confirmed',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Daily fertilizer issue data not successfully confirmed',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'This fertilizer issue is has allready confirmed',
            ]);
        }

    }

}
