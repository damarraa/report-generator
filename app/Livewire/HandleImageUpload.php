<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HandleImageUpload extends Component
{
    use WithFileUploads;

    public $foto_pengukuran = [];

    public function updatingFotoPengukuran($value)
    {
        $this->dispatch('compression-started');

        if (is_array($value)) {
            foreach ($value as $file) {
                $this->compressAndStore($file);
            }
        }

        $this->dispatch('compression-finished');
    }

    protected function compressAndStore($file)
    {
        try {
            $originalSize = $file->getSize();
            $filename = 'bap/' . Str::random(20) . '.jpg';

            $img = Image::make($file->getRealPath())
                ->resize(1920, null, fn($c) => $c->aspectRatio()->upsize())
                ->encode('jpg', 65);

            Storage::disk('public')->put($filename, $img);

            $compressedSize = Storage::disk('public')->size($filename);
            $reduction = 100 - round(($compressedSize / $originalSize) * 100);

            Log::info("Image compressed", [
                'original' => $this->formatBytes($originalSize),
                'compressed' => $this->formatBytes($compressedSize),
                'reduction' => $reduction . '%'
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error("Compression failed: " . $e->getMessage());
            return $file->store('bap', 'public');
        }
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.handle-image-upload');
    }
}
