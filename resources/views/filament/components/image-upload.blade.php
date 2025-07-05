<div x-data="{ showProgress: false }" class="space-y-2">
    <!-- Progress Bar -->
    <div x-show="showProgress" class="mb-2">
        <div class="text-xs text-gray-600 mb-1">Mengkompresi gambar...</div>
        <div id="upload-progress" class="h-2 bg-blue-500 rounded transition-all duration-300"></div>
    </div>

    <!-- FileUpload Field -->
    {{ $this->form->getComponent('foto_pengukuran') }}
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('compression-progress', (progress) => {
            const progressBar = document.getElementById('upload-progress');
            progressBar.style.width = `${progress}%`;
            progressBar.innerText = `${progress}%`;
        });
    });
</script>
