<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyulang extends Model
{
    protected $table = 'penyulangs';

    protected $fillable = [
        'penyulang_gardu',
    ];

    public function berita_acaras()
    {
        return $this->hasMany(BeritaAcara::class);
    }
}
