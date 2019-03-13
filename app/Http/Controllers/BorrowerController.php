<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Input;
use App\Borrower;
use App\User;
use App\FieldOfficer;
use App\Library\Library;
use App\Library\DateTimeLibrary;
use DB;
use App\Library\Log\Contracts\Log;

class BorrowerController extends ApiController
{
   protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowers = DB::table('borrowers') 
            ->join('users', 'users.id', '=', 'borrowers.user_id') 
            ->select('users.name', 'borrowers.*')
            ->whereNull('deleted_at')
            ->get();

        return $this->respond([
            'data' => $borrowers
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $results = DB::table('borrowers')
            ->select('borrowers.*','users.name','users.email','users.role',DB::raw("CASE
             WHEN `points` = 'a' THEN 'A'
             WHEN `points` = 'b' THEN 'B'
             WHEN `points` = 'c' THEN 'C'
             WHEN `points` = 'd' THEN 'D'
             WHEN `points` = 'e' THEN 'E'
             END AS points"))
            ->join('users','users.id','=','borrowers.user_id')
            ->where('borrowers.id',$id)
            ->get();

        //check the borrower exist or not
        if(empty($results[0])){
            return $this->respondNotFound('Borrower does not exist');
        }

        // change date from SQL to UI date
        $dob = DateTimeLibrary::changeDateFormat($results[0]->dob);
        $results[0]->dob = $dob;
        
        return $this->respond([
            'data' => $results
        ]); 
    }
        
    /**
     * Show the form for creating a new resource.
     */
    public function store()
    {
        $errorMessage = "";

        if (Library::validateFields($errorMessage) == false){
            return $this->setStatusCode(422)->respondWithError($errorMessage);
        }

        DateTimeLibrary::changeInputDateFormat();
        $user = new User;
        $user->name = Input::get('name');
        $user->email = Input::get('email');
        $user->password = bcrypt(Input::get('password'));
        $user->role = 'borrower';
        $user->save();

        // Filling "Borrower" data
        $borrower = new Borrower();
        $borrower->code_no = "BOW-".DateTimeLibrary::searchCodeNo('borrowers','-');
        $borrower->nrc = Input::get('nrc');
        $borrower->dob = Input::get('dob');
        $borrower->state = Input::get('state');
        $borrower->township = Input::get('township');
        $borrower->phone_no = Input::get('phone_no');
        $borrower->address = Input::get('address');
        $borrower->photo = Input::get('photo');
        $borrower->attachment = Input::get('attachment');
        $borrower->gender = Input::get('gender');
        $borrower->field_officers_id = Input::get('field_officers_id');
        $user->borrowers()->save($borrower);
        return $this->respondCreated($user->email." is successfully registered");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {    
        $errorMessage = "";
        $borrowers = Borrower::where('id', $id)->first();
        

        if(!$borrowers){
            return $this->respondNotFound('Borrower does not exist');
        }
        $users = User::where('id', $borrowers['user_id'])->first();

        DateTimeLibrary::changeInputDateFormat();

        $users->name = Input::get('name');
        $users->save();
        
        $borrowers->dob = Input::get('dob');
        $borrowers->nrc = Input::get('nrc');
        $borrowers->photo = Input::get('photo');
        $borrowers->gender = Input::get('gender');
        $borrowers->state = Input::get('state');
        $borrowers->township = Input::get('township');
        $borrowers->phone_no = Input::get('phone_no');
        $borrowers->attachment = Input::get('attachment');
        $borrowers->address = Input::get('address');
        $borrowers->save();

        
        return $this->respondCreated('Borrower Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $borrowers = Borrower::find($id);

        if(! $borrowers){
            return $this->respondNotFound('borrower does not exist');
        }

        $project = DB::table('projects')
            ->select('*')
            ->where('borrower_id','=',$borrowers->id)
            ->whereNull('deleted_at')
            ->get();

        if(count($project) > 0){
            return $this->respondCreated('Can not delete the borrower(Code NO: '.$borrowers->code_no.') because there are records related to this borrower');
        }

        $borrowers->delete();
    }

    /**
     * check the borrower code no whether it is already exist in database or not
     */
    /*
    public function searchCodeNo($value)
    {
        $borrower = Borrower::where('code_no', $value)->first();
        if(!$borrower){
            return $this->respondNotFound('No record found');
        }
        return $this->respond([
            'data' => $borrower->toArray()
        ]);
    }*/

    /**
     * retrieve project info by borrower's id.
     */
    public function project($id)
    {
        $results = DB::table('projects')       
            ->select(DB::raw('SUM(investments.amount) as totalinvest'), 'projects.*')
            ->join('investments','projects.id','=','investments.project_id')
            ->where ('projects.borrower_id', $id)
            ->where ('investments.status', 'approved')
            ->groupBy('investments.project_id')  
            //->tosql();        
            ->get()
            ->toArray();

        return $this->respond([
            'data' => $results
        ]);
    }

    /**
     * giving the points to a borrower by borrower's id.
     */
    public function givePoint(Request $request, $id)
    {
        $borrower = Borrower::find($id);
        $old_points=$borrower->points;
        if(! $borrower){
            return $this->respondNotFound('Borrower does not exist');
        }

        $borrower->points = $request->get('points');
        $borrower->save();
        /*
        * Insert Log
        */
        $detail = [
            'borrowers_id' => $borrower->id,
            'message' => 'Borrower\'s give points is changed',
            'from' => $old_points,
            'to' => $borrower->points
        ];

        $detail = json_encode($detail,true);
        $table = "borrowers";
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        
        // end of inserting log

        return $this->respondCreated('The point is given successfully');
    }
}
