<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Input;
use DB;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function store()
    {
        $email=Input::get('email');
        $token=Input::get('token');
        $created_at=Input::get('created_at');
        DB::table('password_resets')->insert(['email'=>$email,'token'=>$token,'created_at'=>$created_at]);
    }
    public function delete($email)
    {

        DB::table('password_resets')->where('email',$email)->delete();
    }
    public function show($token)
    {
        $info=DB::table('password_resets')->where('token',$token)->get();
        $info=json_decode($info, true);
        $data=['data'=>$info];
        return $data;
    }
}
