<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jointer extends Model
{
    protected $table = 'jointers';

    protected $fillable = [
        'nama_jointer'
    ];

    public function berita_acaras()
    {
        return $this->hasMany(BeritaAcara::class);
    }
}
