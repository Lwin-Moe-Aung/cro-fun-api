<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\Log\Contracts\Log;
use App\Http\Controllers\Api\ApiController;

class LogController extends ApiController
{

    protected $log;

    public function __construct(Log $log)
    {
        $this->log=$log;
    }

    public function show($name=null)
    {
        return $this->respond([

            'data'=>$this->log->show($name)

        ]);



    }
}
