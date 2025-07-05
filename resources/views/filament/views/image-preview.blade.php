<div class="space-y-4 p-4">
    @if(!empty($pengukuran))
        <div>
            <h3 class="font-medium">Foto Pengukuran</h3>
            <div class="grid grid-cols-2 gap-2 mt-2">
                @foreach($pengukuran as $url)
                    <img src="{{ $url }}" class="rounded border shadow-sm">
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($realisasi))
        <div>
            <h3 class="font-medium">Foto Realisasi</h3>
            <div class="grid grid-cols-2 gap-2 mt-2">
                @foreach($realisasi as $url)
                    <img src="{{ $url }}" class="rounded border shadow-sm">
                @endforeach
            </div>
        </div>
    @endif
</div>