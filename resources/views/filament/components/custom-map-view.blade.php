<x-filament::fieldset>
    <x-slot name="label">
        {{ $getLabel() }}
    </x-slot>

    <div 
        x-data="customMapPicker({
            lat: {{ $getState()['lat'] ?? -6.2088 }},
            lng: {{ $getState()['lng'] ?? 106.8456 }},
            statePath: '{{ $getStatePath() }}',
            tileLayers: [
                {
                    name: 'OpenStreetMap',
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                },
                {
                    name: 'Satellite (Esri)',
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    attribution: 'Tiles © Esri — Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                }
            ]
        })"
        wire:ignore
        style="height: 400px; width: 100%;"
        id="map-{{ $getStatePath() }}"
    ></div>
</x-filament::fieldset>