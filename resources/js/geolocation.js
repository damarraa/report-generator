// Versi Updated 2
export default function initGeolocation() {
    document.addEventListener("alpine:init", () => {
        Alpine.data("geolocation", () => ({
            errorMessage: "",

            init() {
                // Listener ini akan dipanggil oleh tombol suffixAction yang baru
                this.$wire.on("get-live-location", () => {
                    this.getLiveLocation();
                });

                // Deteksi otomatis saat halaman dimuat (untuk record baru)
                if (
                    !this.$wire.get("latitude") ||
                    !this.$wire.get("longitude")
                ) {
                    this.getLiveLocation();
                }
            },

            async getLiveLocation() {
                this.errorMessage = "";
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
                                timeout: 15000,
                                maximumAge: 0,
                            }
                        );
                    });

                    const {
                        latitude: lat,
                        longitude: lng,
                        accuracy,
                    } = position.coords;

                    if (accuracy > 50) {
                        this.showError(
                            `Akurasi lokasi rendah (~${Math.round(
                                accuracy
                            )}m). Lokasi mungkin tidak tepat.`
                        );
                    }

                    // Update state Livewire
                    this.$wire.set("latitude", lat);
                    this.$wire.set("longitude", lng);
                    this.$wire.set("titik_koordinat", { lat, lng });

                    window.dispatchEvent(
                        new CustomEvent("location-updated", {
                            detail: { lat, lng, accuracy },
                        })
                    );
                } catch (error) {
                    this.showError(this.getErrorMessage(error));
                }
            },

            getErrorMessage(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        return "Anda menolak permintaan lokasi. Harap izinkan akses lokasi di pengaturan browser Anda.";
                    case error.POSITION_UNAVAILABLE:
                        return "Informasi lokasi tidak tersedia. Pastikan GPS/Internet aktif dan sinyal kuat.";
                    case error.TIMEOUT:
                        return "Waktu permintaan lokasi habis. Coba lagi di area dengan sinyal lebih baik.";
                    default:
                        return `Gagal mendapatkan lokasi. Error: ${
                            error.message || "Tidak dikenal"
                        }`;
                }
            },

            showError(message) {
                const errorContainer =
                    document.getElementById("location-error");
                if (errorContainer) {
                    errorContainer.textContent = message;
                } else {
                    console.error("Geolocation Error:", message);
                }
            },
        }));
    });
}

// Versi Updated
// export default function initGeolocation() {
//     document.addEventListener("alpine:init", () => {
//         Alpine.data("geolocation", () => ({
//             // Properti untuk menyimpan pesan error
//             errorMessage: "",

//             init() {
//                 // Event listener Livewire untuk memicu deteksi lokasi secara manual (saat tombol diklik)
//                 this.$wire.on("get-live-location", () => {
//                     this.getLiveLocation();
//                 });

//                 // **LOGIKA PENTING UNTUK DETEKSI OTOMATIS SAAT HALAMAN DIMUAT**
//                 // Panggil getLiveLocation() saat inisialisasi Alpine.js
//                 // Hanya jika latitude atau longitude belum terisi (misal: record baru)
//                 if (
//                     !this.$wire.get("latitude") ||
//                     !this.$wire.get("longitude")
//                 ) {
//                     this.getLiveLocation();
//                 } else {
//                     // Jika sudah ada lokasi tersimpan (saat mengedit record),
//                     // pastikan peta menampilkan lokasi tersebut dan update pop-up
//                     window.dispatchEvent(
//                         new CustomEvent("location-updated", {
//                             detail: {
//                                 lat: this.$wire.get("latitude"),
//                                 lng: this.$wire.get("longitude"),
//                                 // Jika Anda menyimpan akurasi, ambil juga dari Livewire state
//                                 // accuracy: this.$wire.get("accuracy_field_name")
//                             },
//                         })
//                     );
//                 }
//             },

//             async getLiveLocation() {
//                 const button = document.querySelector(
//                     "[data-geolocation-button]"
//                 );
//                 // Reset pesan error sebelum memulai proses deteksi
//                 this.errorMessage = "";

//                 if (!navigator.geolocation) {
//                     this.showError("Browser tidak mendukung geolokasi.");
//                     this.setButtonLoading(button, false); // Pastikan tombol tidak loading jika ada error awal
//                     return;
//                 }

//                 this.setButtonLoading(button, true); // Set tombol ke state loading

