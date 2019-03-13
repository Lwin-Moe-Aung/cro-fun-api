<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;



class TaskController extends Controller
{
   public function __invoke()
    {
        return Task::all();
    }
    public function show($id){
    	return Task::findOrFail($id);
    }
}
