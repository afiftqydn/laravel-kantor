<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lampiran extends Model
{
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