//                 try {
//                     const position = await new Promise((resolve, reject) => {
//                         navigator.geolocation.getCurrentPosition(
//                             resolve,
//                             reject,
//                             {
//                                 enableHighAccuracy: true, // Mencoba akurasi terbaik
//                                 timeout: 15000, // Batas waktu 15 detik
//                                 maximumAge: 0, // Jangan gunakan cache lokasi lama
//                             }
//                         );
//                     });

//                     const {
//                         latitude: lat,
//                         longitude: lng,
//                         accuracy, // Akurasi dalam meter
//                     } = position.coords;

//                     // Opsional: Validasi akurasi
//                     // Sesuaikan batas akurasi sesuai kebutuhan proyekmu.
//                     // Misalnya, jika akurasi terlalu rendah (angka meter besar), bisa diberi peringatan.
//                     if (accuracy > 50) {
//                         // Contoh: Akurasi > 50 meter dianggap rendah
//                         this.showError(
//                             `Akurasi lokasi rendah (~${Math.round(
//                                 accuracy
//                             )}m). Lokasi mungkin tidak tepat. Coba lagi di area terbuka.`
//                         );
//                         // Anda bisa memilih untuk tidak update Livewire jika akurasi terlalu buruk
//                         // atau tetap update tapi dengan peringatan seperti di atas.
//                         // Untuk saat ini, kita tetap update Livewire.
//                     }

//                     // Update Livewire component state
//                     this.$wire.set("latitude", lat);
//                     this.$wire.set("longitude", lng);
//                     this.$wire.set("titik_koordinat", { lat, lng }); // Update field MapPicker

//                     // Dispatch event untuk memberitahu komponen peta untuk update
//                     window.dispatchEvent(
//                         new CustomEvent("location-updated", {
//                             detail: { lat, lng, accuracy },
//                         })
//                     );
//                 } catch (error) {
//                     this.showError(this.getErrorMessage(error));
//                 } finally {
//                     this.setButtonLoading(button, false); // Matikan state loading tombol
//                 }
//             },

//             // Mengatur state loading pada tombol
//             setButtonLoading(button, isLoading) {
//                 if (!button) return; // Penting: cek apakah button ditemukan

//                 button.disabled = isLoading;
//                 // Asumsi konten tombol adalah SVG + teks.
//                 // Sesuaikan innerHTML ini agar sesuai dengan struktur tombol Filament kamu
//                 // dan tetap mempertahankan ikon aslinya jika tidak loading.
//                 button.innerHTML = isLoading
//                     ? `
//                     <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
//                              xmlns="http://www.w3.org/2000/svg"
//                              fill="none"
//                              viewBox="0 0 24 24">
//                             <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
//                             <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
//                         </svg>
//                         Mengambil lokasi...
//                     `
//                     : `
//                     <span class="flex items-center justify-center gap-1">
//                         <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M11.54 22.351A8.287 8.287 0 0 0 18 10a8.283 8.283 0 0 0-16.566 0c0 2.909.522 5.66 1.455 8.351.916 2.628 2.337 5.045 4.195 7.126ZM10 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" clip-rule="evenodd" /></svg>
//                         Dapatkan Lokasi Saya Sekarang
//                     </span>`;
//             },

//             // Mendapatkan pesan error yang user-friendly dari Geolocation API error
//             getErrorMessage(error) {
//                 switch (error.code) {
//                     case error.PERMISSION_DENIED:
//                         return "Anda menolak permintaan lokasi. Harap izinkan akses lokasi di pengaturan browser Anda.";
//                     case error.POSITION_UNAVAILABLE:
//                         return "Informasi lokasi tidak tersedia. Pastikan GPS/Internet aktif dan sinyal kuat.";
//                     case error.TIMEOUT:
//                         return "Waktu permintaan lokasi habis. Coba lagi di area dengan sinyal lebih baik atau koneksi internet stabil.";
//                     default:
//                         return `Gagal mendapatkan lokasi. Error: ${
//                             error.message || "Tidak dikenal"
//                         }`;
//                 }
//             },

//             // Menampilkan pesan error di container yang telah ditentukan
//             showError(message) {
//                 const errorContainer =
//                     document.getElementById("location-error");
//                 if (errorContainer) {
//                     errorContainer.textContent = message;
//                 } else {
//                     // Fallback: Jika container tidak ditemukan, bisa console.error atau alert
//                     console.error("Geolocation Error:", message);
//                 }
//             },

