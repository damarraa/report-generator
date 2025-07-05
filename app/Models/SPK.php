<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPK extends Model
{
    protected $table = 's_p_k_s';

    protected $fillable = [
        'nomor_spk',
    ];

    public function berita_acaras()
    {
        return $this->hasMany(BeritaAcara::class, 'spk_id');
    }
}
