<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'material_name',
    ];

    public function berita_acaras()
    {
        return $this->hasMany(BeritaAcara::class);
    }
}