//             // Fungsi ini mungkin tidak lagi diperlukan jika errorContainer selalu ada dari PHP View::make
//             // Tetapi bisa berguna jika kamu ingin membuat elemen error secara dinamis
//             createErrorContainer() {
//                 const container = document.createElement("div");
//                 container.id = "location-error";
//                 container.className = "text-sm text-red-600 mt-2"; // Tailwind classes

//                 const button = document.querySelector(
//                     "[data-geolocation-button]"
//                 );
//                 if (button) {
//                     // Sisipkan elemen error setelah tombol
//                     button.parentNode.insertBefore(
//                         container,
//                         button.nextSibling
//                     );
//                 }
//                 return container;
//             },
//         }));
//     });
// }

// Versi Original
// export default function initGeolocation() {
//     document.addEventListener("alpine:init", () => {
//         Alpine.data("geolocation", () => ({
//             init() {
//                 // Listen for Livewire event
//                 this.$wire.on("get-live-location", () => {
//                     this.getLiveLocation();
//                 });

//                 // Initialize map marker if location exists
//                 if (this.$wire.get("latitude") && this.$wire.get("longitude")) {
//                     window.dispatchEvent(
//                         new CustomEvent("location-updated", {
//                             detail: {
//                                 lat: this.$wire.get("latitude"),
//                                 lng: this.$wire.get("longitude"),
//                             },
//                         })
//                     );
//                 }
//             },

//             async getLiveLocation() {
//                 const button = document.querySelector(
//                     "[data-geolocation-button]"
//                 );
//                 const errorContainer =
//                     document.getElementById("location-error");

//                 if (!navigator.geolocation) {
//                     this.showError("Browser tidak mendukung geolokasi");
//                     return;
//                 }

//                 // Set loading state
//                 this.setButtonLoading(button, true);

//                 try {
//                     const position = await new Promise((resolve, reject) => {
//                         navigator.geolocation.getCurrentPosition(
//                             resolve,
//                             reject,
//                             {
//                                 enableHighAccuracy: true,
//                                 timeout: 10000,
//                                 maximumAge: 0,
//                             }
//                         );
//                     });

//                     const lat = position.coords.latitude;
//                     const lng = position.coords.longitude;

//                     // Update Livewire component
//                     this.$wire.set("latitude", lat);
//                     this.$wire.set("longitude", lng);
//                     this.$wire.set("location", [lat, lng]);

//                     // Update map
//                     window.dispatchEvent(
//                         new CustomEvent("location-updated", {
//                             detail: { lat, lng },
//                         })
//                     );
//                 } catch (error) {
//                     this.showError(this.getErrorMessage(error));
//                 } finally {
//                     this.setButtonLoading(button, false);
//                 }
//             },

//             setButtonLoading(button, isLoading) {
//                 if (!button) return;

//                 button.disabled = isLoading;
//                 button.innerHTML = isLoading
//                     ? `
//                     <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
//                          xmlns="http://www.w3.org/2000/svg"
//                          fill="none"
//                          viewBox="0 0 24 24">
//                         <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
//                         <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
//                     </svg>
//                     Mengambil lokasi...
//                 `
//                     : "Dapatkan Lokasi Saya Sekarang";
//             },

//             getErrorMessage(error) {
//                 switch (error.code) {
//                     case error.PERMISSION_DENIED:
//                         return "Anda menolak permintaan lokasi. Harap izinkan akses lokasi di pengaturan browser Anda.";
//                     case error.POSITION_UNAVAILABLE:
//                         return "Informasi lokasi tidak tersedia. Pastikan GPS/Internet aktif.";
//                     case error.TIMEOUT:
//                         return "Waktu permintaan habis. Coba lagi di area dengan sinyal lebih baik.";
//                     default:
//                         return (
//                             "Gagal mendapatkan lokasi. Error: " + error.message
//                         );
//                 }
//             },

//             showError(message) {
//                 const errorContainer =
//                     document.getElementById("location-error") ||
//                     this.createErrorContainer();
//                 errorContainer.textContent = message;
//             },

//             createErrorContainer() {
//                 const container = document.createElement("div");
//                 container.id = "location-error";
//                 container.className = "text-sm text-red-600 mt-2";

//                 const button = document.querySelector(
//                     "[data-geolocation-button]"
//                 );
//                 if (button) {
//                     button.parentNode.insertBefore(
//                         container,
//                         button.nextSibling
//                     );
//                 }

//                 return container;
//             },
//         }));
//     });
// }
