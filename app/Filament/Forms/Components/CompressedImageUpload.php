<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class CompressedImageUpload extends Field
{
    protected string $view = 'filament.forms.components.compressed-image-upload';

    // 1. Definisikan sebuah properti untuk menyimpan nilai maxFiles, beri nilai default.
    protected int $maxFiles = 8;

    /**
     * 2. Buat method publik bernama 'maxFiles'.
     * Ini adalah method yang akan dipanggil saat Anda menulis `->maxFiles(6)`
     * di dalam Resource Anda.
     */
    public function maxFiles(int $max): static
    {
        $this->maxFiles = $max;

        return $this; // Kembalikan $this agar method bisa di-chain (dirangkai)
    }

    /**
     * 3. Buat method 'getter' agar nilai ini bisa diakses dari file Blade.
     * Metode ini akan dipanggil oleh kode `{{ $getMaxFiles() }}` di dalam view.
     */
    public function getMaxFiles(): int
    {
        return $this->maxFiles;
    }
}
