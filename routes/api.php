<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Route::middleware('auth:api')->get('/me','UserController@loginUser');

Route::namespace('Auth')->group(function () {
    Route::post('login', 'ApiLoginController@login');
});


Route::get('/me','UserController@loginUser')->middleware('auth:api');

Route::get('/users', 'UserController@index')->middleware(['auth:api','scopes:get-users']);
Route::get('/users/{id}', 'UserController@show')->middleware(['auth:api','scopes:get-user']);
Route::post('/users/store', 'UserController@store')->middleware(['auth:api','scopes:add-user']);
Route::patch('/users/{id}', 'UserController@update');
Route::delete('/users/{id}', 'UserController@destroy')->middleware(['auth:api','scopes:delete-user']);


Route::get('/officers', 'FieldOfficerController@index')->middleware(['auth:api','scopes:get-officers']);
Route::get('/officers/{id}', 'FieldOfficerController@show')->middleware(['auth:api','scopes:get-officer']);
Route::post('/officers/store', 'FieldOfficerController@store')->middleware(['auth:api','scopes:add-officer']);
Route::get('/officers/edit/{id}','FieldOfficerController@Edit')->middleware(['auth:api','scopes:get-officer']);
Route::patch('/officers/edit/{id}', 'FieldOfficerController@update')->middleware(['auth:api','scopes:update-officer']);
Route::delete('/officers/{id}', 'FieldOfficerController@destroy')->middleware(['auth:api','scopes:delete-officer']);
//Route::get('/officers/{value}/search/code-no', 'FieldOfficerController@searchCodeNo')->middleware(['auth:api','scopes:get-officer']);



Route::get('/borrowers','BorrowerController@index')->middleware(['auth:api','scopes:get-borrowers']);
Route::get('/borrowers/{id}','BorrowerController@show')->middleware(['auth:api','scopes:get-borrower']);
Route::post('/borrowers/store','BorrowerController@store')->middleware(['auth:api','scopes:add-borrower']);
Route::patch('/borrowers/{id}','BorrowerController@update');
Route::delete('borrowers/{id}','BorrowerController@destroy')->middleware(['auth:api']);
//Route::get('/borrowers/{value}/search/code-no', 'BorrowerController@searchCodeNo')->middleware(['auth:api','scopes:get-borrower']);

Route::get('borrowers/projects/{id}','BorrowerController@project')->middleware(['auth:api']);
Route::post('borrowers/give/point/{id}','BorrowerController@givePoint')->middleware(['auth:api']);



Route::get('/lenders', 'LenderController@index')->middleware(['auth:api','scopes:get-lenders']);
Route::get('/lenders/{id}', 'LenderController@show')->middleware(['auth:api','scopes:get-lender']);
Route::post('/lenders/store', 'LenderController@store');
Route::patch('/lenders/{id}', 'LenderController@update')->middleware(['auth:api','scopes:get-users']);

Route::get('/lenderInvestments/{id}', 'LenderController@my_investments')->middleware(['auth:api']);
Route::delete('/lenders/{id}', 'LenderController@destroy')->middleware(['auth:api','scopes:delete-user']);

//Route::get('/lenders/{value}/search/code-no', 'LenderController@searchCodeNo');
Route::post('/lender/account/verified/{id}', 'LenderController@lenderAccountVerified')->middleware(['auth:api']);

Route::get('/categories','CategoryController@index');


Route::get('/state','LocationController@state');
Route::get('/township/{state}','LocationController@township');
Route::get('/township','LocationController@townships');


Route::get('/projects','ProjectController@index');
Route::get('/projects/all','ProjectController@all');
Route::get('/projects/{id}','ProjectController@show');
Route::get('/finance','ProjectController@Finance')->middleware(['auth:api','scopes:get-projects']);
Route::post('/projects/store','ProjectController@store')->middleware('auth:api');
Route::patch('/projects/{id}','ProjectController@update')->middleware(['auth:api','scopes:update-project']);
Route::delete('/projects/{id}','ProjectController@destroy')->middleware('auth:api','scopes:delete-project');
//Route::get('/projects/{value}/search/code-no', 'ProjectController@searchCodeNo');



/*------------ showing investment table -----------------*/

