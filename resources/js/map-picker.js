// resources/js/map-picker.js
export default function initMapPicker() {
    document.addEventListener("alpine:init", () => {
        Alpine.data("mapPicker", (config) => ({
            map: null,
            marker: null,
            errorMessage: "",

            // Tile Layers
            tileLayers: {
                OpenStreetMap: L.tileLayer(
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                    { attribution: "© OpenStreetMap" }
                ),
                "Satellite (Esri)": L.tileLayer(
                    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                    {
                        attribution: "Tiles © Esri",
                        maxZoom: 19,
                    }
                ),
            },

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.setupEventListeners();
                });
            },

            initMap() {
                // Inisialisasi peta
                this.map = L.map(`map-${config.statePath}`).setView(
                    [config.lat, config.lng],
                    config.zoom
                );

                // Tambahkan layer default
                this.tileLayers["OpenStreetMap"].addTo(this.map);

                // Kontrol layer
                L.control
                    .layers(this.tileLayers, null, {
                        position: "topright",
                    })
                    .addTo(this.map);

                // Marker awal
                this.updateMarker(config.lat, config.lng);
            },

            updateMarker(lat, lng) {
                if (this.marker) {
                    this.map.removeLayer(this.marker);
                }

                this.marker = L.marker([lat, lng], {
                    draggable: true,
                }).addTo(this.map);

                // Update Livewire saat marker di-drag
                this.marker.on("dragend", (e) => {
                    const { lat, lng } = e.target.getLatLng();
                    this.$wire.set(config.statePath, { lat, lng });
                    this.$wire.set("latitude", lat);
                    this.$wire.set("longitude", lng);
                });
            },

            // Geolocation
            async getLiveLocation() {
                if (!navigator.geolocation) {
                    this.showError("Browser tidak mendukung geolokasi.");
                    return;
                }

                try {
                    const position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(
                            resolve,
                            reject,
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                            }
                        );
                    });

                    const { latitude: lat, longitude: lng } = position.coords;
                    this.map.setView([lat, lng], 15);
                    this.updateMarker(lat, lng);

                    // Update Livewire
                    this.$wire.set(config.statePath, { lat, lng });
                    this.$wire.set("latitude", lat);
                    this.$wire.set("longitude", lng);
                } catch (error) {
                    this.showError(this.getGeolocationError(error));
                }
            },

            // Error Handling
            showError(message) {
                const errorElement = document.getElementById(
                    `location-error-${config.statePath}`
                );
                if (errorElement) errorElement.textContent = message;
            },

            getGeolocationError(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        return "Akses lokasi ditolak. Izinkan akses di pengaturan browser.";
                    case error.TIMEOUT:
                        return "Waktu permintaan habis. Coba lagi.";
                    default:
                        return "Gagal mendapatkan lokasi.";
                }
            },

            // Sync dengan input form
            setupEventListeners() {
                this.$wire.on("update-map", ({ lat, lng }) => {
                    this.map.setView([lat, lng]);
                    this.updateMarker(lat, lng);
                });
            },
        }));
    });
}
