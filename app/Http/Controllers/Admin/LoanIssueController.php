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

        $suppliers = DB::table('suppliers AS ts')
                       ->join('routes AS tr','tr.id','ts.route_id')
                       ->select('ts.sup_name','ts.sup_no','ts.id')
                       ->whereNull('ts.deleted_at')
                       ->whereNull('tr.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        // dd($data);
        return view('Admin.loan-issue')->with('data',$data);
    }

    public function loanDatatable(Request $request ) {

        if ($request->ajax()) {
            $data = DB::table('loan_issues AS tli')
                        ->join('suppliers AS ts','ts.id','tli.supplier_id')
                        ->select('ts.sup_no AS supplier_id','ts.sup_name AS supplier_name','tli.date AS loan_date',DB::raw('IFNULL(tli.remarks,"") AS remarks'),'tli.amount AS amount')
                        ->whereNull('tli.deleted_at')
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
                    ->make(true);
        }
        
        return view('Admin.items');
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
                $loan->loan_no = $request->loan_no;
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
}
