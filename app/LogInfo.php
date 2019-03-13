<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogInfo extends Model
{
    protected $fillable=['user_id','detail','table','date','url'];
}
