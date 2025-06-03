import initGeolocation from "../filament/geolocation";

export default function () {
    // Initialize only if we're on the berita acara form page
    if (document.querySelector("[data-geolocation-button]")) {
        initGeolocation();
    }
}
