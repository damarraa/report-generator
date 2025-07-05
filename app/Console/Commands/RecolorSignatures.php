<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Log;

class RecolorSignatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recolor-signatures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengubah warna tanda tangan pada Berita Acara';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses mengubah warna tanda tangan...');

        // --- UBAH BAGIAN INI ---
        // Ambil semua laporan yang memiliki setidaknya satu tanda tangan
        $reports = BeritaAcara::where(function ($query) {
            $query->whereNotNull('signature_pengawas')
                ->orWhereNotNull('signature_pelaksana')
                ->orWhereNotNull('signature_kontraktor');
        })->get();
        // --- AKHIR PERUBAHAN ---

        if ($reports->isEmpty()) {
            $this->info('Tidak ada tanda tangan untuk diproses.');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($reports->count());
        $progressBar->start();

        foreach ($reports as $report) {
            try {
                // --- PROSES SEMUA KOLOM SIGNATURE DI SINI ---
                $report->signature_pengawas = $this->recolorImage($report->signature_pengawas);
                $report->signature_pelaksana = $this->recolorImage($report->signature_pelaksana);
                $report->signature_kontraktor = $this->recolorImage($report->signature_kontraktor);

                $report->save();
            } catch (\Exception $e) {
                $this->error(" Gagal memproses ID Berita Acara: {$report->id}. Error: " . $e->getMessage());
                Log::error("Gagal recolor signature untuk Berita Acara ID {$report->id}", ['error' => $e->getMessage()]);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\n\nProses selesai!");

        return 0;
    }

    /**
     * Fungsi untuk mengubah warna gambar dari base64 string.
     *
     * @param string|null $base64String
     * @return string|null
     */
    private function recolorImage(?string $base64String): ?string
    {
        if (empty($base64String) || !str_contains($base64String, 'data:image/png;base64,')) {
            return $base64String;
        }

        list(, $data) = explode(',', $base64String);
        $imageData = base64_decode($data);
        $image = imagecreatefromstring($imageData);

        if (!$image) {
            return $base64String;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);

        $black = imagecolorallocatealpha($image, 0, 0, 0, 0);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgba = imagecolorat($image, $x, $y);
                $colors = imagecolorsforindex($image, $rgba);

                // Ubah menjadi hitam jika tidak sepenuhnya transparan
                if ($colors['alpha'] < 127) {
                    imagesetpixel($image, $x, $y, $black);
                }
            }
        }

        ob_start();
        imagepng($image);
        $newImageData = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode($newImageData);
    }
}
