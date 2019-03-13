<?php
namespace App\Services\Investment;

use App\Lender;
use App\User;
use App\Investment;
use Carbon\Carbon;
use DB;
use App\Library\DateTimeLibrary;

class InvestmentService
{
    /**
     * retrieve total investment and count the projects via investments table
     * according by lender_id and investments's status must be approved and 
     * projects's status must be project on going.
     */
	public static function getActiveInvestments($id)
	{	

		$result = DB::table('investments')       
			->select(DB::raw('SUM(investments.amount) as totalinvest'),DB::raw('COUNT(projects.id) as projectnumbers'))
	        ->join('projects','projects.id','=','investments.project_id')
	        ->where('investments.lender_id',$id) 
	        ->where('investments.status',"approved") 
	        ->where('projects.status',"project_on_going") 
	        ->get()
	        ->toArray();


        return $result;           

	}

    /**
     * retrieve total investment and count the projects via the investments's table 
     * according by lender_id and investment's status must be approved and 
     * projects's status must be open for funding.
     */
	public static function getFundingProcess($id)
	{
		$result = DB::table('investments')
            ->select(DB::raw('SUM(investments.amount) as totalinvest'),DB::raw('COUNT(projects.id) as projectnumbers'))
            ->join('projects','projects.id','=','investments.project_id')
            ->where('investments.lender_id',$id) 
            ->where('investments.status',"approved") 
            ->where('projects.status',"open_for_funding") 
            ->get()
            ->toArray();
                    
        return $result;            
	}

    /**
     * the combination of all other methods in here
     */
	public static function getLenderInvestments($id)
	{	
		$active["active"] = self::getActiveInvestments($id);
		$funding["funding"] = self::getFundingProcess($id);
		
		$total_profit["total_profit"] =  self::getTotalProfitRealization($id);
		$total_invest_projects["total_invest_projects"] =  self::getTotalInvestmentProject($id);
		$total_profit_exception["total_profit_exception"] =  self::summaryProfitException($id);
        $lender_projects_profits["lender_projects_profits"] = self::lenderProjectProfitByDate($id);
        
        return array_merge($active, $funding, $total_profit, $total_invest_projects,$total_profit_exception,$lender_projects_profits);
	}

    /**
     * retrieve the total profit distribution amount by investment's lender_id .
     */
	public static function getTotalProfitRealization($id)
	{
		$result = DB::table('investments')
            ->select(DB::raw('SUM(profit_distributions.profit) as total_profit'))
            ->join('profit_distributions','profit_distributions.investment_id','=','investments.id')
            ->where('investments.lender_id',$id)
            ->get()
           	->toArray();

        return $result;
	}

    /**
     * retrieve the total number of project by investments's lender_id.
     */
	public static function getTotalInvestmentProject($id)
	{
		$result = DB::table('investments')
            ->select(DB::raw('COUNT(investments.project_id) as total_invest_projects'))
            ->where('investments.lender_id',$id)
            ->get()
            ->toArray();

        return $result;
	}

    /**
     * retrieve the total profit estimation by investments's lender_id and the investments's status
     * must be approved and projects's status must be project on going.
     */
    public static function summaryProfitException($id)
    {
        $result = DB::table('investments')
            ->select(DB::raw('SUM(investments.profit_estimation) as total_profit'))
            ->join('projects','projects.id','=','investments.project_id')
            ->where('investments.lender_id',$id)
            ->where('investments.status',"approved")
            ->where('projects.status',"project_on_going")
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * retrieve the investment record and project record according to current month and 
     * investments's lender_id .
     */
    public static function lenderProjectProfitByDate($id)
	{
		$current_month = date('m ');
		//$month_word=date('M');
		$current_year = date('Y');

        $investment = DB::table('investments')
            ->select('investments.*', 'projects.project_title', 'projects.project_end_date')
            ->join('projects', 'projects.id', '=', 'investments.project_id')
            ->where('investments.lender_id', $id)
            ->whereMonth('projects.project_end_date', $current_month)
            ->whereYear('projects.project_end_date', $current_year)
            ->whereNOTIn('investments.id', function($query){
                $query->select('profit_distributions.investment_id')->from('profit_distributions');
            })
            ->get()
		    ->toArray();

        return $investment;
	}
}
?>

