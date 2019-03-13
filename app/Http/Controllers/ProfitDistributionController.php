<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProfitDistribution;
use App\Profit;
use App\Library\DateTimeLibrary;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Input;
use DB;
use App\Library\Log\Contracts\Log;

class ProfitDistributionController extends ApiController
{


    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }

    /*
    * Insert the profit distribution's record into the database
    */
    public function store(Request $request)
    {

        DateTimeLibrary::changeInputDateFormat();
        $profit_distribution = new ProfitDistribution();
        $profit_distribution->profit_id = $request->get('profit_id');
        $profit_distribution->investment_id = $request->get('investment_id');
        $profit_distribution->profit = $request->get('profit');
        $profit_distribution->revenue = $request->get('revenue');
        $profit_distribution->profit_paid_date = $request->get('profit_paid_date');
        $profit_distribution->status = "pending";
        $profit_distribution->transaction_no = $request->get('transaction_no');
        $profit_distribution->profit_distribution_percentage = $request->get('profit_distribution_percentage');
        $profit_distribution->save();
        return $this->respondCreated("Profit distribution is successfully calculated");

    }


    /*
    * Retrieve the particular profit distribution's record
    * And its related all the investments' info
    * And its related borrower info
    *  by profit's project id
   */
    public function show($id)
    {
    
        $results = DB::table('profit_distributions')
            ->select('profit_distributions.*',DB::raw('profit_distributions.profit + investments.amount as total_revenue'),'users.name','lenders.code_no as lender_id','investments.amount as investment')
            ->join('investments','investments.id','=','profit_distributions.investment_id')
            ->join('lenders','lenders.id','=','investments.lender_id')
            ->join('users','users.id','=','lenders.user_id')
            ->join('profits','profits.id','=','profit_distributions.profit_id')
            ->where('profits.project_id','=',$id)
            ->get();

        if (count($results)>0) {

            foreach ($results as $key => $result) {
                
                $result->profit_paid_date = DateTimeLibrary::changeDateFormat($result->profit_paid_date);
            }
        }
        return $this->respond([

                'data' => $results

        ]);

    }
//'sum("profit_distributions.profit","profit_distributions.revenue") as total_revenue'

    /*
     * Check the profit distribution's transaction no whether it is already in the database or not
     */
    public function searchTransactionNo($value)
    {
        $profit_distribution = ProfitDistribution::where('transaction_no',$value)->first();
        if(!$profit_distribution) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$profit_distribution->toArray()

        ]);

    }


    /*
    * Retrieve the particular profit distribution's record
    * And its related all the investments' info
    * And its related all the projects' info
    * And its related lender info
    *  by profit distribution's id
   */
    public function profitDistribution($id)
    {
        $results = DB::table('profit_distributions')
            ->select('profit_distributions.*',DB::raw('profit_distributions.profit + investments.amount as total_revenue'),'users.name','lenders.code_no','investments.amount as investment','projects.project_title')
            ->join('investments','investments.id','=','profit_distributions.investment_id')
            ->join('projects','projects.id','=','investments.project_id')
            ->join('lenders','lenders.id','=','investments.lender_id')
            ->join('users','users.id','=','lenders.user_id')
            ->where('profit_distributions.id','=',$id)
            ->get();

        return $this->respond([

            'data' => $results

        ]);

    }


    /*
     * Update the profit distribution's status
     * By profit distribution's id
     */
    public function profitDistributionUpdate($id,Request $request)
    {

      $profit_distribution = ProfitDistribution::where('id','=',$id)->first();
      DateTimeLibrary::changeInputDateFormat();
      $profit_distribution->profit_paid_date = Input::get('profit_paid_date');
      $profit_distribution->status = "delivered";
      $profit_distribution->save();

        // Inserting Log
        $detail = [

            'profit_distribution_id' => $profit_distribution->id,
            'message' => "Profit distribution status's is changed",
            'from' => 'pending',
            'to' => 'delivered'

        ];


        $detail = json_encode($detail,true);
        $table = "profit_distributions";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log

      return $this->respondCreated('Successfully deliver the profit to lender');

    }

    /*
    *get total revenue from profit distribution table by project id
    */
    public function totalRevenue($id)
    {   
        $results = DB::table('profits')
            ->select('profits.*')
            ->where('profits.project_id',"=",$id)
            ->get();
        //return $results;die();
        $total_revenue = "";
        if (count($results) > 0) {
             $total_revenue = DB::table('profit_distributions')
                ->select(DB::raw('SUM(profit_distributions.revenue) as totalrevenue'))
                ->where('profit_distributions.profit_id',"=",$results[0]->id)
                ->get();

            return $this->respond([

                'data' => $total_revenue

            ]);
        }
        else{
            return $this->respond([

                'data' => $total_revenue

            ]);
        }       

    }

}
