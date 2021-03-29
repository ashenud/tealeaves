<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Models\AdvanceIssues;
use App\Models\MonthlyInstallment;
use App\Models\MonthEnd;

class AdvanceIssueController extends Controller {

    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Advance Issue';

        // dd($data);
        return view('Admin.advance-issue')->with('data',$data);
    }    

    public function loadMonthlyAdvance($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();            
            $data['advance_month'] = $requested_month;            

            $suppliers = DB::table('suppliers AS ts')
                        ->join('routes AS tr','tr.id','ts.route_id')
                        ->select('ts.sup_name','ts.sup_no','ts.id',DB::raw('CONCAT(ts.id, "_", ts.sup_name) AS value'))
                        ->whereNull('ts.deleted_at')
                        ->whereNull('tr.deleted_at')
                        ->where('ts.sup_no', '<>' ,'')
                        ->orderBy('ts.sup_no')
                        ->get();
            $data['suppliers'] = $suppliers;
                        
            $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',1)->limit(1)->get();
            
            if(count($month_end) > 0) {
                         
                $data['month_end'] = 1;

                // dd($data);
                return view('Admin.Loadings.advance-issue-view')->with('data',$data);
                
            }
            else {

                $data['month_end'] = 0;

                // dd($data);
                return view('Admin.Loadings.advance-issue-view')->with('data',$data);
            }

        }
        else {
            return view('Admin.Loadings.no-data');
        }

    }

    public function advanceDatatable(Request $request ) {

        if ($request->ajax()) {

            $advance_month = $request->get('advance_month');

            $data = DB::table('advance_issues AS tai')
                        ->join('monthly_installments AS tmi','tmi.reference','tai.id')
                        ->join('suppliers AS ts','ts.id','tai.supplier_id')
                        ->select('tai.id AS advance_id','tmi.id AS instalment_id','ts.sup_no AS supplier_id','ts.sup_name AS supplier_name','tai.date AS advance_date',DB::raw('IFNULL(tai.remarks,"") AS remarks'),'tai.amount AS amount')
                        ->where(DB::raw('DATE_FORMAT(tai.date, "%Y-%m")'),'=',$advance_month)
                        ->where('tmi.remarks','=','advance')
                        ->whereNull('tai.deleted_at')
                        ->whereNull('tmi.deleted_at')
                        ->whereNull('ts.deleted_at');

            return Datatables::of($data)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                            $regex = $request->get('search')['value'];
                            return $query->where(function($queryNew) use($regex){
                                $queryNew->where('ts.sup_no', 'like', '%' . $regex . '%')
                                    ->orWhere('ts.sup_name', 'like', '%' . $regex . '%')
                                    ->orWhere('tai.date', 'like', '%' . $regex . '%')
                                    ->orWhere('tai.remarks', 'like', '%' . $regex . '%');
                            });
                        }
                    })
                    ->addColumn('action', function($data) use ($request){

                        $month_end = MonthEnd::where('month',$request->get('advance_month'))->where('ended_status',1)->limit(1)->get();
                        if(count($month_end) > 0) {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-disabled btn-sm" type="button"><i class="far fa-trash-alt"></i></a>';
                        }
                        else {
                            $btn = '<a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->advance_id.')" data-mdb-toggle="modal" data-mdb-target="#edit_model" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="removeAdvance('.$data->advance_id.','.$data->instalment_id.')" type="button"><i class="far fa-trash-alt"></i></a>';
                        }                        
                        
                        return $btn;
                            
                    })
                    ->order(function ($query) use ($request) {
                        if ($request->has('order') && ! is_null($request->get('order')[0]['column']) && ! is_null($request->get('order')[0]['dir']) ) {
                            $column = $request->get('order')[0]['column'];
                            $dir = $request->get('order')[0]['dir'];
                            
                            if($column == 0) {
                                $query->orderBy('ts.id', $dir);
                            }
                            if($column == 1) {
                                $query->orderBy('ts.sup_name', $dir);
                            }
                            if($column == 2) {
                                $query->orderBy('tai.date', $dir);
                            }
                        }
                    })
                    ->with('total', number_format($data->sum('amount'),2))
                    ->make(true);
        }
        
    }

    public function getAdvanceData(Request $request ) {

        $advance = DB::table('advance_issues AS tai')
                     ->join('monthly_installments AS tmi','tmi.reference','tai.id')
                     ->join('suppliers AS ts','ts.id','tai.supplier_id')
                     ->select('tai.id AS advance_id','tmi.id AS instalment_id','ts.sup_no AS supplier_no','ts.sup_name AS supplier_name','tai.date AS advance_date',DB::raw('IFNULL(tai.remarks,"") AS remarks'),'tai.amount AS amount')
                     ->where('tai.id','=',$request->id)
                     ->where('tmi.remarks','=','advance')
                     ->whereNull('tai.deleted_at')
                     ->whereNull('tmi.deleted_at')
                     ->whereNull('ts.deleted_at')
                     ->groupBy('tai.id')
                     ->get();

        return response()->json([
            'result' => true,
            'data' => $advance
        ]);

    }

    public function insertAdvance(Request $request) {

        $user_id = Auth::user()->id;
        $requested_date = $request->date;
        
        $requested_month = date("Y-m", strtotime($requested_date));

        $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',0)->limit(1)->get();
        
        if(count($month_end) > 0) {

            try {

                DB::beginTransaction();

                $advance = new AdvanceIssues();
                $advance->date = $request->date;
                $advance->advance_no = $request->advance_no;
                $advance->supplier_id = $request->supplier;
                $advance->amount = $request->amount;
                $advance->remarks = $request->remarks;
                $advance->user_id = $user_id;
                $advance->save();

                $advance_id = $advance->id;

                $timestamp = strtotime($request->date);
                $installment_month = date("Y-m", $timestamp);
                $remarks = "advance";

                $monthly_installment = new MonthlyInstallment();
                $monthly_installment->supplier_id = $request->supplier;
                $monthly_installment->month = $installment_month;
                $monthly_installment->installment = $request->amount;
                $monthly_installment->reference = $advance_id;
                $monthly_installment->remarks = $remarks;
                $monthly_installment->deducted_status = 0;
                $monthly_installment->save();
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Advance amount successfully approved',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Advance amount not successfully approved',
                    'error' => $e,
                ]);
            }
        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'Month you selected is allready ended',
            ]);
        }
    }

    public function editAdvance(Request $request) {

        $user_id = Auth::user()->id;
        $requested_date = $request->date;
        
        $requested_month = date("Y-m", strtotime($requested_date));

        $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',0)->limit(1)->get();
        
        if(count($month_end) > 0) {

            try {

                DB::beginTransaction();

                $advance = AdvanceIssues::find($request->advance_id);
                $advance->date = $request->date;
                $advance->amount = $request->amount;
                $advance->remarks = $request->remarks;
                $advance->save();   
                
                $timestamp = strtotime($request->date);
                $installment_month = date("Y-m", $timestamp);

                $monthly_installment = MonthlyInstallment::find($request->instalment_id);
                $monthly_installment->month = $installment_month;
                $monthly_installment->installment = $request->amount;
                $monthly_installment->save(); 
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Advance amount successfully edited',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Advance amount not successfully edited',
                    'error' => $e,
                ]);
            }
        }
        else {
            return response()->json([
                'result' => false,
                'message' => 'Month you selected is allready ended',
            ]);
        }
    }

    public function deleteAdvance(Request $request) {        

        try {

            DB::beginTransaction();

            $advance = AdvanceIssues::find($request->advance_id);
            $advance->delete();  

            $monthly_installment = MonthlyInstallment::find($request->instalment_id);
            $monthly_installment->delete();  
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Advance amount successfully deleted',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Advance amount not successfully deleted',
                'error' => $e,
            ]);
        }
        
    }

}
