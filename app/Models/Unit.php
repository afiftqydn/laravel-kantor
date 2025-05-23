<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function subUnits()
    {
        return $this->hasMany(SubUnit::class);
    }
}
