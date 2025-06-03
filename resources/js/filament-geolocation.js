export default function initGeolocation() {
    document.addEventListener("alpine:init", () => {
        Alpine.data("geolocation", () => ({
            init() {
                // Listen for Livewire event
                this.$wire.on("get-live-location", () => {
                    this.getLiveLocation();
                });

                // Initialize map marker if location exists
                if (this.$wire.get("latitude") && this.$wire.get("longitude")) {
                    window.dispatchEvent(
                        new CustomEvent("location-updated", {
                            detail: {
                                lat: this.$wire.get("latitude"),
                                lng: this.$wire.get("longitude"),
                            },
                        })
                    );
                }
            },

            async getLiveLocation() {
                const button = document.querySelector(
                    "[data-geolocation-button]"
                );
                const errorContainer =
                    document.getElementById("location-error");

                // Reset error
                if (errorContainer) errorContainer.textContent = "";

                if (!navigator.geolocation) {
                    this.showError("Browser tidak mendukung geolokasi");
                    return;
                }

                // Set loading state
                this.setButtonLoading(button, true);

                try {
                    const position = await new Promise((resolve, reject) => {
                        navigator.geolocation.getCurrentPosition(
                            resolve,
                            reject,
                            {
                                enableHighAccuracy: true,
                                timeout: 15000,
                                maximumAge: 0,
                            }
                        );
                    });

                    // Cek akurasi
                    if (position.coords.accuracy > 100) {
                        // Jika akurasi > 100 meter
                        this.showError(
                            "Akurasi lokasi rendah. Pastikan GPS aktif dan dapat sinyal yang baik."
                        );
                        return;
                    }

                    // const lat = position.coords.latitude;
                    // const lng = position.coords.longitude;

                    const {
                        latitude: lat,
                        longitude: lng,
                        accuracy,
                    } = position.coords;

                    // Update Livewire component
                    this.$wire.set("latitude", lat);
                    this.$wire.set("longitude", lng);
                    this.$wire.set("titik_koordinat", [lat, lng]);

                    // Update map
                    window.dispatchEvent(
                        new CustomEvent("location-updated", {
                            detail: { lat, lng, accuracy },
                        })
                    );
                } catch (error) {
                    this.showError(this.getErrorMessage(error));
                } finally {
                    this.setButtonLoading(button, false);
                }
            },

            setButtonLoading(button, isLoading) {
                if (!button) return;

                button.disabled = isLoading;
                button.innerHTML = isLoading
                    ? `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                         xmlns="http://www.w3.org/2000/svg" 
                         fill="none" 
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengambil lokasi...
                `
                    : "Dapatkan Lokasi Saya Sekarang";
            },

            getErrorMessage(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        return "Anda menolak permintaan lokasi. Harap izinkan akses lokasi di pengaturan browser Anda.";
                    case error.POSITION_UNAVAILABLE:
                        return "Informasi lokasi tidak tersedia. Pastikan GPS/Internet aktif.";
                    case error.TIMEOUT:
                        return "Waktu permintaan habis. Coba lagi di area dengan sinyal lebih baik.";
                    default:
                        return (
                            "Gagal mendapatkan lokasi. Error: " + error.message
                        );
                }
            },

            showError(message) {
                const errorContainer =
                    document.getElementById("location-error") ||
                    this.createErrorContainer();
                errorContainer.textContent = message;
            },

            createErrorContainer() {
                const container = document.createElement("div");
                container.id = "location-error";
                container.className = "text-sm text-red-600 mt-2";

                const button = document.querySelector(
                    "[data-geolocation-button]"
                );
                if (button) {
                    button.parentNode.insertBefore(
                        container,
                        button.nextSibling
                    );
                }

                return container;
            },
        }));
    });
}
