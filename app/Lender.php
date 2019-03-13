<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lender extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'code_no',
        'nrc',
        'dob',
        'state',
        'township',
        'phone_no',
        'address',
        'photo',
        'attachment',
        'gender',
        'verified'
    ];
    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
