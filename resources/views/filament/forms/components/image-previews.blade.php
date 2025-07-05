@props(['state'])

@if (is_array($state) && count($state) > 0)
    <div class="mb-4">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Gambar Saat Ini:</p>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
            @foreach ($state as $path)
                <div class="aspect-square bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($path) }}" alt="Preview"
                        class="object-cover w-full h-full rounded-lg">
                </div>
            @endforeach
        </div>
    </div>
    <hr class="my-4 border-gray-200 dark:border-gray-700">
@endif
