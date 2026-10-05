<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'Transaction Id',
        'Date/Time',
        'Type',
        'Sector',
        'Entry Location',
        'Exit Location',
        'Amount'
    ];
}
