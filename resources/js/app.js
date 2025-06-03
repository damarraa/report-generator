import "./bootstrap";
if (window.location.pathname.includes("/berita-acara")) {
    import("./pages/berita-acara-form").then((module) => module.default());
}

// import './filament-geolocation';
// Hanya jalankan script geolocation di halaman yang membutuhkan