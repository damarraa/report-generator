<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    
    protected $fillable = [
        'customer_name'
    ];

    public function berita_acaras()
    {
        return $this->hasMany(BeritaAcara::class);
    }
}
