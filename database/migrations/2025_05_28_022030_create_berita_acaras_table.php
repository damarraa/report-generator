<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penyulangs', function (Blueprint $table) {
            $table->id();
            $table->string('penyulang_gardu');
            $table->index('penyulang_gardu');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->index('customer_name');
            $table->timestamps();
        });

        Schema::create('jointers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jointer');
            $table->index('nama_jointer');
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_name');
            $table->index('material_name');
            $table->timestamps();
        });

        Schema::create('s_p_k_s', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_spk');
            $table->timestamps();
        });

        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bap');
            $table->enum('jenis_pekerjaan', ['jointing', 'terminating']);
            $table->enum('pekerjaan', ['pasang baru', 'gangguan tanpa tambah kabel', 'gangguan dengan tambah kabel']);
            $table->string('pelaksana_gali');
            $table->string('arah_gardu');
            $table->string('lokasi_pekerjaan');
            $table->json('galian_perbaikan')->nullable();
            $table->json('gangguan')->nullable();
            $table->json('cek_fisik_kabel_gangguan')->nullable();
            $table->json('cek_tahanan_isolasi_awal')->nullable();
            $table->json('cek_fisik_kabel_tambahan')->nullable();
            $table->json('material')->nullable();
            $table->json('cek_tahanan_isolasi_akhir')->nullable();
            $table->json('pekerjaan_lain')->nullable();
            $table->text('titik_koordinat');

            $table->json('waktu_pemasangan')->nullable();
            $table->json('peralatan_kerja')->nullable();
            $table->json('seragam_kerja')->nullable();
            $table->json('peralatan_k2')->nullable();
            $table->json('label_timah')->nullable();

            $table->text('catatan_pekerjaan')->nullable();
            $table->json('foto_pengukuran')->nullable();
            $table->json('foto_realisasi')->nullable();

            $table->text('signature_pengawas')->nullable();
            $table->text('signature_pelaksana')->nullable();
            $table->text('signature_kontraktor')->nullable();
            $table->string('nama_pengawas')->nullable();
            $table->string('nama_pelaksana')->nullable();
            $table->string('nama_kontraktor')->nullable();

            $table->foreignId('penyulang_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('spk_id')->constrained('s_p_k_s')->onDelete('cascade');
            // $table->foreignId('jointer_id')->constrained()->onDelete('cascade');
            $table->foreignId('leader_id')->nullable()->constrained('jointers')->onDelete('set null');
            $table->foreignId('jointer_id')->nullable()->constrained('jointers')->onDelete('set null');
            $table->foreignId('helper_id')->nullable()->constrained('jointers')->onDelete('set null');

            $table->timestamps();

            $table->index('pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyulangs');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('jointers');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('s_p_k_s');
        Schema::dropIfExists('berita_acaras');
    }
};
