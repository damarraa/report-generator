<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    use HasFactory;

    protected $table = 'berita_acaras';

    protected $fillable = [
        'nomor_bap',
        'jenis_pekerjaan',
        'pekerjaan',
        'no_spk',
        'pelaksana_gali',
        'arah_gardu',
        'lokasi_pekerjaan',
        'galian_perbaikan',
        'gangguan',
        'cek_fisik_kabel_gangguan',
        'cek_tahanan_isolasi_awal',
        'cek_fisik_kabel_tambahan',
        'material',
        'cek_tahanan_isolasi_akhir',
        'pekerjaan_lain',
        'titik_koordinat',
        'waktu_pemasangan',
        'peralatan_kerja',
        'catatan_peralatan_kerja',
        'seragam_kerja',
        'catatan_seragam_kerja',
        'peralatan_k2',
        'catatan_peralatan_k2',
        'label_timah',
        'catatan_label_timah',
        'catatan_pekerjaan',
        'foto_pengukuran',
        'foto_realisasi',
        'signature_pengawas',
        'nama_pengawas',
        'signature_pelaksana',
        'nama_pelaksana',
        'signature_kontraktor',
        'nama_kontraktor',
        'customer_id',
        'penyulang_id',
        'jointer_id',
        'user_id'
    ];

    protected $casts = [
        'galian_perbaikan' => 'array',
        'gangguan' => 'array',
        'cek_fisik_kabel_gangguan' => 'array',
        'cek_tahanan_isolasi_awal' => 'array',
        'material' => 'array',
        'cek_fisik_kabel_tambahan' => 'array',
        'cek_tahanan_isolasi_akhir' => 'array',
        'pekerjaan_lain' => 'array',
        'titik_koordinat' => 'array',
        'jointer_pelaksana' => 'array',
        'waktu_pemasangan' => 'array',
        'peralatan_kerja' => 'array',
        'seragam_kerja' => 'array',
        'peralatan_k2' => 'array',
        'label_timah' => 'array',
        'foto_pengukuran' => 'array',
        'foto_realisasi' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function penyulang()
    {
        return $this->belongsTo(Penyulang::class);
    }

    public function leader()
    {
        return $this->belongsTo(Jointer::class, 'leader_id');
    }

    public function jointer()
    {
        return $this->belongsTo(Jointer::class, 'jointer_id');
    }

    public function helper()
    {
        return $this->belongsTo(Jointer::class, 'helper_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
