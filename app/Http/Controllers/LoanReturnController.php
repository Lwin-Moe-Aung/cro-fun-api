<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanReturn;
use App\Library\DateTimeLibrary;
use App\Http\Controllers\Api\ApiController;
use App\Library\Log\Contracts\Log;
use DB;

class LoanReturnController extends ApiController
{
    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }
    /*
    * Insert the loan return's record into the database
    */
    public function store(Request $request)
    {

        DateTimeLibrary::changeInputDateFormat();
        $payment = new LoanReturn();
        $payment->project_id = $request->get('project_id');
        $payment->payment_date = $request->get('payment_date');
        $payment->amount = $request->get('amount');
        $payment->status = "paid";
        $payment->remark = $request->get('remark');
        $payment->attachment = $request->get('attachment');
        $payment->transaction_no = $request->get('transaction_no');
        $payment->save();
        /*
        // Inserting Log
        $detail = [

            'project_id' => $payment->project_id,
            'message' => 'Loan Returns from borrower',
            'amount' => $payment->amount,

        ];


        $detail = json_encode($detail,true);
        $table = "loan_return1";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log
*/
        return $this->respondCreated("Loan return form borrower successful with Transaction ID :".$payment->transaction_no);

    }


    /*
     * Check the loan return's transaction no whether it is already in the database or not
     */
    public function searchByTransactionNo($value)
    {
        $payment = LoanReturn::where('transaction_no',$value)->first();
        if(!$payment) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$payment->toArray()

        ]);
    }

    /*
     * Get all loan returns of the particular project
     * By Project's id
     */
    public function show($id)
    {

        $payments = DB::table('loan_returns')
            ->join('projects', 'projects.id', '=', 'loan_returns.project_id')
            ->select('loan_returns.*','projects.project_title')
            ->where('project_id',"=",$id)
            ->get();
           
        if (count($payments) > 0) {
            foreach ($payments as $key => $payment) {
                $payment->payment_date = DateTimeLibrary::changeDateFormat($payment->payment_date); 
              //print_r($payment->payment_date) ;
            }
        }

        return $this->respond([
            'data' => $payments
        ]);


    }

    public function checkLoanReturn($id)
    {
        $total_loan_return = DB::table('loan_returns')
            ->select(DB::raw('SUM(loan_returns.amount) as totalloanreturn'))
            ->where('loan_returns.project_id',"=",$id)
            ->get();
        /*
        $project=DB::table('projects')
            ->select('projects.loan_value')
            ->where('projects.id',"=",$id)
            ->get();
        $loan_value=$project[0]->loan_value;
        $loan_return=$result[0]->totalloanreturn;
        if($loan_return==$loan_value)
        {
            return $this->respondCreated("Loan is fully paid");
        }
        */
        return $this->respond([
            'data' => $total_loan_return
        ]);

    }
}
