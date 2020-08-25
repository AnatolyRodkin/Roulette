<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winning extends Model
{
    protected $fillable = [
        'user_id', 'money', 'bonus', 'prize'
    ];
}
