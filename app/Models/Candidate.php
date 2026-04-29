<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Position;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->img_path) {
            return null;
        }

        if (Storage::disk('public')->exists($this->img_path)) {
            return Storage::disk('public')->url($this->img_path);
        }

        return asset('candidates/' . $this->img_path);
    }
}
