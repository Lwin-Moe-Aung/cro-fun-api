<?php

namespace App\Http\Controllers;

use App\Lender;
use App\User;
use App\Investment;
use App\Http\Controllers\Api\ApiController;
use App\Library\DateTimeLibrary;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Library\Library;
use DB;
use App\Services\Investment\InvestmentService;
use App\Library\Log\Contracts\Log;

class LenderController extends ApiController
{


    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }

    /*
     * Retrieve all the lenders' record
    */
    public function index()
    {

         $lenders = DB::table('lenders') ->join('users', 'users.id', '=', 'lenders.user_id') ->select('users.name', 'lenders.*')
                    ->whereNull('deleted_at')
                    ->get();

         return $this->respond([

            'data'=>$lenders

         ]);

    }


    /*
     * Retrieve a lender's record
     * By lender's id
     */
    public function show($id)
    {

         $results = DB::table('lenders')
                    ->select('lenders.*','users.name','users.email','users.role')
                    ->join('users','users.id','=','lenders.user_id')
                    ->where('lenders.id',$id)
                    ->get();

         if(empty($results[0])){
            return $this->respondNotFound('lender does not exist');
         }
         if($results[0]->dob != null) {

            // change date from SQL to UI date
            $dob = DateTimeLibrary::changeDateFormat($results[0]->dob);
            $results[0]->dob = $dob;

         }
         return $this->respond([
            'data'=>$results
         ]);

    }


    /*
     * Insert the lender's record into the database
     */
    public function store(Request $request)
    {

        $errorMessage = "";

        if (Library::validateFields($errorMessage) == false){

            return $this->setStatusCode(422)->respondWithError($errorMessage);

        }
        DateTimeLibrary::changeInputDateFormat();
        // Filling "User" data
        $user = new User;
        $user->name = Input::get('name');
        $user->email = Input::get('email');
        $user->password = bcrypt(Input::get('password'));
        $user->role = 'lender';
        $user->save();
        // Filling "lender" data
        $lender = new Lender;
        $lender->code_no = "LND-".DateTimeLibrary::searchCodeNo('lenders','-');
        $lender->nrc = Input::get('nrc');
        $lender->dob = Input::get('dob');
        $lender->state = Input::get('state');
        $lender->township = Input::get('township');
        $lender->phone_no = Input::get('phone_no');
        $lender->address = Input::get('address');
        $lender->photo = Input::get('photo');
        $lender->attachment = Input::get('attachment');
        $lender->gender = Input::get('gender');
        $lender->verified = '0';
        $user->lenders()->save($lender);
        return $this->respondCreated("Lender ID ".$lender->id." is created");

    }



    /*
      * Update the lender's record
      * By lender's id
      */
    public function update(Request $request, $id)
    {
        DateTimeLibrary::changeInputDateFormat();
        $lender = Lender::where('id',$id)->first();
        if(!$lender) {

            return $this->respondNotFound('Lender does not exist');

        }
        $users = User::where('id',$lender['user_id'])->first();

        $users->name = Input::get('name');
        $users->save();

        $lender->dob = Input::get('dob');
        $lender->photo = Input::get('photo');
        $lender->gender = Input::get('gender');
        $lender->nrc = Input::get('nrc');
        $lender->state = Input::get('state');
        $lender->township = Input::get('township');
        $lender->phone_no=Input::get('phone_no');
        $lender->address = Input::get('address');
        $lender->attachment = Input::get('attachment');
        $lender->save();
        return $this->respondCreated('Lender Successfully Updated');

    }

    /*
     * Soft delete the lender's record
     * By lender's id
     */
    public function destroy($id)
    {
        $lender = Lender::find($id);

        if(! $lender) {

            return $this->respondNotFound('lender does not exist');

        }
        $investment = DB::table('investments')
        ->select('*')
        ->where('lender_id','=',$lender->id)
        ->get();
        if(count($investment)>0) {

             return $this->respondCreated('Can not delete the lender(Code NO: '.$lender->code_no.') because there are records related to this lender');

        }
        $lender->delete();

    }


    /*
     * Check the lender's code no whether it is already in the database or not
     */
    /*
    public function searchCodeNo($value)
    {
        $lender = Lender::where('code_no',$value)->first();
        if(!$lender) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$lender->toArray()

        ]);
    }*/


    /*
     * Retrieve a lender's investment info
     * By lender's id
     */
    public function lenderProfile($id)
    {
        $lender_investment = InvestmentService::getLenderInvestments($id);
        return $this->respond([

                'data'=>$lender_investment

        ]);

    }


    /*
     * Update the lender's verified
     * By lender's id
     */
    public function lenderAccountVerified(Request $request,$id)
    {
        $lender = Lender::find($id);
        $old_verified=$lender->verified;
        if(! $lender) {

            return $this->respondNotFound('Lender does not exist');

        }
        $lender->verified = $request->get('verified');
        $lender->save();

        // Inserting Log
        $detail = [
            'lender_id' => $lender->id,
            'message' => 'Lender\'s verified is changed',
            'from' => $old_verified,
            'to' => $request->get('verified')
        ];

        $detail = json_encode($detail,true);
        $table = "lenders";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        // end of inserting log
        return $this->respondCreated('This lender\'s account status is successfully updated.');

    }


    /*
     * Retrieve a lender's all investment's info
     * By lender's id
     */
    public function my_investments($id)
    {
         $results = DB::table('investments')
                    ->select('projects.code_no','projects.project_title','projects.id','categories.title','investments.transaction_no','investments.investment_date','investments.amount','investments.profit_estimation','investments.profit_percentage','investments.status')
                    ->join('projects','projects.id','=','investments.project_id')
                    ->join('categories','categories.id','=','projects.category_id')
                    ->where('investments.lender_id',$id)
                    ->whereIn('projects.status',array('open_for_funding','project_on_going'))
                    ->get();

         return $this->respond([

                    'data'=>$results

         ]);
    }

    public function Available($id)
    {
       $results = DB::table('profit_distributions')
            ->select('projects.project_title','projects.id','investments.amount as investment_amount','profit_distributions.profit','profit_distributions.revenue','profit_distributions.profit_distribution_percentage','profit_distributions.profit_paid_date')
            ->join('investments','investments.id','=','profit_distributions.investment_id')
            //->join('profits','profits.id','=','profit_distributions.profit_id')
            ->join('projects','projects.id','=','investments.project_id')
            ->join('lenders','lenders.id','=','investments.lender_id')
            ->join('users','users.id','=','lenders.user_id')
            ->where('investments.lender_id','=',$id)
            ->where('profit_distributions.status','=','delivered')
            ->get();
        return $this->respond([

            'data' => $results

        ]);
    }
    public function notAvailable($id)
    {
      
        $results = DB::table('profit_distributions')
            ->select('projects.project_title','projects.id','investments.amount as investment_amount','profit_distributions.profit','profit_distributions.revenue','profit_distributions.profit_distribution_percentage','profit_distributions.profit_paid_date')
            ->join('investments','investments.id','=','profit_distributions.investment_id')
            ->join('profits','profits.id','=','profit_distributions.profit_id')
            ->join('projects','projects.id','=','investments.project_id')
            ->join('lenders','lenders.id','=','investments.lender_id')
            ->join('users','users.id','=','lenders.user_id')
            ->where('investments.lender_id','=',$id)
            ->where('profit_distributions.status','=','pending')
            ->get();
        return $this->respond([

            'data' => $results

        ]);
    }
    public function progressProject($id){
        $progress = DB::table('project_progresses')
            ->select('project_progresses.*','projects.project_title')
            ->join('projects','projects.id','=','project_progresses.project_id')
            ->join('investments','investments.project_id','=','project_progresses.project_id')
            ->join('lenders','lenders.id','=','investments.lender_id')
            ->join('users','users.id','=','lenders.user_id')
            ->where('investments.lender_id','=',$id)
            
            ->get();
             return $this->respond([

            'data' => $progress

        ]);
    }

}
