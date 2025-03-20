<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    public function candidates():HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}
