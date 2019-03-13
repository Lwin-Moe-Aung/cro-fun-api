<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    //
    protected $fillable = [
        'project_id',
        'revenue',
        'profit',
        'profit_generated_date',
        'status',
        'remark',
        'attachment',
        'transaction_no'
    ];
}
