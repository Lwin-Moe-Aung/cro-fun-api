<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Input;
use App\Project;
use App\Borrower;
use App\Category;
use App\User;
use App\Library\Library;
use App\Library\DateTimeLibrary;
use DB;
use App\Library\ProjectStatus;
use App\Library\Log\Contracts\Log;


class ProjectController extends ApiController
{
     protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }
    /*
     * Retrieve all the project records which status are open_for_funding and unfunded_open
     */
    public function index()
    {

        $projects= Project::whereIn('status',array('open_for_funding','unfunded_open'))
                            ->where('featured','=',1)
                            ->get();

        return $this->respond([

           'data'=>$projects->toArray(),

        ]);
    }


    /*
     * Retrieve all the project records which status are open_for_funding and unfunded_open
     */
    public function all()
    {

        $limit = Input::get('limit')?:4;
        $projects = Project::whereIn('status',array('open_for_funding','unfunded_open'))->paginate($limit);
        return $this->respond([

            'data'=>$projects->toArray(),

        ]);
    }


    /*
     * Retrieve all the project records
     * And its related category title
     * And its related borrower info
     * And where project's "deleted_at" field is null
     */
    public function Finance()
    {

        $projects = DB::table('projects')
                ->select('projects.id','projects.code_no','projects.project_title','categories.title','users.name', 'borrowers.nrc',DB::raw("CASE
                 WHEN `status` = 'draft' THEN 'Draft'
                WHEN `status` = 'open_for_funding' THEN 'Open for funding'
                WHEN `status` = 'unfunded' THEN 'Unfunded'
                WHEN `status` = 'fully_funded' THEN 'Fully Funded'
                WHEN `status` = 'project_on_going' THEN 'Project on going'
                WHEN `status` = 'harvesting_period' THEN 'Harvesting Period'
                WHEN `status` = 'close_finished' THEN 'Finished'
                WHEN `status` = 'cancelled_open' THEN 'Cancelled'
                WHEN `status` = 'default_open' THEN 'Defaulted'
                ELSE `status`
                 END AS status"))
                ->join('categories', 'categories.id', '=', 'projects.category_id')
                ->join('borrowers','borrowers.id','=','projects.borrower_id')
                ->join('users','users.id','=','borrowers.user_id')
                 ->whereNull('projects.deleted_at')
                 ->get();
        return $this->respond([

            'data'=>$projects

        ]);

    }


    /*
     * Retrieve the particular project's record
     * And its related category title
     * And its related borrower info
     *  by project's id
    */
    public function show($id)
    {

        $project = Project::find($id);
        $borrowers = Borrower::all();
        $categories = Category::all();
        $users = User::all();
        $borrower_project = array();
        if(!$project){

            return $this->respondNotFound('Project does not exist');

        }
        if($project->fund_closing_date != null) {

            // change date from SQL to UI date
            $date=DateTimeLibrary::changeDateFormat($project->fund_closing_date);
            $project->fund_closing_date = $date;

        }
        if($project->project_start_date != null) {

            // change date from SQL to UI date
            $date=DateTimeLibrary::changeDateFormat($project->project_start_date);
            $project->project_start_date = $date;

        }
        if($project->project_end_date != null) {

            // change date from SQL to UI date
            $date=DateTimeLibrary::changeDateFormat($project->project_end_date);
            $project->project_end_date = $date;

        }
        foreach($borrowers as $borrower)
        {
            foreach($users as $user)
            {

                foreach ($categories as $category) {

                    if ($borrower->id == $project->borrower_id & $category->id == $project->category_id & $user->id == $borrower->user_id)
                    {

                            $b_p = $project->toArray();
                            $b_p['nrc'] = $borrower->nrc;
                            $b_p['phone_no'] = $borrower->phone_no;
                            $b_p['state'] = $borrower->state;
                            $b_p['township'] = $borrower->township;
                            $b_p['address'] = $borrower->address;
                            $b_p['photo'] = $borrower->photo;
                            $b_p['points'] = $borrower->points;
                            $b_p['name'] = $user->name;
                            $b_p['email'] = $user->email;
                            $b_p['category'] = $category->title;
                            $borrower_project[] = $b_p;

                    }
                }
            }

        }
        return $this->respond([

            'data'=>$borrower_project

        ]);

    }


    /*
     * Insert the project's record into the database
     */
    public function store()
    {

        $errorMessage = "";
        if (Library::validateProjectFields($errorMessage)==false){

            return $this->setStatusCode(422)->respondWithError($errorMessage);

        }
        DateTimeLibrary::changeInputDateFormat();
        $project = new Project();
        $project->borrower_id = Input::get('borrower_id');
        $project->field_officers_id = Input::get('field_officers_id');
        $project->code_no = "PRJ-".DateTimeLibrary::searchCodeNo('projects','-');
        $project->project_title = Input::get('project_title');
        $project->category_id = Input::get('category_id');
        $project->loan_value = Input::get('loan_value');
        $project->return_estimation_proposed = Input::get('return_estimation_proposed');
        $project->minimum_investment_amount = Input::get('minimum_investment_amount');
        $project->collateral_availability = Input::get('collateral_availability');
        $project->collateral_estimated_value = Input::get('collateral_estimated_value');
        $project->collateral_description = Input::get('collateral_description');
        $project->collateral_evidence = Input::get('collateral_evidence');
        $project->project_period = Input::get('project_period');
        $project->state = Input::get('state');
        $project->township = Input::get('township');
        $project->project_location = Input::get('project_location');
        $project->project_image = Input::get('project_image');
        $project->project_description = Input::get('project_description');
        $project->fund_closing_date = Input::get('fund_closing_date');
        $project->project_start_date = Input::get('project_start_date');
        $project->project_end_date = Input::get('project_end_date');
        $project->status = Input::get('status');
        $project->commodity = Input::get('commodity');
        $project->save();
        return $this->respondCreated('Project is Successfully inserted');

    }

    /*
     * Update the project's record
     * By project's id
     */
    public function update($id, Request $request)
    {

        $project = Project::where('id', $id)->first();
        $old_status= $project->status;

        if (!$project) {
            return $this->respondNotFound('Project does not exist');
        }
        DateTimeLibrary::changeInputDateFormat();
        $inputs = Input::all();
       Project::where('id',$id)->update($inputs);

        /*
        * Insert Log
        */
        $detail = [
            'projects_id' => $project->id,
            'message' => 'Project\'s status is changed',
            'from' => $old_status,
            'to' => Input::get('status')
        ];

        $detail = json_encode($detail,true);
        $table = "projects".$project->id;
        $data = $this->log->format($detail,$table,$request);
        $this->log->save($data);
        
        // end of inserting log
        return $this->respondCreated('Project is successfully Updated');

    }


    /*
     * Soft delete the project's record
     * By project's  id
     */
    public function destroy($id)
    {
        $project = Project::find($id);
        if(! $project) {

            return $this->respondNotFound('Project does not exist');

        }
        $payment = DB::table('payments')
            ->select('*')
            ->where('project_id','=',$project->id)
            ->get();
        $profit = DB::table('profits')
            ->select('*')
            ->where('project_id','=',$project->id)
            ->get();
        $investment = DB::table('investments')
            ->select('*')
            ->where('project_id','=',$project->id)
            ->get();

        if(count($payment)>0 || count($profit)>0 || count($investment)>0) {

            return $this->respondCreated('Can not delete the project(Code No: '.$project->code_no.') because there are records related to this project');

        }
        $project->delete();

    }

    /*
     * Check the project's code no whether it is already in the database or not
     */
    /*
    public function searchCodeNo($value)
    {
        $project=Project::where('code_no',$value)->first();
        if(!$project) {

            return $this->respondNotFound('No record found');

        }
        return $this->respond([

            'data'=>$project->toArray()

        ]);
    }*/

}