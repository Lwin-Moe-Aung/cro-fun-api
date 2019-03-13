<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Location;
use App\Library\DateTimeLibrary;


class LocationController extends ApiController
{


    /*
     * Retrieve all the state
     */
    public function state()
    {

        $states = Location::select('state')->groupBy('state')->get()->toArray() ;
        return $this->respond([

            'data'=>$states

        ]);

    }


    /*
     * Retrieve the township
     * By state
     */
    public function township($state)
    {

        $townships = Location::where('state',$state)->get();
        return $this->respond([

            'data'=>$townships

        ]);

    }


    /*
     * Retrieve all the townships
     */
    public function townships()
    {

        $township = Location::select('township')->groupBy('township')->get()->toArray() ;
        return $this->respond([

            'data'=>$township

        ]);

    }



}
