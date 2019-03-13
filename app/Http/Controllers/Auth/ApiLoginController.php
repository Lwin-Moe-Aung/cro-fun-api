<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


class ApiLoginController extends Controller
{
    use AuthenticatesUsers;
   
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function authenticated(Request $request, $user)
    {               
        
        // implement your user role retrieval logic
        $role = $user->role;


        // grant scopes based on the role that we get previously
        if ($role == 'admin') {            
            $request->request->add([
                'scope' => implode(' ', config('scope.admin')),// grant manage  scope for user with admin role
            ]);
        } else  if ($role == 'officer') {
            $request->request->add([
                 'scope' => implode(' ', config('scope.officer'))// scope for other user role
            ]);
        } else  if ($role == 'lender') {    
            $request->request->add([
                 'scope' => implode(' ', config('scope.lender'))// scope for other user role
            ]);
        } else  if ($role == 'borrower') {    
            $request->request->add([
                 'scope' => implode(' ', config('scope.borrower'))// scope for other user role
            ]);
        }              
        //var_dump($request->all())
        // forward the request to the oauth token request endpoint
        $tokenRequest = Request::create(
            '/oauth/token',
            'POST',
            $request->all()          
        );
        
        $response = Route::dispatch($tokenRequest);       
       
        return $response;
       
   
    }
    public function login(Request $request){
       
        $user = new \App\User();

        if (Auth::attempt(['email' => $request->input("username"), 'password' => $request->input("password")])) {
            $user = Auth::user();            
           
        }

        $response = $this->authenticated($request, $user);
        return new JsonResponse((array) json_decode($response->getContent(), true), 200,[]);
        
     
    }
   
}