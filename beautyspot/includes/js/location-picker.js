// Powers the address search + draggable pin on the onboarding and edit-profile pages.
// Uses Leaflet + OpenStreetMap (free, no API key) instead of Google Maps.

const BEAUTYSPOT_MARKER_ICON = L.icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

let picker_map, picker_marker, picker_searchTimeout;

function initLocationPicker() {
    const latField = document.getElementById('lat');
    const lngField = document.getElementById('lng');
    const hasExisting = latField.value && lngField.value;

    const startPos = hasExisting
        ? [parseFloat(latField.value), parseFloat(lngField.value)]
        : [-26.2041, 28.0473]; // default: Johannesburg

    picker_map = L.map('map-canvas').setView(startPos, hasExisting ? 14 : 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(picker_map);

    picker_marker = L.marker(startPos, { draggable: true, icon: BEAUTYSPOT_MARKER_ICON }).addTo(picker_map);

    picker_marker.on('dragend', () => {
        const pos = picker_marker.getLatLng();
        setLatLng(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
    });

    picker_map.on('click', (e) => {
        picker_marker.setLatLng(e.latlng);
        setLatLng(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    const input = document.getElementById('address_search');
    const resultsBox = document.createElement('div');
    resultsBox.className = 'address-suggestions';
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(resultsBox);

    input.addEventListener('input', () => {
        clearTimeout(picker_searchTimeout);
        const q = input.value.trim();
        if (q.length < 3) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
            return;
        }
        picker_searchTimeout = setTimeout(() => searchAddress(q, resultsBox), 400);
    });

    document.addEventListener('click', (e) => {
        if (e.target !== input) resultsBox.style.display = 'none';
    });
}

function searchAddress(query, resultsBox) {
    fetch(`${SITE_URL}/api/geocode.php?action=search&q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(data => {
            resultsBox.innerHTML = '';
            if (!data.results || data.results.length === 0) {
                resultsBox.style.display = 'none';
                return;
            }
            data.results.forEach(place => {
                const item = document.createElement('div');
                item.className = 'address-suggestion-item';
                item.textContent = place.display_name;
                item.addEventListener('click', () => selectPlace(place, resultsBox));
                resultsBox.appendChild(item);
            });
            resultsBox.style.display = 'block';
        });
}

function selectPlace(place, resultsBox) {
    picker_map.setView([place.lat, place.lng], 15);
    picker_marker.setLatLng([place.lat, place.lng]);
    setLatLng(place.lat, place.lng);

    document.getElementById('address_search').value = place.display_name;
    document.getElementById('address').value = place.display_name;
    document.getElementById('suburb').value = place.suburb || '';
    document.getElementById('city').value = place.city || '';

    resultsBox.innerHTML = '';
    resultsBox.style.display = 'none';
}

function setLatLng(lat, lng) {
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
}

function reverseGeocode(lat, lng) {
    fetch(`${SITE_URL}/api/geocode.php?action=reverse&lat=${lat}&lng=${lng}`)
        .then(r => r.json())
        .then(data => {
            if (data.display_name) {
                document.getElementById('address').value = data.display_name;
                if (data.suburb) document.getElementById('suburb').value = data.suburb;
                if (data.city) document.getElementById('city').value = data.city;
            }
        });
}

window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('map-canvas')) {
        initLocationPicker();
    }
});
