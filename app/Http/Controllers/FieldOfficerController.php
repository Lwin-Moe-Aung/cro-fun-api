<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Input;
use App\FieldOfficer;
use App\User;
use App\Library\DateTimeLibrary;
use App\Library\Library;
use Illuminate\Validation\Rules\In;
use DB;

class FieldOfficerController extends ApiController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $officers = DB::table('field_officers') 
            ->join('users', 'users.id', '=', 'field_officers.user_id') 
            ->select('users.name', 'field_officers.*')
            ->whereNull('deleted_at')
            ->get();

        return $this->respond([
            'data' => $officers
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find officer by id
        $officers = DB::table('field_officers')
                ->select('users.name', 'users.email', 'users.role', 'field_officers.*')
                ->join('users', 'users.id', '=', 'field_officers.user_id')
                ->where('field_officers.id', $id)
                ->get();

        //Check the officer exist or not
        //return $officers;die();
        if(empty($officers[0])){
            return $this->respondNotFound('Field Officer does not exist');
        }

        if($officers[0]->dob != null){
            // change date from SQL to UI date
            $dob=DateTimeLibrary::changeDateFormat($officers[0]->dob);
            $officers[0]->dob = $dob;
        }

        return $this->respond([
            'data' => $officers
            ]);
    }
   
    public function Edit($id){
        $officers = DB::table('field_officers')
            ->select('users.name', 'users.email', 'field_officers.*')
            ->join('users', 'users.id', '=', 'field_officers.user_id')
            ->where('field_officers.id', $id)
            ->get();

        //Convert the officer exist or not
        if(empty($officers[0])){
            return $this->respondNotFound('Field Officer does not exist');
        }

        $dob = DateTimeLibrary::changeDateFormat($officers[0]->dob);
        $officers[0]->dob = $dob;

        return $this->respond([
            'data' => $officers
            ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $errorMessage = "";

        // Validating User Input
        if (Library::validateFields($errorMessage) == false){
            return $this->setStatusCode(422)->respondWithError($errorMessage);
        }

        // Convert DOB from UI to SQL format
        DateTimeLibrary::changeInputDateFormat();

        // Filling "User" data
        $user = new User;
        $user->name = Input::get('name');
        $user->email = Input::get('email');
        $user->password = bcrypt(Input::get('password'));
        $user->role = 'field-officer';
        $user->save();

        // Filling "Field Officer" data
        $officer = new FieldOfficer;
        $officer->code_no = "FOF-".DateTimeLibrary::searchCodeNo('field_officers','-');
        $officer->nrc = Input::get('nrc');
        $officer->dob = Input::get('dob');
        $officer->state = Input::get('state');
        $officer->township = Input::get('township');
        $officer->phone_no = Input::get('phone_no');
        $officer->address = Input::get('address');
        $officer->photo = Input::get('photo');
        $officer->attachment = Input::get('attachment');
        $officer->gender = Input::get('gender');
        $officer->admin_id = Input::get('admin_id');
        $user->fieldofficers()->save($officer);
        return $this->respondCreated($user->email." is successfully registered");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $errorMessage = "";
        $officer = FieldOfficer::where('id', $id)->first();

        if (!$officer){
            return $this->respondNotFound('Field Officer does not exist');
        }
        DateTimeLibrary::changeInputDateFormat();

        $users = User::where('id' ,$officer['user_id'])->first();
        $users->name = Input::get('name');
        $users->save();

        $officer->nrc = Input::get('nrc');
        $officer->dob = Input::get('dob');
        $officer->state = Input::get('state');
        $officer->township = Input::get('township');
        $officer->phone_no = Input::get('phone_no');
        $officer->address = Input::get('address');
        $officer->photo = Input::get('photo');
        $officer->attachment = Input::get('attachment');
        $officer->gender = Input::get('gender');
        $officer->save();

        return $this->respondCreated('Field Officer Successfully Updated');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $officer = FieldOfficer::find($id);
        if(!$officer){
            return $this->respondNotFound('Field Officer does not exist');
        }
        $project = DB::table('projects')
            ->select('*')
            ->where('field_officers_id', '=', $officer->id)
            ->whereNull('deleted_at')
            ->get();

        if(count($project)>0)
        {
            return $this->respondCreated('Can not delete the field officer(Code No:'.$officer->code_no.') because there are records related to this field officer');
        }
        $officer->delete();
    }

    /**
     * check the fieldofficer's code no whether it is already exist in database or not
     */
    /*
    public function searchCodeNo($value)
    {
        $officer = FieldOfficer::where('code_no',$value)->first();
        if(!$officer){
            return $this->respondNotFound('No record found');
        }
        return $this->respond([
            'data'=>$officer->toArray()
        ]);
    }*/
}
