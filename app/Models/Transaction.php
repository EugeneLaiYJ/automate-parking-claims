<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'date_time',
        'type',
        'sector',
        'entry_location',
        'exit_location',
        'amount',
    ];
}
