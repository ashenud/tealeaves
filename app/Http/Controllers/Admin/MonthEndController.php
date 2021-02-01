<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Models\DailyCollection;
use App\Models\DailyIssues;
use App\Models\MonthEnd;
use App\Models\MonthEndSupplier;
use App\Models\FertilizerIssues;
use App\Models\FertilizerIssuesSupplier;
use App\Models\MonthlyInstallment;
use App\Models\DebtorDetails;
use App\Models\Supplier;

class MonthEndController extends Controller {

    public function index() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Month End';

        return view('Admin.month-end')->with('data',$data);
    }

    public function monthEndDatatable(Request $request ) {

        $current_month = date("Y-m");

        if ($request->ajax()) {
            $data = MonthEnd::select('id','month','user_id','ended_status',
                                    DB::raw('IFNULL(ended_date, "Month not ended yet") AS ended_date'))
                             ->where('month','<',$current_month)      
                             ->whereNotIn('month', ['2020-10'])
                             ->orderBy('month','DESC');
            return Datatables::of($data)
                    ->addColumn('create', function($data){
     
                        $last_month = date('Y-m',strtotime('first day of previous month',strtotime($data->month)));
                        $last_data = MonthEnd::select('ended_status')
                                              ->where('month','=',$last_month)                          
                                              ->limit(1)
                                              ->get();

                        if($last_data[0]->ended_status == 1) {
                            
                            if ($data->ended_status == 1) {
                                $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="fas fa-check-square"></i></i></a>';
                            }
                            else {
                                $btn = '<a class="btn btn-sheding btn-sm" onclick="createMonthEnd('.$data->id.')" type="button"><i class="far fa-check-square"></i></a>';
                            }
                        }
                        else {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="fas fa-check-square"></i></i></a>';
                        }
                        
                        return $btn;
                    })
                    ->addColumn('print', function($data){
     
                        if ($data->ended_status == 0) {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="fas fa-print"></i></a>';
                        }
                        else {
                            $btn = '<a class="btn btn-sheding btn-sm" onclick="printBill('.$data->id.')" type="button"><i class="fas fa-print"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['create','print'])
                    ->make(true);
        }
        
        return view('Admin.month-end');
    }
    
    public function createMonthEnd(Request $request) {

        $user_id = Auth::user()->id;

        $month_end_id = $request->month_end_id;

        $month_end = MonthEnd::where('id',$month_end_id)->where('ended_status',0)->limit(1)->get();

        if(count($month_end) > 0) {

            $ending_month = $month_end[0]->month;

            try {

                DB::beginTransaction();

                /* CONFIRM ALL DAILY COLLECTIONS */
                DailyCollection::where(DB::raw('DATE_FORMAT(date, "%Y-%m")'),'=',$ending_month)
                               ->where(['confirm_status' => 0])
                              ->update(['confirm_status' => 1]);

                /* CONFIRM ALL DAILY ISSUES */
                DailyIssues::where(DB::raw('DATE_FORMAT(date, "%Y-%m")'),'=',$ending_month)
                           ->where(['confirm_status' => 0])
                          ->update(['confirm_status' => 1]);

                /* CONFIRM ALL FERTILIZER ISSUES */
                $month = array();
                $month[1] = date("Y-m", strtotime($ending_month));
                $month[2] = date('Y-m',strtotime('first day of +1 month',strtotime($ending_month)));
                $month[3] = date('Y-m',strtotime('first day of +2 month',strtotime($ending_month)));

                $sup_fertilizer_issues =DB::table('fertilizer_issues_suppliers AS tfis')
                                    ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                    ->select('tfis.id','tfis.supplier_id','tfis.daily_value','tfis.payment_frequency')
                                    ->where(DB::raw('DATE_FORMAT(tfi.date, "%Y-%m")'),'=',$ending_month)
                                    ->where(['tfi.confirm_status' => 0])
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

                FertilizerIssues::where(DB::raw('DATE_FORMAT(date, "%Y-%m")'),'=',$ending_month)
                                ->where(['confirm_status' => 0])
                               ->update(['confirm_status' => 1]);

                /* DEDUCT ALL MONTHLY INSTALLMENTS */
                MonthlyInstallment::where('month','=',$ending_month)
                                  ->where(['deducted_status' => 0])
                                 ->update(['deducted_status' => 1]);
                               
                /* DATA CALCULATIONS */
                $supplier_data = array();

                $monthly_colection =DB::table('daily_collection_suppliers AS tdcs')
                                    ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                    ->select('tdcs.supplier_id',DB::raw('IFNULL(SUM(tdcs.number_of_units),0) AS total_units'),DB::raw('IFNULL(SUM(tdcs.daily_amount),0) AS total_value'),DB::raw('IFNULL(SUM(tdcs.delivery_cost),0) AS total_delivery_cost'),DB::raw('IFNULL(SUM(tdcs.daily_value),0) AS net_value'))
                                    ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$ending_month)
                                    ->where('tdc.confirm_status', '=', 1)
                                    ->whereNull('tdcs.deleted_at')
                                    ->whereNull('tdc.deleted_at')
                                    ->groupBy('tdcs.supplier_id')
                                    ->get();

                foreach ($monthly_colection as $collection) {
                    $supplier_data[$collection->supplier_id]['tea_units'] = $collection->total_units;
                    $supplier_data[$collection->supplier_id]['delivery_cost'] = $collection->total_delivery_cost;
                    $supplier_data[$collection->supplier_id]['total_earnings'] = $collection->total_value;
                    $supplier_data[$collection->supplier_id]['net_earnings'] = $collection->net_value;
                }

                $monthly_issues =DB::table('daily_issues_suppliers AS tdis')
                                    ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                    ->select('tdis.supplier_id',DB::raw('IFNULL(SUM(tdis.daily_value),0) AS net_cost'))
                                    ->where(DB::raw('DATE_FORMAT(tdi.date, "%Y-%m")'),'=',$ending_month)
                                    ->where('tdi.confirm_status', '=', 1)
                                    ->whereNull('tdis.deleted_at')
                                    ->whereNull('tdi.deleted_at')
                                    ->groupBy('tdis.supplier_id')
                                    ->get();

                foreach ($monthly_issues as $issues) {
                    $supplier_data[$issues->supplier_id]['item_cost'] = $issues->net_cost;
                }

                $monthly_installments =DB::table('monthly_installments AS tmi')
                                    ->select('tmi.supplier_id',DB::raw('IFNULL(SUM(tmi.installment),0) AS total_installments'))
                                    ->where('tmi.month','=',$ending_month)
                                    ->where('tmi.deducted_status','=', 1)
                                    ->whereNull('tmi.deleted_at')
                                    ->groupBy('tmi.supplier_id')
                                    ->get();

                foreach ($monthly_installments as $installment) {
                    $supplier_data[$installment->supplier_id]['installments'] = $installment->total_installments;
                }

                $debtor_details =DB::table('debtor_details AS tdd')
                                    ->select('tdd.supplier_id',DB::raw('IFNULL(SUM(tdd.amount),0) AS sup_credit'))
                                    ->where('tdd.relevant_month','=',$ending_month)
                                    ->where('tdd.forwarded_status','=', 0)
                                    ->whereNull('tdd.deleted_at')
                                    ->groupBy('tdd.supplier_id')
                                    ->get();

                foreach ($debtor_details as $debtors) {
                    $supplier_data[$debtors->supplier_id]['forwarded_credit'] = $debtors->sup_credit;
                }

                /* INSERT MONTH END SUPLIER DATA */
                if(count($supplier_data) > 0) {

                    foreach ($supplier_data as $supplier_id => $sup_data) {

                        $total_earnings = 0;
                        $total_cost = 0;
                        $total_installment = 0;
                        $forwarded_credit = 0;

                        if(isset($sup_data['net_earnings'])) {
                            $total_earnings = $sup_data['net_earnings'];
                        }
                        if(isset($sup_data['item_cost'])) {
                            $total_cost = $sup_data['item_cost'];
                        }
                        if(isset($sup_data['installments'])) {
                            $total_installment = $sup_data['installments'];
                        }
                        if(isset($sup_data['forwarded_credit'])) {
                            $forwarded_credit = $sup_data['forwarded_credit'];
                        }
                        
                        $total_outstanding = $total_earnings - ($total_cost + $total_installment + $forwarded_credit);

                        if($total_outstanding >= 0) {
                            $current_income = $total_outstanding;
                            $current_credit = 0;
                        }
                        else {
                            $current_income = 0;
                            $current_credit = 0 - $total_outstanding;
                        }

                        /* FORWARD CURRENT CREDIT TO NEXT MONTH IF EXISTS*/
                        if($current_credit>0) {
                            $next_month = date('Y-m',strtotime('first day of +1 month',strtotime($ending_month)));

                            $debtor_details = new DebtorDetails();
                            $debtor_details->supplier_id = $supplier_id;
                            $debtor_details->relevant_month = $next_month;
                            $debtor_details->amount = $current_credit;
                            $debtor_details->forwarded_status = 0;
                            $debtor_details->save();
                        }

                        $month_end_supplier = new MonthEndSupplier();
                        $month_end_supplier->month_end_id = $month_end_id;
                        $month_end_supplier->supplier_id = $supplier_id;
                        $month_end_supplier->total_earnings = $total_earnings;
                        $month_end_supplier->total_cost = $total_cost;
                        $month_end_supplier->total_installment = $total_installment;
                        $month_end_supplier->forwarded_credit = $forwarded_credit;
                        $month_end_supplier->current_income = $current_income;
                        $month_end_supplier->current_credit = $current_credit;
                        $month_end_supplier->save();

                    }

                }

                /* MARK DEBTORS AMOUNTS AS DEDUCTED */
                DebtorDetails::where('relevant_month','=',$ending_month)
                             ->where(['forwarded_status' => 0])
                            ->update(['forwarded_status' => 1]);

                /* MARK MONTH END AS CREATED */
                $today = date("Y-m-d");
                MonthEnd::where('id','=',$month_end_id)
                       ->update(['ended_date' => $today,'user_id' => $user_id,'ended_status' => 1]);                
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Month end has successfully created',
                    'test' => $supplier_data,
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Month end has not successfully created',
                    'error' => $e,
                ]);
            }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'This month end has allready created',
            ]);
        }

    }

}
