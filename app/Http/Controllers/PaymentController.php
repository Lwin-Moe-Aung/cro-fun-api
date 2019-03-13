<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use App\Library\DateTimeLibrary;
use App\Http\Controllers\Api\ApiController;
use App\Library\Log\Contracts\Log;

class PaymentController extends ApiController
{

    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }
    /*
    * Insert the payment's record into the database
    */
    public function store(Request $request)
    {

        DateTimeLibrary::changeInputDateFormat();
        $payment = new Payment;
        $payment->project_id = $request->get('project_id');
        $payment->payment_date = $request->get('payment_date');
        $payment->amount = $request->get('amount');
        $payment->status = "paid";
        $payment->remark = $request->get('remark');
        $payment->attachment = $request->get('attachment');
        $payment->transaction_no = $request->get('transaction_no');
        $payment->save();

         // Inserting Log
        $detail = [

            'project_id' => $payment->project_id,
            'message' => 'Give loan to borrower',
            'amount' => $payment->amount,
           
        ];


        $detail = json_encode($detail,true);
        $table = "payments";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log

        return $this->respondCreated("Give loan to borrower successful with Transaction ID :".$payment->transaction_no);

    }


    /*
     * Check the payment's transaction no whether it is already in the database or not
     */
    public function searchByTransactionNo($value)
    {
        $payment = Payment::where('transaction_no',$value)->first();
        if(!$payment) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$payment->toArray()

        ]);
    }


    /*
    * Retrieve the payment's record
    *  by payment's project id
   */
    public function searchByProject($id)
    {
        $payment = Payment::where('project_id',$id)->first();
        if(!$payment) {

            return $this->respondNotFound('No record found');

        }
        $payment['payment_date'] = DateTimeLibrary::changeDateFormat($payment['payment_date']);
        return $this->respond([

            'data'=>$payment->toArray()

        ]);
    }
}
