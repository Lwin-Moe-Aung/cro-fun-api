<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectProgress extends Model
{
    protected $fillable = [
        'percentage',
        'attachment',
        'remark',
        'project_id',
        'progress_date'

    ];
}
