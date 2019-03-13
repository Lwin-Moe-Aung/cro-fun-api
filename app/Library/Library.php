<?php
namespace App\Library;
use Illuminate\Support\Facades\Input;

use App\User;

class Library
{

    /*
    * Validate the user fields's data
    * @errorMessage is passed by Ref
    * Return "true" or "false"
    */
    public static function validateFields(&$errorMessage)
    {

        if(! Input::get('name')  ) {

            $errorMessage="Name field cannot be empty";
            return false;

        }
        if(! Input::get('nrc')) {

            $errorMessage="NRC field cannot be empty";
            return false;

        }
        if(! Input::get('state')) {

            $errorMessage="State field cannot be empty";
            return false;

        }
        if(! Input::get('township')) {

            $errorMessage="Township field cannot be empty";
            return false;

        }

        if(! Input::get('address')) {

            $errorMessage="Address field cannot be empty";
            return false;

        }

        if(! Input::get('email')) {

            $errorMessage="email field cannot be empty";
            return false;

        }
        if(! Input::get('password')) {

            $errorMessage="password field cannot be empty";
            return false;

        }
        if(Input::get('email')) {

            $user = User::where('email',Input::get('email'))->get();
            if(count($user->toArray())>0) {

                $errorMessage="User already exit";
                return false;

            }
        }
        return true;

    }



    /*
   * Validate the project fields's data
   * @errorMessage is passed by Ref
   * Return "true" or "false"
   */
    public static function validateProjectFields(&$errorMessage)
    {
        if(! Input::get('borrower_id')  ) {

            $errorMessage = "Borrower id field cannot be empty";
            return false;

        }
        if(! Input::get('project_title')  ) {

            $errorMessage = "Project title field cannot be empty";
            return false;

        }
        if(! Input::get('category_id')  ) {

            $errorMessage = "Category id field cannot be empty";
            return false;

        }
        if(! Input::get('loan_value')  ) {

            $errorMessage = "Loan value field cannot be empty";
            return false;

        }
        if(! Input::get('minimum_investment_amount')  ) {

            $errorMessage = "minimum investment amount field cannot be empty";
            return false;

        }
        if( Input::get('collateral_availability')==""  ) {

            $errorMessage = "collateral availability field cannot be empty";
            return false;

        }

        if(! Input::get('state')  ) {

            $errorMessage = "state cannot be empty";
            return false;

        }
        if(! Input::get('township')  ) {

            $errorMessage = "collateral description field cannot be empty";
            return false;

        }

        if(! Input::get('project_period')  ) {

            $errorMessage = "project period field cannot be empty";
            return false;

        }
        if(! Input::get('project_location')  ) {

            $errorMessage = "project location field cannot be empty";
            return false;

        }
        if(! Input::get('project_image')  ) {

            $errorMessage = "project image field cannot be empty";
            return false;

        }
        if(! Input::get('project_description')  ) {

            $errorMessage = "project description field cannot be empty";
            return false;

        }
        if(! Input::get('fund_closing_date')  ) {

            $errorMessage = "fund closing date field cannot be empty";
            return false;

        }
        if(! Input::get('project_start_date')  ) {

            $errorMessage = "project start date field cannot be empty";
            return false;

        }
        if(! Input::get('project_end_date')  ) {

            $errorMessage = "project end date field cannot be empty";
            return false;

        }
        if(! Input::get('status')  ) {

            $errorMessage = "status field cannot be empty";
            return false;

        }
        if(! Input::get('commodity')  ) {

            $errorMessage = "commodity field cannot be empty";
            return false;

        }
        return true;

    }


    /*
   * Validate the investment fields's data
   * @errorMessage is passed by Ref
   * Return "true" or "false"
   */
    public static function validateInvestmentFields(&$errorMessage)
    {
        if(! Input::get('project_id')  ) {

            $errorMessage = "Project id field cannot be empty";
            return false;

        }
        if(! Input::get('lender_id')  ) {

            $errorMessage = "Lender id field cannot be empty";
            return false;

        }
        if(! Input::get('investment_date')  ) {

            $errorMessage = "Investment date field cannot be empty";
            return false;

        }
        if(! Input::get('amount')  ) {

            $errorMessage = "Amount field cannot be empty";
            return false;

        }
        if(! Input::get('display_amount')  ) {

            $errorMessage = "Display amount field cannot be empty";
            return false;
        }
        if(! Input::get('transaction_no')  ) {

            $errorMessage = "Transaction no field cannot be empty";
            return false;

        }
        if(! Input::get('investment_type')  ) {

            $errorMessage = "Investment type field cannot be empty";
            return false;

        }
        if(! Input::get('investment_details')  ) {

            $errorMessage = "Investment details field cannot be empty";
            return false;

        }

        return true;

    }

}