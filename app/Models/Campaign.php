<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- AJOUTER CECI

class Campaign extends Model
{
    protected $fillable = ['name','description','start_date', 'end_date', 'status'];

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
