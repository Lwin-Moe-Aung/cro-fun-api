<?php

namespace App\Library\Log;

use App\Library\Log\Contracts\Log;
use App\LogInfo;
use Illuminate\Http\Request;
use DB;

class DBlog implements Log
{


      /*
       * log table format to insert
       */
      public function format($detail,$table,Request $request)
      {
          $login_user = $request->user();
          $data = [

              "user_id" => $login_user['id'],
              "detail" => $detail,
              "table" => $table,
              "date" => date('Y-m-d h:i:s'),
              'url' => url()->current()

          ];
          return $data;
      }

      /*
       * Insert log data into database
       */
      public function save($data)
      {

          LogInfo::create($data);

      }

      /*
       * Retrieve all the logs from database or
       * Retrieve a log info according to parameter(table field)
       */
      public function show($name)
      {
          if($name) {


              $log_info = DB::table('log_infos')
                  ->select('log_infos.*', 'users.name')
                  ->join('users', 'users.id', '=', 'log_infos.user_id')
                  ->where('table',$name)
                  ->get();


              $log=[];
              $detail=[];
              for($i = 0;$i < count($log_info); $i++) {

                  $log[] = $log_info[$i];
                  $detail[] = \GuzzleHttp\json_decode($log_info[$i]->detail);
                  for($j = 0; $j <count($detail); $j++) {

                      $project = DB::table('projects')->select('projects.project_title')
                               ->where('id',"=",$detail[0]->projects_id)->get();
                      $project_title = $project[0]->project_title;
                      $log[$j]->projects_id = $detail[$j]->projects_id;
                      $log[$j]->project_title = $project_title;
                      $log[$j]->message = $detail[$j]->message;
                      $log[$j]->from = $detail[$j]->from;
                      $log[$j]->to = $detail[$j]->to;

                  }

              }
              return $log;




          }

          return LogInfo::all();
      }
}