<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\ProjectProgress;
use App\Library\DateTimeLibrary;
use DB;

class ProjectProgressController extends ApiController
{

    /*
    * Insert the project progress's record into the database
    */
    public function store(Request $request)
    {

        DateTimeLibrary::changeInputDateFormat();
        $progress = new ProjectProgress();
        $progress->percentage = $request->get('percentage');
        $progress->attachment = $request->get('attachment');
        $progress->remark = $request->get('remark');
        $progress->project_id = $request->get('project_id');
        $progress->progress_date = $request->get('progress_date');
        $progress->save();
        return $this->respondCreated('Project Progress is successfully stored');

    }


    /*
    * Retrieve the particular project progress's record
    *  by project progress's project id
   */
    public function show($id)
    {
        $progress = ProjectProgress::where('project_id',$id)->get();
        if ($progress) {
            return $this->respond([

                'data' => $progress

            ]);
        }
        return $this->respondNotFound('There is no record');

    }


    /*
     * Retrieve the particular project progress's record
     * And its related project's info
     * By  project progress's id
     */
    public function detail($id)
    {
        $progress = DB::table('project_progresses')
            ->select('project_progresses.*','projects.project_title')
            ->join('projects','projects.id','=','project_progresses.project_id')
            ->where('project_progresses.id','=',$id)
            ->get();

        if (!empty($progress[0])) {

            $progress_date=DateTimeLibrary::changeDateFormat($progress[0]->progress_date);
            $progress[0]->progress_date = $progress_date;
            return $this->respond([

                'data' => $progress

            ]);

        }
        return $this->respondNotFound('There is no record');

    }
}
