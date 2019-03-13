<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable=[
        'borrower_id',
        'field_officers_id',
        'code_no',
        'project_title',
        'category_id',
        'loan_value',
        'return_estimation_proposed',
        'return_estimation_approved',
        'minimum_investment_amount',
        'collateral_availability',
        'collateral_estimated_value',
        'collateral_description',
        'collateral_evidence',
        'project_period',
        'state',
        'township',
        'project_location',
        'project_image',
        'project_description',
        'fund_closing_date',
        'project_start_date',
        'project_end_date',
        'status',
        'featured',
        'comment',
        'commodity'
    ];
    protected $dates = ['deleted_at'];
}
