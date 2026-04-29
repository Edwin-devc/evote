<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $casts = [
        'has_voted' => 'boolean',
    ];
}
