@php
    $statePath = $getStatePath();
    $state = $getState() ?? ['lat' => 0.5071, 'lng' => 101.4478]; // Fallback ke Pekanbaru
    $defaultCenter = [$state['lat'], $state['lng']];
@endphp

<div
    x-data="mapPicker()"
    data-state-path="{{ $statePath }}"
    data-state="@json($state)"
    data-default-center="@json($defaultCenter)"
    wire:ignore
    {{-- PERBAIKAN KUNCI ADA DI SINI: ditambahkan 'overflow-hidden' --}}
    {{ $attributes->merge($getExtraAttributes())->class(['w-full', 'relative', 'rounded-lg', 'overflow-hidden', 'z-0']) }}
>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1000] pointer-events-none">
        <svg class="w-8 h-8 text-red-500 drop-shadow-lg" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
        </svg>
    </div>

    <button
        type="button"
        x-on:click="getUserLocation"
        class="absolute top-3 right-3 z-[1000] flex items-center justify-center w-10 h-10 bg-white rounded-md shadow-md text-gray-700 hover:bg-gray-50"
        title="Dapatkan Lokasi Saat Ini"
    >
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
    </button>

    <div x-ref="map" class="w-full h-full rounded-lg relative" style="height: 400px;"></div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mapPicker', () => ({
                statePath: '', defaultCenter: [], map: null,

                init() {
                    delete L.Icon.Default.prototype._getIconUrl;
                    L.Icon.Default.mergeOptions({
                        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    });

                    this.statePath = this.$el.dataset.statePath;
                    this.defaultCenter = JSON.parse(this.$el.dataset.defaultCenter);
                    
                    this.initMap(this.defaultCenter[0], this.defaultCenter[1]);
                    
                    this.$nextTick(() => this.getUserLocation()); 
                },

                initMap(lat, lng) {
                    this.map = L.map(this.$refs.map, { center: [lat, lng], zoom: 17 });

                    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri'
                    }).addTo(this.map);

                    this.map.on('moveend', () => {
                        const center = this.map.getCenter();
                        this.$wire.set(this.statePath, { lat: center.lat, lng: center.lng });
                    });
                },

                async getUserLocation() {
                    if (!navigator.geolocation) {
                        return alert('Browser Anda tidak mendukung Geolocation.');
                    }
                    try {
                        const position = await new Promise((resolve, reject) => {
                            navigator.geolocation.getCurrentPosition(resolve, reject, {
                                enableHighAccuracy: true, timeout: 10000, maximumAge: 0
                            });
                        });
                        const newCoords = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        this.map.setView(newCoords, 17);
                    } catch (error) {
                        alert(`Gagal mendapatkan lokasi: ${error.message}`);
                    }
                }
            }));
        });
    </script>
</div>