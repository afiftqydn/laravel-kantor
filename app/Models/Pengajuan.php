<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Pengajuan extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'judul',
        'deskripsi',
        'status',
        'catatan',
        'user_id',
        'unit_id',
        'sub_unit_id',
        'cabang_id',
        // Jangan masukkan 'lampiran' karena media library pakai tabel media terpisah
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi lampiran bisa dihilangkan jika kamu pakai media library langsung,
    // atau kalau kamu punya model Lampiran, pastikan relasi dan penggunaannya jelas.
    // Jika menggunakan Spatie Media Library, relasi ini tidak perlu:
    // public function lampirans()
    // {
    //     return $this->hasMany(Lampiran::class);
    // }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10);
    }
}
