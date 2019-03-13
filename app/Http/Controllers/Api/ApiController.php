<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;


class ApiController extends Controller
{

    protected $statusCode=200;

    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function setStatusCode($statusCode)
    {
        $this->statusCode=$statusCode;
        return $this;
    }
    public function respondNotFound($message='Not Found!')
    {
        return $this->setStatusCode(\Illuminate\Http\Response::HTTP_NOT_FOUND)->respondWithError($message);

    }
    public function respondInternalError($message='Internal Error!')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }
    public function respond($data,$headers=[])
    {
       
        return Response::json($data ,$this->getStatusCode(),$headers);
    }
    public function respondWithError($message)
    {
        return $this->respond([
            'error'=>[
                'message'=>$message,
                'status_code'=>$this->getStatusCode()
            ]
        ]);
    }
    public function respondCreated($message)
    {
        return $this->setStatusCode(201)->respond([
            'message'=>$message
        ]);
    }

}
