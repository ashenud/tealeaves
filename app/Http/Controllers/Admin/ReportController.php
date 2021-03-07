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
        $data['page_title'] = 'Stock';

        $items = DB::table('items AS ti')
                    ->join('item_types AS tit','tit.id','ti.item_type')
                    ->select(DB::raw('CONCAT(ti.id, ",", ti.item_name) AS value'),'ti.item_code')
                    ->whereNotIn('tit.id', [config('application.tealeaves_type')])
                    ->whereNull('ti.deleted_at')
                    ->whereNull('tit.deleted_at')
                    ->get();
        $data['items'] = $items;

        // dd($data);
        return view('Admin.audit-trail')->with('data',$data);
    }

    public function auditTrailTable($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();
                        
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

}
