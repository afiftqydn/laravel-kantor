<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
