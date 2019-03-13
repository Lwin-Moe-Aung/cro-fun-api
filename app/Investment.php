<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    //
    protected $fillable = [
        'project_id',
        'lender_id',
        'investment_date',
        'amount',
        'profit_estimation',
        'profit_percentage',
        'display_amount',
        'transaction_no',
        'investment_type',
        'investment_details',
        'status'
    ];
}
