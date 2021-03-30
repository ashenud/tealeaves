<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Models\LoanIssues;
use App\Models\MonthlyInstallment;
use App\Models\MonthEnd;

class LoanIssueController extends Controller {

    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Other';

       

        // dd($data);
        return view('Admin.loan-issue')->with('data',$data);
    }    

    public function loadMonthlyLoan($month) {

        $user_id = Auth::user()->id;
        $requested_month = $month;

        $start_month = date("Y-m", strtotime(config('application.start_date')));

        if($start_month <= $requested_month) {

            $data = array();            
            $data['loan_month'] = $requested_month;            

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
                return view('Admin.Loadings.loan-issue-view')->with('data',$data);
                
            }
            else {

                $data['month_end'] = 0;

                // dd($data);
                return view('Admin.Loadings.loan-issue-view')->with('data',$data);
            }

        }
        else {
            return view('Admin.Loadings.no-data');
        }

    }

    public function loanDatatable(Request $request ) {

        if ($request->ajax()) {

            $loan_month = $request->get('loan_month');

            $data = DB::table('loan_issues AS tli')
                        ->join('monthly_installments AS tmi','tmi.reference','tli.id')
                        ->join('suppliers AS ts','ts.id','tli.supplier_id')
                        ->select('tli.id AS loan_id','tmi.id AS instalment_id','ts.sup_no AS supplier_id','ts.sup_name AS supplier_name','tli.date AS loan_date',DB::raw('IFNULL(tli.remarks,"") AS remarks'),'tli.amount AS amount')
                        ->where(DB::raw('DATE_FORMAT(tli.date, "%Y-%m")'),'=',$loan_month)
                        ->where('tmi.remarks','=','loan')
                        ->whereNull('tli.deleted_at')
                        ->whereNull('tmi.deleted_at')
                        ->whereNull('ts.deleted_at');

            return Datatables::of($data)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && ! is_null($request->get('search')['value']) ) {
                            $regex = $request->get('search')['value'];
                            return $query->where(function($queryNew) use($regex){
                                $queryNew->where('ts.sup_no', 'like', '%' . $regex . '%')
                                    ->orWhere('ts.sup_name', 'like', '%' . $regex . '%')
                                    ->orWhere('tli.date', 'like', '%' . $regex . '%')
                                    ->orWhere('tli.remarks', 'like', '%' . $regex . '%');
                            });
                        }
                    })
                    ->addColumn('action', function($data) use ($request){

                        $month_end = MonthEnd::where('month',$request->get('loan_month'))->where('ended_status',1)->limit(1)->get();
                        if(count($month_end) > 0) {
                            $btn = '<a class="btn btn-disabled btn-sm" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-disabled btn-sm" type="button"><i class="far fa-trash-alt"></i></a>';
                        }
                        else {
                            $btn = '<a class="btn btn-sheding btn-sm" onclick="sendDataToEditModel('.$data->loan_id.')" data-mdb-toggle="modal" data-mdb-target="#edit_model" type="button"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-sheding btn-sm" onclick="removeLoan('.$data->loan_id.','.$data->instalment_id.')" type="button"><i class="far fa-trash-alt"></i></a>';
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
                                $query->orderBy('tli.date', $dir);
                            }
                        }
                    })
                    ->with('total', number_format($data->sum('amount'),2))
                    ->make(true);
        }

    }

    public function getLoanData(Request $request ) {

        $loan = DB::table('loan_issues AS tli')
                     ->join('monthly_installments AS tmi','tmi.reference','tli.id')
                     ->join('suppliers AS ts','ts.id','tli.supplier_id')
                     ->select('tli.id AS loan_id','tmi.id AS instalment_id','ts.sup_no AS supplier_no','ts.sup_name AS supplier_name','tli.date AS loan_date',DB::raw('IFNULL(tli.remarks,"") AS remarks'),'tli.amount AS amount')
                     ->where('tli.id','=',$request->id)
                     ->where('tmi.remarks','=','loan')
                     ->whereNull('tli.deleted_at')
                     ->whereNull('tmi.deleted_at')
                     ->whereNull('ts.deleted_at')
                     ->groupBy('tli.id')
                     ->get();

        return response()->json([
            'result' => true,
            'data' => $loan
        ]);

    }

    public function insertLoan(Request $request) {

        $user_id = Auth::user()->id;
        $requested_date = $request->date;
        
        $requested_month = date("Y-m", strtotime($requested_date));

        $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',0)->limit(1)->get();
        
        if(count($month_end) > 0) {
        
            try {

                DB::beginTransaction();

                $loan = new LoanIssues();
                $loan->date = $request->date;
                $loan->supplier_id = $request->supplier;
                $loan->amount = $request->amount;
                $loan->remarks = $request->remarks;
                $loan->user_id = $user_id;
                $loan->save();

                $loan_id = $loan->id;

                $timestamp = strtotime($request->date);
                $installment_month = date("Y-m", $timestamp);
                $remarks = "loan";

                $monthly_installment = new MonthlyInstallment();
                $monthly_installment->supplier_id = $request->supplier;
                $monthly_installment->month = $installment_month;
                $monthly_installment->installment = $request->amount;
                $monthly_installment->reference = $loan_id;
                $monthly_installment->remarks = $remarks;
                $monthly_installment->deducted_status = 0;
                $monthly_installment->save();
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Loan amount successfully approved',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Loan amount not successfully approved',
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

    public function editLoan(Request $request) {

        $user_id = Auth::user()->id;
        $requested_date = $request->date;
        
        $requested_month = date("Y-m", strtotime($requested_date));

        $month_end = MonthEnd::where('month',$requested_month)->where('ended_status',0)->limit(1)->get();
        
        if(count($month_end) > 0) {

            try {

                DB::beginTransaction();

                $loan = LoanIssues::find($request->loan_id);
                $loan->date = $request->date;
                $loan->amount = $request->amount;
                $loan->remarks = $request->remarks;
                $loan->save();   
                
                $timestamp = strtotime($request->date);
                $installment_month = date("Y-m", $timestamp);

                $monthly_installment = MonthlyInstallment::find($request->instalment_id);
                $monthly_installment->month = $installment_month;
                $monthly_installment->installment = $request->amount;
                $monthly_installment->save(); 
                
                DB::commit();
                return response()->json([
                    'result' => true,
                    'message' => 'Loan amount successfully edited',
                ]);

            } catch (\Exception $e) {
                DB::rollback();    
                return response()->json([
                    'result' => false,
                    'message' => 'Loan amount not successfully edited',
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

    public function deleteLoan(Request $request) {        

        try {

            DB::beginTransaction();

            $loan = LoanIssues::find($request->loan_id);
            $loan->delete();  

            $monthly_installment = MonthlyInstallment::find($request->instalment_id);
            $monthly_installment->delete();  
            
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Loan amount successfully deleted',
            ]);

        } catch (\Exception $e) {
            DB::rollback();    
            return response()->json([
                'result' => false,
                'message' => 'Loan amount not successfully deleted',
                'error' => $e,
            ]);
        }
        
    }

}
