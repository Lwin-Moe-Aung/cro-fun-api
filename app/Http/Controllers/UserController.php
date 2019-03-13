<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Library\DateTimeLibrary;
use App\User;
use App\Lender;
use App\FieldOfficer;
use App\Borrower;
use Hash;

class UserController extends ApiController
{
    /**
     * retrieve all data via user's table.
     */
    public function index()
    {
        $users = User::all();
        return $this->respond([

            'data'=>$users->toArray()

        ]);
    }

    /**
     * retrieve data via user's table by specified id.
     */
    public function show($id)
    {
        $user = User::find($id);
        if(! $user) {

            return $this->respondNotFound('User does not exist');
        }
        return $this->respond([
            'data'=>$user->toArray()

        ]);
    }

    /**
     * retrieve data via user's table by specified email. 
     */
    public function searchByEmail($email)
    {
        $user = User::where('email',$email)->first();
        if(! $user) {

            return $this->respondNotFound('User does not exist');

        }
        return $this->respond([

            'data'=>$user->toArray()

        ]);

    }

    /**
     * insert the user's info to user's table.
     */
    public function store()
    {
        $email=User::where('email',Input::get('email'))->get();
        if(! Input::get('name')  ) {

            return $this->setStatusCode(422)->respondWithError('Name field should not be empty');

        }
        else if(! Input::get('email')) {

            return $this->setStatusCode(422)->respondWithError('Email field should not be empty');

        }
        else if(! Input::get('password')) {

            return $this->setStatusCode(422)->respondWithError('Password field should not be empty');

        }
        else if(!$email->isEmpty()) {

            return $this->respondNotFound('User already exist');

        }
        $user = User::create(Input::all());
        return $this->respondCreated("User ID ".$user->id." is created");

    }

    /**
     * update the user's info by specified user's id.
     */
    public function update($id , Request $request)
    {
        $user = User::where('id',$id)->first();
        if(!$user) {

            return $this->respondNotFound('User does not exist');

        }
        $inputs = Input::all();
        User::where('id',$id)->update($inputs);
        return $this->respondCreated('User Successfully Updated');

    }

    /**
     * delete the user's info by specified user's id .
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(! $user) {

            return $this->respondNotFound('User does not exist');

        }
        $user->delete();
        return $this->respondCreated('User Successfully Deleted');

    }

    /**
     * retrieve the current login user's info.
     */
    public function loginUser(Request $request)
    {
        $login_user = $request->user();
        $user = array();

        if($login_user['role'] == 'lender') {

            $user = Lender::where('user_id',$login_user['id'])->withTrashed()->get();

        }
        elseif($login_user['role'] == 'field-officer') {

            $user = FieldOfficer::where('user_id',$login_user['id'])->withTrashed()->get();

        }
        elseif($login_user['role'] == 'borrower') {

            $user = Borrower::where('user_id',$login_user['id'])->withTrashed()->get();

        }
        else{

            return $login_user;

        }
        

        if($user[0]->deleted_at != null){

            return 'Invalid User';

        }

        $dob = DateTimeLibrary::changeDateFormat($user[0]->dob);
        $user[0]->dob = $dob;


        $users = array();
        foreach ($user as $u){

            $users = $u->toArray();
            $users['name'] = $login_user['name'];
            $users['email'] = $login_user['email'];
            $users['role'] = $login_user['role'];
        }
        return $users;
    }

    /**
     * change user's password.
     */
    public function changePassword($id)
    {
        $user = User::find($id);
        if(!$user) {

            return $this->respondNotFound('User does not exist');

        }

        if(Hash::check(Input::get('old_password'),$user->password)) {

            $user->password = bcrypt(Input::get('password'));
            $user->save();
            return $this->respondCreated('Your new password is changed successfully');

        }
        else {

            return $this->respondCreated('Your old password is incorrect');
        }
    }
}