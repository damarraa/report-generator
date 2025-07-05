<div x-show="$wire.isUploading" 
     x-transition
     class="p-4 mb-4 bg-blue-50 rounded-lg">
    <div class="flex items-center space-x-2">
        <x-filament::loading-indicator class="w-5 h-5 text-blue-500" />
        <span class="text-sm font-medium text-blue-700">
            Mengkompresi dan mengunggah gambar...
        </span>
        <span x-text="$wire.uploadProgress + '%'" class="text-sm text-blue-600"></span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
        <div class="bg-blue-600 h-2.5 rounded-full" 
             :style="'width: ' + $wire.uploadProgress + '%'"></div>
    </div>
</div>