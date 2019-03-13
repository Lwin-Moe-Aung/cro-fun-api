<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfitDistribution extends Model
{
    protected $fillable = [
        'profit_id',
        'lender_id',
        'profit',
        'revenue',
        'profit_paid_date',
        'status',
        'transaction_no',
        'profit_distribution_percentage'

    ];
}
