<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profit;
use App\Library\DateTimeLibrary;
use App\Http\Controllers\Api\ApiController;
use App\Library\Log\Contracts\Log;


class ProfitController extends ApiController
{
    
    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }

    /*
    * Insert the profit's record into the database
    */
    public function store(Request $request)
    {

        DateTimeLibrary::changeInputDateFormat();
        $profit = new Profit();
        $profit->project_id = $request->get('project_id');
        $profit->revenue = $request->get('revenue');
        $profit->profit = $request->get('profit');
        $profit->profit_generated_date = $request->get('profit_generated_date');
        $profit->remark = $request->get('remark');
        $profit->attachment = $request->get('attachment');
        $profit->transaction_no = $request->get('transaction_no');
        $profit->save();

         // Inserting Log
        $detail = [

            'project_id' => $profit->project_id,
            'message' => 'Receive payment',
            'revenue' => $profit->revenue,
            'profit' => $profit->profit,
           
        ];


        $detail = json_encode($detail,true);
        $table = "profits";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log
        return $this->respondCreated($profit->id);

    }


    /*
     * Check the profit's transaction no whether it is already in the database or not
     */
    public function searchTransactionNo($value)
    {

        $profit=Profit::where('transaction_no',$value)->first();
        if(!$profit) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$profit->toArray()

        ]);
    }


    /*
    * Retrieve the profit's record
    *  by profit's project id
   */
    public function searchRecordByProject($id)
    {
        $profit = Profit::where('project_id',$id)->first();
        if(!$profit) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$profit->toArray()

        ]);


    }
}
