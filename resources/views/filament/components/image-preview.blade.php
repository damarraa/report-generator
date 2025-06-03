<div class="grid grid-cols-3 gap-4 mt-4">
    @foreach ($images as $image)
        <div class="relative group">
            <img src="{{ Storage::url($image) }}" alt="Preview"
                class="rounded-lg w-full h-32 object-cover border border-gray-200">
            <div
                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                <a href="{{ Storage::url($image) }}" target="_blank" class="text-white hover:text-blue-300 mx-1"
                    title="Lihat Full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>
                <a href="{{ Storage::url($image) }}" download class="text-white hover:text-blue-300 mx-1"
                    title="Download">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
            </div>
        </div>
    @endforeach
</div>
