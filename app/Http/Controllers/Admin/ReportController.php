<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\MonthEnd;
use App\Models\DailyCollection;

class ReportController extends Controller {   

    public function auditTrail() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Audit Trail';

        // dd($data);
        return view('Admin.audit-trail')->with('data',$data);
    }

    public function auditTrailTable($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();
            $data['audit_month'] = $requested_month;
                        
            $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',1)->limit(1)->get();
            if(count($month_end) > 0) {                

                $daily_colection =DB::table('daily_collection_suppliers AS tdcs')
                                        ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                        ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                        ->select('tdc.date','ts.sup_name','ts.sup_no','tdcs.supplier_id','tdcs.number_of_units')
                                        ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tdc.confirm_status', '=', 1)
                                        ->whereNull('tdcs.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('tdc.deleted_at')
                                        ->orderBy('ts.id')
                                        ->get();

                $start    = new DateTime(date('Y-m-01',strtotime($requested_month)));
                $end      = new DateTime(date('Y-m-t',strtotime($requested_month)));
                $end->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 day');
                $period   = new DatePeriod($start, $interval, $end);

                foreach ($period as $dt) {                    
                    foreach ($daily_colection as $collection) {
                        if($collection->date == $dt->format("Y-m-d")) {
                            $data['supplier_data'][$collection->supplier_id]['supplier_name'] = $collection->sup_name;
                            $data['supplier_data'][$collection->supplier_id]['supplier_no'] = $collection->sup_no;
                            $data['supplier_data'][$collection->supplier_id]['daily_data'][intval($dt->format("d"))] = $collection->number_of_units;
                        }                    
                    }
                }
                if(isset($data['supplier_data'])) {
                    ksort($data['supplier_data']);
    
                    // dd($data);
                    return view('Admin.Loadings.audit-trail-table')->with('data',$data);
                }
                else {
                    return view('Admin.Loadings.no-data');
                }
            }
            else {
                return view('Admin.Loadings.no-data');
            }

        }
        else {
            return view('Admin.Loadings.no-data');
        }

    }

    public function printAuditTrail($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;
        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();
            $data['audit_month'] = $requested_month;
                        
            $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',1)->limit(1)->get();
            if(count($month_end) > 0) {                

                $daily_colection =DB::table('daily_collection_suppliers AS tdcs')
                                        ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                        ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                        ->select('tdc.date','ts.sup_name','ts.sup_no','tdcs.supplier_id','tdcs.number_of_units')
                                        ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                        ->where('tdc.confirm_status', '=', 1)
                                        ->whereNull('tdcs.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('tdc.deleted_at')
                                        ->orderBy('ts.id')
                                        ->get();

                $start    = new DateTime(date('Y-m-01',strtotime($requested_month)));
                $end      = new DateTime(date('Y-m-t',strtotime($requested_month)));
                $end->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 day');
                $period   = new DatePeriod($start, $interval, $end);

                foreach ($period as $dt) {                    
                    foreach ($daily_colection as $collection) {
                        if($collection->date == $dt->format("Y-m-d")) {
                            $data['supplier_data'][$collection->supplier_id]['supplier_name'] = $collection->sup_name;
                            $data['supplier_data'][$collection->supplier_id]['supplier_no'] = $collection->sup_no;
                            $data['supplier_data'][$collection->supplier_id]['daily_data'][intval($dt->format("d"))] = $collection->number_of_units;
                        }                    
                    }
                }

                ksort($data['supplier_data']);

                // dd($data);
                $pdfname = date('YM',strtotime($requested_month));
                $pdfname = strtoupper($pdfname);
                $pdfname .= "-AUDIT-TRAIL.pdf";
                $pdf = app('dompdf.wrapper')->loadView('templates.audit-trail', ['data' => $data])->setPaper('a4', 'landscape');
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
                'message' => 'Not any data for this month !',
            ]);
        }

    }

    public function salesReport() {

        $user_id = Auth::user()->user_id;
        $data = array();
        $data['page_title'] = 'Sales Report';

        // dd($data);
        return view('Admin.sales-report')->with('data',$data);
    }

    public function salesReportTable($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();
            $data['sales_month'] = $requested_month;
                        
            $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',1)->limit(1)->get();
            if(count($month_end) > 0) {

                $monthly_sale =DB::table('month_end_suppliers AS tmes')
                                        ->join('month_ends AS tme','tme.id','tmes.month_end_id')
                                        ->join('suppliers AS ts','ts.id','tmes.supplier_id')
                                        ->select('ts.sup_name','ts.sup_no','tmes.supplier_id','tmes.total_earnings','tmes.total_cost','tmes.total_installment','tmes.forwarded_credit','tmes.current_income','tmes.current_credit')
                                        ->where('tmes.month_end_id',$month_end[0]->id)
                                        ->where('tme.ended_status', '=', 1)
                                        ->whereNull('tmes.deleted_at')
                                        ->whereNull('ts.deleted_at')
                                        ->whereNull('tme.deleted_at')
                                        ->orderBy('ts.id')
                                        ->get();
                
                $grand_colection = 0;
                $grand_earnings = 0;
                $grand_deduction = 0;
                $grand_income = 0;
                foreach ($monthly_sale as $sale) {

                    $daily_colection =DB::table('daily_collection_suppliers AS tdcs')
                                            ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                            ->select(DB::raw('SUM(tdcs.number_of_units) AS num_of_units'),DB::raw('IFNULL(SUM(tdcs.delivery_cost),0) AS delivery_cost'))
                                            ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                            ->where('tdcs.supplier_id','=',$sale->supplier_id)
                                            ->where('tdc.confirm_status', '=', 1)
                                            ->whereNull('tdcs.deleted_at')
                                            ->whereNull('tdc.deleted_at')
                                            ->groupBy('tdcs.supplier_id')
                                            ->limit(1)
                                            ->get();

                    $data['supplier_data'][$sale->supplier_id]['supplier_name'] = $sale->sup_name;
                    $data['supplier_data'][$sale->supplier_id]['supplier_no'] = $sale->sup_no;
                    $num_of_units = 0;
                    $delivery_cost = 0;
                    foreach ($daily_colection as $collectoin) { 
                        $num_of_units += $collectoin->num_of_units;
                        $delivery_cost += $collectoin->delivery_cost;
                    }                    
                    $grand_colection += $num_of_units; 
                    $data['supplier_data'][$sale->supplier_id]['number_of_units'] = $num_of_units;
                    $data['supplier_data'][$sale->supplier_id]['total_earnings'] = $sale->total_earnings + $delivery_cost;
                    $grand_earnings += $sale->total_earnings + $delivery_cost;
                    $total_deduction = $sale->total_cost + $sale->total_installment + $sale->forwarded_credit + $delivery_cost;
                    $grand_deduction += $total_deduction;
                    $data['supplier_data'][$sale->supplier_id]['total_deduction'] = $total_deduction;
                    $data['supplier_data'][$sale->supplier_id]['current_income'] = $sale->current_income;
                    $grand_income += (floor($sale->current_income / 10) * 10);                    
                }

                if(isset($data['supplier_data'])) {
                    ksort($data['supplier_data']);

                    $data['grand_colection'] = $grand_colection;
                    $data['grand_earnings'] = $grand_earnings;
                    $data['grand_deduction'] = $grand_deduction;
                    $data['grand_income'] = $grand_income;
    
                    // dd($data);
                    return view('Admin.Loadings.sales-report-table')->with('data',$data);
                }
                else {
                    return view('Admin.Loadings.no-data');
                }
            }
            else {

                $monthly_colection =DB::table('daily_collection_suppliers AS tdcs')
                                    ->join('daily_collections AS tdc','tdc.id','tdcs.collection_id')
                                    ->join('suppliers AS ts','ts.id','tdcs.supplier_id')
                                    ->select('ts.sup_name','ts.sup_no','tdcs.supplier_id',DB::raw('IFNULL(SUM(tdcs.number_of_units),0) AS total_units'),DB::raw('IFNULL(SUM(tdcs.daily_amount),0) AS total_value'),DB::raw('IFNULL(SUM(tdcs.delivery_cost),0) AS total_delivery_cost'),DB::raw('IFNULL(SUM(tdcs.daily_value),0) AS net_value'))
                                    ->where(DB::raw('DATE_FORMAT(tdc.date, "%Y-%m")'),'=',$requested_month)
                                    ->where('tdc.confirm_status', '=', 0)
                                    ->whereNull('tdcs.deleted_at')
                                    ->whereNull('tdc.deleted_at')
                                    ->whereNull('ts.deleted_at')
                                    ->groupBy('tdcs.supplier_id')
                                    ->get();

                foreach ($monthly_colection as $collection) {
                    $data['supplier_data'][$collection->supplier_id]['supplier_name'] = $collection->sup_name;
                    $data['supplier_data'][$collection->supplier_id]['supplier_no'] = $collection->sup_no;
                    $data['supplier_data'][$collection->supplier_id]['number_of_units'] = $collection->total_units;
                    $data['supplier_data'][$collection->supplier_id]['delivery_cost'] = $collection->total_delivery_cost;
                    $data['supplier_data'][$collection->supplier_id]['total_earnings'] = $collection->total_value;
                }

                $monthly_issues =DB::table('daily_issues_suppliers AS tdis')
                                    ->join('daily_issues AS tdi','tdi.id','tdis.issue_id')
                                    ->join('suppliers AS ts','ts.id','tdis.supplier_id')
                                    ->select('ts.sup_name','ts.sup_no','tdis.supplier_id',DB::raw('IFNULL(SUM(tdis.daily_value),0) AS net_cost'))
                                    ->where(DB::raw('DATE_FORMAT(tdi.date, "%Y-%m")'),'=',$requested_month)
                                    ->where('tdi.confirm_status', '=', 0)
                                    ->whereNull('tdis.deleted_at')
                                    ->whereNull('tdi.deleted_at')
                                    ->whereNull('ts.deleted_at')
                                    ->groupBy('tdis.supplier_id')
                                    ->get();

                foreach ($monthly_issues as $issues) {
                    $data['supplier_data'][$issues->supplier_id]['supplier_name'] = $issues->sup_name;
                    $data['supplier_data'][$issues->supplier_id]['supplier_no'] = $issues->sup_no;
                    $data['supplier_data'][$issues->supplier_id]['item_cost'] = $issues->net_cost;
                }

                $month = array();
                $month[1] = date("Y-m", strtotime($requested_month));
                $month[2] = date('Y-m',strtotime('first day of +1 month',strtotime($requested_month)));
                $month[3] = date('Y-m',strtotime('first day of +2 month',strtotime($requested_month)));

                $sup_fertilizer_issues =DB::table('fertilizer_issues_suppliers AS tfis')
                                    ->join('fertilizer_issues AS tfi','tfi.id','tfis.fertilizer_issue_id')
                                    ->join('suppliers AS ts','ts.id','tfis.supplier_id')
                                    ->select('ts.sup_name','ts.sup_no','tfis.id','tfis.supplier_id',DB::raw('IFNULL(SUM(tfis.daily_value/tfis.payment_frequency),0) AS fertilizer_cost'))
                                    ->where(DB::raw('DATE_FORMAT(tfi.date, "%Y-%m")'),'=',$requested_month)
                                    ->where(['tfi.confirm_status' => 0])
                                    ->whereNull('tfis.deleted_at')
                                    ->whereNull('ts.deleted_at')
                                    ->whereNull('tfi.deleted_at')
                                    ->groupBy('tfis.supplier_id')
                                    ->get();

                foreach ($sup_fertilizer_issues as $issue) {
                    $data['supplier_data'][$issue->supplier_id]['supplier_name'] = $issue->sup_name;
                    $data['supplier_data'][$issue->supplier_id]['supplier_no'] = $issue->sup_no;
                    $data['supplier_data'][$issue->supplier_id]['fertilizer_cost'] = $issue->fertilizer_cost;
                }


                $monthly_installments =DB::table('monthly_installments AS tmi')
                                    ->join('suppliers AS ts','ts.id','tmi.supplier_id')
                                    ->select('ts.sup_name','ts.sup_no','tmi.supplier_id',DB::raw('IFNULL(SUM(tmi.installment),0) AS total_installments'))
                                    ->where('tmi.month','=',$requested_month)
                                    ->where('tmi.deducted_status','=', 0)
                                    ->whereNull('tmi.deleted_at')
                                    ->whereNull('ts.deleted_at')
                                    ->groupBy('tmi.supplier_id')
                                    ->get();

                foreach ($monthly_installments as $installment) {
                    $data['supplier_data'][$installment->supplier_id]['supplier_name'] = $installment->sup_name;
                    $data['supplier_data'][$installment->supplier_id]['supplier_no'] = $installment->sup_no;
                    $data['supplier_data'][$installment->supplier_id]['installments'] = $installment->total_installments;
                }

                $debtor_details =DB::table('debtor_details AS tdd')                
                                    ->join('suppliers AS ts','ts.id','tdd.supplier_id')
                                    ->select('ts.sup_name','ts.sup_no','tdd.supplier_id',DB::raw('IFNULL(SUM(tdd.amount),0) AS sup_credit'))
                                    ->where('tdd.relevant_month','=',$requested_month)
                                    ->where('tdd.forwarded_status','=', 0)
                                    ->whereNull('tdd.deleted_at')
                                    ->whereNull('ts.deleted_at')
                                    ->groupBy('tdd.supplier_id')
                                    ->get();

                foreach ($debtor_details as $debtors) {
                    $data['supplier_data'][$debtors->supplier_id]['supplier_name'] = $debtors->sup_name;
                    $data['supplier_data'][$debtors->supplier_id]['supplier_no'] = $debtors->sup_no;
                    $data['supplier_data'][$debtors->supplier_id]['forwarded_credit'] = $debtors->sup_credit;
                }
                
                $grand_colection = 0;
                $grand_earnings = 0;
                $grand_deduction = 0;
                $grand_income = 0;
                if(count($data['supplier_data']) > 0) {

                    foreach ($data['supplier_data'] as $supplier_id => $sup_data) {

                        $total_earnings = 0;
                        $total_delivery_cost = 0;
                        $total_item_cost = 0;
                        $total_fertilizer_cost = 0;
                        $total_installment = 0;
                        $forwarded_credit = 0;

                        if(isset($sup_data['number_of_units'])) {
                            $grand_colection += $sup_data['number_of_units'];
                        }
                        else {
                            $data['supplier_data'][$supplier_id]['number_of_units'] = 0;
                        }

                        if(isset($sup_data['total_earnings'])) {
                            $total_earnings = $sup_data['total_earnings'];
                            $grand_earnings += $sup_data['total_earnings'];
                        }
                        else {
                            $data['supplier_data'][$supplier_id]['total_earnings'] = 0;
                        }

                        if(isset($sup_data['delivery_cost'])) {
                            $total_delivery_cost = $sup_data['delivery_cost'];
                            $grand_deduction += $sup_data['delivery_cost'];
                        }
                        if(isset($sup_data['item_cost'])) {
                            $total_item_cost = $sup_data['item_cost'];
                            $grand_deduction += $sup_data['item_cost'];
                        }
                        if(isset($sup_data['fertilizer_cost'])) {
                            $total_fertilizer_cost = $sup_data['fertilizer_cost'];
                            $grand_deduction += $sup_data['fertilizer_cost'];
                        }
                        if(isset($sup_data['installments'])) {
                            $total_installment = $sup_data['installments'];
                            $grand_deduction += $sup_data['installments'];
                        }
                        if(isset($sup_data['forwarded_credit'])) {
                            $forwarded_credit = $sup_data['forwarded_credit'];
                            $grand_deduction += $sup_data['forwarded_credit'];
                        }

                        $total_deduction = ($total_delivery_cost + $total_item_cost + $total_fertilizer_cost + $total_installment + $forwarded_credit);
                        $data['supplier_data'][$supplier_id]['total_deduction'] = $total_deduction;
                        
                        $total_outstanding = $total_earnings - $total_deduction;

                        if($total_outstanding >= 0) {
                            $current_income = $total_outstanding;
                            $grand_income += (floor($total_outstanding / 10) * 10);   
                        }
                        else {
                            $current_income = 0;
                        }
                        
                        $data['supplier_data'][$supplier_id]['current_income'] = $current_income;

                    }

                    ksort($data['supplier_data']);

                }

                $data['grand_colection'] = $grand_colection;
                $data['grand_earnings'] = $grand_earnings;
                $data['grand_deduction'] = $grand_deduction;
                $data['grand_income'] = $grand_income;

                // dd($data);
                return view('Admin.Loadings.sales-report-table')->with('data',$data);

            }

        }
        else {
            return view('Admin.Loadings.no-data');
        }

    }

}
