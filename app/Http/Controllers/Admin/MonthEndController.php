<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\DailyCollection;
use App\Models\DailyIssues;
use App\Models\MonthEnd;
use App\Models\MonthEndSupplier;
use App\Models\FertilizerIssues;
use App\Models\FertilizerIssuesSupplier;
use App\Models\MonthlyInstallment;
use App\Models\DebtorDetails;
use App\Models\Supplier;
use App\Models\CurrentStock;

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
                             ->whereNotIn('month', [config('application.previous_month')])
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

                /* UPDATE CURRENT ITEM STOCK */
                $sup_issue_items =DB::table('daily_issues_suppliers AS tdis')
                                        ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                        ->select('tdis.item_id',DB::raw('IFNULL(SUM(tdis.number_of_units),0) AS issued_qty'))
                                        ->where(DB::raw('DATE_FORMAT(tdi.date, "%Y-%m")'),'=',$ending_month)
                                        ->where('tdi.confirm_status', '=', 0)
                                        ->whereNull('tdis.deleted_at')
                                        ->whereNull('tdi.deleted_at')
                                        ->groupBy('tdis.item_id')
                                        ->get();

                foreach ($sup_issue_items as $item) {

                    $issued_qty = $item->issued_qty;

                    $current_stock = CurrentStock::where('item_id', $item->item_id)->get();

                    foreach ($current_stock as $stock) {
                        if($issued_qty > 0){
                            if($stock->current_quantity >= $issued_qty){
                                CurrentStock::where('id',$stock->id)
                                            ->decrement('current_quantity' , $issued_qty);
                                $issued_qty=0;
                            }else if($stock->current_quantity < $issued_qty){
                                CurrentStock::where('id',$stock->id)
                                            ->update(['current_quantity' => 0]);
                                $issued_qty -= $stock->current_quantity;
                            }
                        }else{
                            break;
                        }
                    }

                }

                /* CONFIRM ALL DAILY ISSUES */
                DailyIssues::where(DB::raw('DATE_FORMAT(date, "%Y-%m")'),'=',$ending_month)
                           ->where(['confirm_status' => 0])
                          ->update(['confirm_status' => 1]);

                 /* UPDATE CURRENT FERTILIZER STOCK */
                $sup_issue_fertilizers =DB::table('fertilizer_issues_suppliers AS tfis')
                                            ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                            ->select('tfis.item_id',DB::raw('IFNULL(SUM(tfis.number_of_units),0) AS issued_qty'))
                                            ->where(DB::raw('DATE_FORMAT(tfi.date, "%Y-%m")'),'=',$ending_month)
                                            ->where('tfi.confirm_status', '=', 0)
                                            ->whereNull('tfis.deleted_at')
                                            ->whereNull('tfi.deleted_at')
                                            ->groupBy('tfis.item_id')
                                            ->get();

                foreach ($sup_issue_fertilizers as $fertilizer) {

                    $issued_qty = $fertilizer->issued_qty;

                    $current_stock = CurrentStock::where('item_id', $fertilizer->item_id)->get();

                    foreach ($current_stock as $stock) {
                        if($issued_qty > 0){
                            if($stock->current_quantity >= $issued_qty){
                                CurrentStock::where('id',$stock->id)
                                            ->decrement('current_quantity' , $issued_qty);
                                $issued_qty=0;
                            }else if($stock->current_quantity < $issued_qty){
                                CurrentStock::where('id',$stock->id)
                                            ->update(['current_quantity' => 0]);
                                $issued_qty -= $stock->current_quantity;
                            }
                        }else{
                            break;
                        }
                    }

                }

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

    public function printBulkBills($id) {

        $data = array();
        $user_id = Auth::user()->user_id;
        $month_end_id = $id;
        $month_end = MonthEnd::where('id',$month_end_id)->where('ended_status',1)->limit(1)->get();
        if(count($month_end) > 0) {
            
            $requested_month = $month_end[0]->month;

            $data['month' ]= date('M-Y',strtotime($requested_month));

            $supplier_info =DB::table('month_end_suppliers AS tmes')
                            ->join('month_ends AS tme','tme.id','tmes.month_end_id')
                            ->join('suppliers AS ts','ts.id','tmes.supplier_id')
                            ->join('routes AS tr','tr.id','ts.route_id')
                            ->select('ts.id','ts.sup_name','ts.sup_no','tr.route_name',DB::raw('IFNULL(tmes.total_cost,0) AS total_cost'),DB::raw('IFNULL(tmes.total_installment,0) AS total_installment'),DB::raw('IFNULL(tmes.forwarded_credit,0) AS forwarded_credit'),DB::raw('IFNULL(tmes.current_income,0) AS current_income'),DB::raw('IFNULL(tmes.current_credit,0) AS current_credit'))
                            ->where('tme.id','=',$month_end_id)
                            ->where('tme.ended_status', '=', 1)
                            ->whereNull('tmes.deleted_at')
                            ->whereNull('tme.deleted_at')
                            ->whereNull('ts.deleted_at')
                            ->whereNull('tr.deleted_at')
                            ->get();
            
            if(count($supplier_info) > 0) {

                foreach ($supplier_info as $supplier) {
                    $data['supplier_data'][$supplier->id]['supplier_id'] = $supplier->id;
                    $data['supplier_data'][$supplier->id]['supplier_no'] = $supplier->sup_no;
                    $data['supplier_data'][$supplier->id]['supplier_name'] = $supplier->sup_name;
                    $data['supplier_data'][$supplier->id]['route_name'] = $supplier->route_name;
                    $data['supplier_data'][$supplier->id]['total_issues'] = $supplier->total_cost;
                    $data['supplier_data'][$supplier->id]['total_installment'] = $supplier->total_installment;
                    $data['supplier_data'][$supplier->id]['forwarded_credit'] = $supplier->forwarded_credit;
                    $data['supplier_data'][$supplier->id]['current_income'] = $supplier->current_income;
                    $data['supplier_data'][$supplier->id]['current_credit'] = $supplier->current_credit;
                }

                $monthly_colection =DB::table('daily_collection_suppliers AS tdcs')
                                        ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                        ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                        ->select('tdcs.supplier_id',DB::raw('MIN(tdcs.current_units_price) AS current_units_price'),DB::raw('IFNULL(SUM(tdcs.number_of_units),0) AS total_units'),DB::raw('IFNULL(SUM(tdcs.daily_amount),0) AS total_value'),DB::raw('IFNULL(SUM(tdcs.delivery_cost),0) AS total_delivery_cost'),DB::raw('IFNULL(SUM(tdcs.daily_value),0) AS net_value'))
                                        ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tdc.confirm_status', '=', 1)
                                        ->whereNull('tdcs.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('tdc.deleted_at')
                                        ->groupBy('tdcs.supplier_id')
                                        ->get();

                foreach ($monthly_colection as $collection) {
                    $data['supplier_data'][$collection->supplier_id]['current_units_price'] = $collection->current_units_price;
                    $data['supplier_data'][$collection->supplier_id]['tea_units'] = $collection->total_units;
                    $data['supplier_data'][$collection->supplier_id]['delivery_cost'] = $collection->total_delivery_cost;
                    $data['supplier_data'][$collection->supplier_id]['total_earnings'] = $collection->total_value;
                    $data['supplier_data'][$collection->supplier_id]['net_earnings'] = $collection->net_value;
                }

                $daily_colection =DB::table('daily_collection_suppliers AS tdcs')
                                        ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                        ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                        ->select('tdc.date','tdcs.supplier_id','tdcs.number_of_units')
                                        ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tdc.confirm_status', '=', 1)
                                        ->whereNull('tdcs.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('tdc.deleted_at')
                                        ->get();

                $start    = new DateTime(date('Y-m-01',strtotime($requested_month)));
                $end      = new DateTime(date('Y-m-t',strtotime($requested_month)));
                $end->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 day');
                $period   = new DatePeriod($start, $interval, $end);

                foreach ($period as $dt) {                    
                    foreach ($daily_colection as $collection) {
                        if($collection->date == $dt->format("Y-m-d")) {
                            $data['supplier_data'][$collection->supplier_id]['daily_data'][intval($dt->format("d"))] = $collection->number_of_units;
                        }                    
                    }
                }

                $monthly_issues =DB::table('daily_issues_suppliers AS tdis')
                                        ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                        ->join('suppliers AS ts','ts.id','tdis.supplier_id')
                                        ->select('tdis.supplier_id','tdis.item_type',DB::raw('IFNULL(SUM(tdis.daily_value),0) AS net_cost'))
                                        ->where(DB::raw('DATE_FORMAT(tdi.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tdi.confirm_status', '=', 1)
                                        ->whereNull('tdis.deleted_at')
                                        ->whereNull('tdi.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->groupBy('tdis.supplier_id','tdis.item_type')
                                        ->get();

                foreach ($monthly_issues as $issues) {
                    if ($issues->item_type == config('application.teabag_type')) {
                        $data['supplier_data'][$issues->supplier_id]['issue_teabag'] = $issues->net_cost;
                    }
                    else if ($issues->item_type == config('application.dolamite_type')) {
                        $data['supplier_data'][$issues->supplier_id]['issue_dolamite'] = $issues->net_cost;
                    }
                    else if ($issues->item_type == config('application.dolamite_type')) {
                        $data['supplier_data'][$issues->supplier_id]['issue_chemical'] = $issues->net_cost;
                    }
                }

                $monthly_installments =DB::table('monthly_installments AS tmi')
                                        ->join('suppliers AS ts','ts.id','tmi.supplier_id')
                                        ->select('tmi.supplier_id','tmi.remarks',DB::raw('IFNULL(SUM(tmi.installment),0) AS total_installments'))
                                        ->where('tmi.month','=',$requested_month)
                                        ->where('tmi.deducted_status','=', 1)
                                        ->whereNull('tmi.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->groupBy('tmi.supplier_id','tmi.remarks')
                                        ->get();

                foreach ($monthly_installments as $installment) {
                    if($installment->remarks == 'fertilizer') {
                        $data['supplier_data'][$installment->supplier_id]['installment_fertilizer'] = $installment->total_installments;
                    }
                    else if($installment->remarks == 'advance') {
                        $data['supplier_data'][$installment->supplier_id]['installment_advance'] = $installment->total_installments;
                    }
                    else if($installment->remarks == 'loan') {
                        $data['supplier_data'][$installment->supplier_id]['installment_loan'] = $installment->total_installments;
                    }
                }

                $debtor_details =DB::table('debtor_details AS tdd')
                                        ->join('suppliers AS ts','ts.id','tdd.supplier_id')
                                        ->select('tdd.supplier_id',DB::raw('IFNULL(SUM(tdd.amount),0) AS sup_credit'))
                                        ->where('tdd.relevant_month','=',$requested_month)
                                        ->where('tdd.forwarded_status','=', 1)
                                        ->whereNull('tdd.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->groupBy('tdd.supplier_id')
                                        ->get();

                foreach ($debtor_details as $debtors) {
                    $data['supplier_data'][$debtors->supplier_id]['forwarded_credit'] = $debtors->sup_credit;
                }

                $fertilizer_credits =DB::table('fertilizer_issues AS tfi')
                                        ->join('fertilizer_issues_suppliers AS tfis','tfis.fertilizer_issue_id','tfi.id')
                                        ->join('monthly_installments AS tmi','tmi.reference','tfis.id')
                                        ->join('suppliers AS ts','ts.id','tmi.supplier_id')
                                        ->select('tmi.supplier_id',DB::raw('IFNULL(SUM(tmi.installment),0) AS total_value'))
                                        ->where(DB::raw('DATE_FORMAT(tfi.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tfi.confirm_status','=', 1)
                                        ->where('tmi.deducted_status','=', 0)
                                        ->where('tmi.remarks','=', 'fertilizer')
                                        ->whereNull('tfi.deleted_at')
                                        ->whereNull('tfis.deleted_at')
                                        ->whereNull('tmi.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->groupBy('tmi.supplier_id')
                                        ->get();

                foreach ($fertilizer_credits as $fert_credt) {
                    $data['supplier_data'][$fert_credt->supplier_id]['fert_credt'] = $fert_credt->total_value;
                }

                // return response()->json([
                //     'result' => true,
                //     'message' => 'This month end has allready created',
                //     'data' => $data,
                // ]);
                
                $pdfname = date('YM',strtotime($requested_month));
                $pdfname = strtoupper($pdfname);
                $pdfname .= "-MONTHLY-BILL.pdf";
                $pdf = app('dompdf.wrapper')->loadView('templates.monthly-bill', ['data' => $data])->setPaper('a4', 'landscape');
                return $pdf->stream($pdfname);

            }
        else {
            return response()->json([
                'result' => false,
                'message' => 'Not any data for this month !',
            ]);
        }

        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'This month end has not been created yet',
            ]);
        }

        
        
    }

}