Route::post('/investment/store','InvestmentController@store')->middleware(['auth:api']);
Route::get('/investment/{value}/search/transaction','InvestmentController@searchByTransactionNo');
Route::get('/investment/{value}/{value1}/search/display/amount','InvestmentController@searchByAmountToTransfer');
//Route::get('/investment/{value}/search/display/amount','InvestmentController@searchByAmountToTransfer1');
Route::get('investment/project','InvestmentController@getProjectInvestment');
Route::get('update_status/{id}','InvestmentController@forUpdate');
Route::patch('investments/{id}','InvestmentController@update')->middleware('auth:api');
Route::get('investmentsdetail/{id}','InvestmentController@investmentsDetail');
Route::get('investments','InvestmentController@allInvestment');
Route::get('investments/{id}','InvestmentController@investment');
Route::get('investments/project/{id}','InvestmentController@getProjectInvestmentByFinance');
Route::get('check/lender/investment/project/{id}','InvestmentController@checkLenderInvestmentByProject');



Route::post('/payment/store','PaymentController@store')->middleware('auth:api');
Route::get('/payment/{value}/search/transaction','PaymentController@searchByTransactionNo');
Route::get('/payment/{value}/search/project','PaymentController@searchByProject');

Route::post('/loan_return/store','LoanReturnController@store');
Route::get('loan_return/{value}/search/transaction_no','LoanReturnController@searchByTransactionNo');
Route::get('/loan_return/{id}/search','LoanReturnController@show');
Route::get('check/loan_return/{id}','LoanReturnController@checkLoanReturn');


Route::post('/profit/store','ProfitController@store')->middleware('auth:api');
Route::get('/profit/{value}/search/transaction_no', 'ProfitController@searchTransactionNo');
Route::get('/profit/{value}/search/project', 'ProfitController@searchRecordByProject');


Route::post('/profit_distribution/store','ProfitDistributionController@store')->middleware('auth:api');
Route::get('/profit_distribution/show/{id}','ProfitDistributionController@show');
Route::get('/profit_distribution/display/{id}','ProfitDistributionController@profitDistribution');
Route::patch('/profit_distribution/display/{id}','ProfitDistributionController@profitDistributionUpdate')->middleware('auth:api');
Route::get('/profit_distribution/{value}/search/transaction_no', 'ProfitDistributionController@searchTransactionNo');
Route::get('/total_revenue/{id}','ProfitDistributionController@totalRevenue');





/*------------ forgot password -----------------*/
Route::get('/users/search/{email}', 'UserController@searchByEmail');
Route::post('/password_reset/store','Auth\ResetPasswordController@store');
Route::delete('/password_reset/{email}','Auth\ResetPasswordController@delete');
Route::get('/password_reset/show/{token}','Auth\ResetPasswordController@show');
/*
Route::get('/tasks','TaskController')->middleware('auth:api');
Route::get('/tasks/{id}','TaskController@')->middleware('auth:api');
Route::post('/tasks/create','TaskController')->middleware('auth:api');
//
*/

Route::get('lenderprofile/{id}','LenderController@lenderProfile')->middleware('auth:api');
Route::patch('change/password/{id}','UserController@changePassword')->middleware('auth:api');


/* --------------  Creating,Updating,Retrieving and deleting the cms page --------*/
Route::get('pages','PagesController@index')->middleware('auth:api');
Route::post('pages/store','PagesController@store')->middleware('auth:api');
Route::get('pages/{slug}','PagesController@show');
Route::delete('pages/{id}','PagesController@destroy')->middleware('auth:api');

Route::get('pages/edit/{id}','PagesController@showById')->middleware('auth:api');
Route::patch('pages/edit/{id}','PagesController@update')->middleware('auth:api');



Route::post('project/progress/store','ProjectProgressController@store')->middleware('auth:api');
Route::get('project/progress/{id}','ProjectProgressController@show');
Route::get('project/progress/detail/{id}','ProjectProgressController@detail')->middleware('auth:api');


Route::get('send/mail/flag/{state}','InvestmentController@sendingEmailFlag')->middleware('auth:api');



Route::get('/logs/{name?}','LogController@show');

Route::get('lender/getmoney/{id}','LenderController@Available');
Route::get('lender/notgetmoney/{id}','LenderController@notAvailable');
Route::get('lender/proj-progress/{id}','LenderController@progressProject');


//Route::get('search/code/{table}/{prefix}','LocationController@searchCodeNo');



