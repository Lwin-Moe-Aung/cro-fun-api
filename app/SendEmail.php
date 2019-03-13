<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    //
    protected $fillable = [
        'send_email_state',
        'flag'

    ];
}
