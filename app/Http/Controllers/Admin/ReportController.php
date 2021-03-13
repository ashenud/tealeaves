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
                                            ->select(DB::raw('SUM(tdcs.number_of_units) AS num_of_units'))
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
                    foreach ($daily_colection as $collectoin) { 
                        $num_of_units += $collectoin->num_of_units;
                    }                    
                    $grand_colection += $num_of_units; 
                    $data['supplier_data'][$sale->supplier_id]['number_of_units'] = $num_of_units;
                    $data['supplier_data'][$sale->supplier_id]['total_earnings'] = $sale->total_earnings;
                    $grand_earnings += $sale->total_earnings;
                    $total_deduction = $sale->total_cost + $sale->total_installment + $sale->forwarded_credit;
                    $grand_deduction += $total_deduction;
                    $data['supplier_data'][$sale->supplier_id]['total_deduction'] = $total_deduction;
                    $data['supplier_data'][$sale->supplier_id]['current_income'] = $sale->current_income;
                    $grand_income += (ceil($sale->current_income / 10) * 10);                    
                }

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
            return view('Admin.Loadings.no-data');
        }

    }

}
