<?php
namespace App\Http\Controllers;

use App\Library\Library;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Investment;
use App\Library\DateTimeLibrary;
use Illuminate\Validation\Rules\In;
use Illuminate\Support\Facades\Input;
use DB;
use App\Library\Log\Contracts\Log;

class InvestmentController extends ApiController
{

    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }
    /*
     * Insert the investment's record into the database
     */
    public function store()
    {
        $errorMessage = "";

        if (Library::validateInvestmentFields($errorMessage) == false){
            return $this->setStatusCode(422)->respondWithError($errorMessage);
        }
        $investment = new Investment();
        $investment->project_id = Input::get('project_id');
        $investment->lender_id = Input::get('lender_id');
        $investment->investment_date = Input::get('investment_date');
        $investment->amount = Input::get('amount');
        $investment->profit_estimation = Input::get('profit_estimation');
        $investment->profit_percentage = Input::get('profit_percentage');
        $investment->display_amount = Input::get('display_amount');
        $investment->transaction_no = Input::get('transaction_no');
        $investment->investment_type = Input::get('investment_type');
        $investment->investment_details = Input::get('investment_details');
        $investment->status = "pending";
        $investment->save();
        return $this->respondCreated("Investment successful with Transaction ID : " .$investment->transaction_no);
    }


    /*
     * Check the investment's transaction no whether it is already in the database or not
     */
    public function searchByTransactionNo($value)
    {
        $investment = Investment::where('transaction_no', $value)->first();
        if(!$investment){
            return $this->respondNotFound('No record found');
        }
        return $this->respond([
            'data' => $investment->toArray()
        ]);
    }


    /*
     * Check the investment's display_amount whether it is already in the database or not
     */
    public function searchByAmountToTransfer($value, $value1)
    {
        $investment = Investment::whereBetween('display_amount', [$value,$value1])->orderBy('id', 'ASC')->get();
        if($investment->isEmpty()){
            return $this->respondNotFound('No record found');
        }
        return $this->respond([
            'data' => $investment->toArray()
        ]);
    }


    /*
     * Retrieve all the project's investment amount and percentage
     */
    public function getProjectInvestment()
    {
       
        $results = DB::select('select * from project_investment');
        return $this->respond([
            'data' => $results
        ]);

    }


    /*
     * Retrieve all the investment's record
     * And its related project's info
     * And its related lender's info
     */
    public function allInvestment()
    {
        $results = DB::table('investments')
            ->select('investments.*', 'projects.project_title', 'users.name')
            ->join('lenders', 'lenders.id', '=', 'investments.lender_id')
            ->join('users', 'users.id', '=', 'lenders.user_id')
            ->join('projects', 'projects.id', '=', 'investments.project_id')  
            ->get();

        return $this->respond([
            'data' => $results
        ]);           
    }


    /*
     * Retrieve all the investment's record
     * And its related project's info
     * And its related lender's info
     * By investment's project id
     */
    public function investment($id)
    {
        $results = DB::table('investments')
            ->select('investments.*', 'projects.project_title', 'users.name', 'lenders.code_no')
            ->join('lenders', 'lenders.id', '=', 'investments.lender_id')
            ->join('users', 'users.id', '=', 'lenders.user_id')
            ->join('projects', 'projects.id', '=', 'investments.project_id')
            ->where('investments.project_id', $id) 
            ->get();
        return $this->respond([
            'data' => $results
        ]);
    }



    /*
     * Retrieve the investment's record
     * And its related project's info
     * And its related lender's info
     * By investment's id
     */
    public function investmentsDetail($id)
    {
        $results = DB::table('investments')
            ->select('investments.*', 'projects.project_title', 'projects.code_no as project_code', 'projects.loan_value', 'users.name', 'lenders.code_no', 'users.email')
            ->join('lenders', 'lenders.id', '=', 'investments.lender_id')
            ->join('users', 'users.id', '=', 'lenders.user_id')
            ->join('projects', 'projects.id', '=', 'investments.project_id')
            ->where('investments.id', $id) 
            ->get();

        if ($results[0]->investment_date != null){

            $date = DateTimeLibrary::changeDateFormat($results[0]->investment_date);
            $results[0]->investment_date = $date; 
        }

        return $this->respond([
            'data' => $results->toArray()
        ]);
    }



    /*
     * Update the investment's status
     * By investment's id
     */
    public function update($id,Request $request)
    {
        $errorMessage = "";
        $investment = investment::where('id', $id)->first();
        $old_status=$investment->status;


        if (!$investment){
            return $this->respondNotFound('Investment does not exist');
        }
        
        $investment->status = Input::get('status');
        $investment->save();

        // Inserting Log
        $detail = [
            'investment_id' => $investment->id,
            'message' => 'Investment\'s status is changed',
            'from' => $old_status,
            'to' => Input::get('status')
        ];

        $detail = json_encode($detail,true);
        $table = "investments";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log

        return $this->respondCreated('Investment status is successfully Updated');  
    }


    /*
     * Retrieve all the investment's total amount record
     * And where investment's status is approved
     * By investment's project id
     */
    public function forUpdate($id)
    {

        $results = DB::table('investments')
            ->select(DB::raw('SUM(investments.amount) as total'))
            ->where('investments.project_id', $id)
            ->where('investments.status', "approved")
            ->get();

        return $this->respond([
        'data' => $results
        ]);

    }



    /*
     * Retrieve all the investment's record
     * And its related project's info
     * And its related lender's info
     * By investment's project id
     * And where investments' status is approved
     */
    public function getProjectInvestmentByFinance($id)
    {
        $results = DB::table('investments')
            ->select('users.name', 'investments.*', 'projects.project_title')
            ->join('lenders', 'lenders.id','=', 'investments.lender_id')
            ->join('users', 'users.id', '=', 'lenders.user_id')
            ->join('projects', 'projects.id', '=', 'investments.project_id')
            ->where('investments.project_id', $id)
            ->where('investments.status', '=', 'approved')
            ->get();
        return $this->respond([
            'data' => $results
        ]);
    }



    /*
     * Retrieve all the investment's total amount record
     * And where investment's status is approved
     * By investment's project id
     */
    public function checkLenderInvestmentByProject($id)
    {
        $results = DB::table('investments')
            ->select(DB::raw('SUM(investments.amount) as total'))
            ->where('investments.project_id', $id)
            ->where('investments.status', "approved")
            ->get();

        return $this->respond([
            'data' => $results
        ]);

    }

    /*
     * checking the flag for sending mail
    */
    public function sendingEmailFlag($state)
    {
       $result = DB::table('send_emails')
             ->select('*')
             ->where('send_email_state',$state)
             ->get();

        return $this->respond([

            'data' => $result

        ]);
    }
}
