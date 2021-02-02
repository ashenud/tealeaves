<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\AdvanceIssues;
use App\Models\MonthlyInstallment;

class AdvanceIssueController extends Controller
{
    public function index() {

        $user_id = Auth::user()->id;
        $data = array();
        $data['page_title'] = 'Advance Issue';

        $suppliers = DB::table('suppliers AS ts')
                       ->join('routes AS tr','tr.id','ts.route_id')
                       ->select('ts.sup_name','ts.id')
                       ->whereNull('ts.deleted_at')
                       ->whereNull('tr.deleted_at')
                       ->get();
        $data['suppliers'] = $suppliers;

        // dd($data);
        return view('Admin.advance-issue')->with('data',$data);
    }

    public function insertAdvance(Request $request) {

        $user_id = Auth::user()->id;
        
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
}
