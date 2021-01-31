<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\LoanIssues;
use App\Models\MonthlyInstallment;

class LoanIssueController extends Controller
{
    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Loan Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->join('routes AS tr','tr.id','ts.route_id')
                       ->select('ts.sup_name','ts.id')
                       ->whereNull('ts.deleted_at')
                       ->whereNull('tr.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        // dd($data);
        return view('Admin.loan-issue')->with('data',$data);
    }

    public function insertLoan(Request $request) {

        $user_id = Auth::user()->id;
        
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
}
