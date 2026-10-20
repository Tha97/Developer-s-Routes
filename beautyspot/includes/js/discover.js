let discover_map, discover_markers = [];
let currentLat = null, currentLng = null;
let discover_searchTimeout;

const BEAUTYSPOT_MARKER_ICON = L.icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
});

function initDiscoverMap() {
    discover_map = L.map('map-canvas').setView([-26.2041, 28.0473], 11); // Johannesburg default

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(discover_map);

    const locInput = document.getElementById('loc');
    const resultsBox = document.createElement('div');
    resultsBox.className = 'address-suggestions';
    locInput.parentNode.style.position = 'relative';
    locInput.parentNode.appendChild(resultsBox);

    locInput.addEventListener('input', () => {
        clearTimeout(discover_searchTimeout);
        const q = locInput.value.trim();
        if (q.length < 3) {
            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';
            return;
        }
        discover_searchTimeout = setTimeout(() => {
            fetch(SITE_URL + '/api/geocode.php?action=search&q=' + encodeURIComponent(q))
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
                        item.addEventListener('click', () => {
                            currentLat = place.lat;
                            currentLng = place.lng;
                            locInput.value = place.display_name;
                            discover_map.setView([place.lat, place.lng], 12);
                            resultsBox.innerHTML = '';
                            resultsBox.style.display = 'none';
                            runSearch();
                        });
                        resultsBox.appendChild(item);
                    });
                    resultsBox.style.display = 'block';
                });
        }, 400);
    });

    document.addEventListener('click', (e) => {
        if (e.target !== locInput) resultsBox.style.display = 'none';
    });

    document.getElementById('search-btn').addEventListener('click', runSearch);

    // Leaflet measures its container once at start-up. If fonts/CSS/images change the layout afterwards,
    // tiles end up in the wrong place (grey map with a stray tile block). Re-measure when things settle.
    setTimeout(() => discover_map.invalidateSize(), 150);
    window.addEventListener('load', () => discover_map.invalidateSize());
    window.addEventListener('resize', () => discover_map.invalidateSize());

    // Run an initial search (unfiltered by location unless one was passed in the URL) once the map is ready.
    runSearch();
}

function clearMarkers() {
    discover_markers.forEach(m => discover_map.removeLayer(m));
    discover_markers = [];
}

function runSearch() {
    const q = document.getElementById('q').value;
    const category = document.getElementById('category').value;
    const radius = document.getElementById('radius').value;
    const params = new URLSearchParams({ q, radius, category });
    if (currentLat !== null && currentLng !== null) {
        params.set('lat', currentLat);
        params.set('lng', currentLng);
    }

    document.getElementById('result-count').textContent = 'Searching...';

    fetch(SITE_URL + '/api/search-stylists.php?' + params.toString())
        .then(r => r.json())
        .then(data => renderResults(data.results));
}

function renderResults(results) {
    clearMarkers();
    const list = document.getElementById('results-list');
    const countEl = document.getElementById('result-count');

    countEl.textContent = results.length === 0
        ? 'No professionals found — try widening your search radius.'
        : `${results.length} professional${results.length === 1 ? '' : 's'} found`;

    list.innerHTML = '';

    if (results.length === 0) {
        list.innerHTML = '<div class="empty-state">No matches yet. Try a different suburb, keyword, or a wider radius.</div>';
        return;
    }

    const boundsPoints = [];

    results.forEach(r => {
        const card = document.createElement('a');
        card.href = r.profile_url;
        card.className = 'stylist-card';
        card.innerHTML = `
            <div class="thumb">
                ${r.cover_shot ? `<img src="${r.cover_shot}" alt="${escapeHtml(r.business_name)}">` : ''}
                ${r.category_labels && r.category_labels.length ? `<span class="category-badge">${escapeHtml(r.category_labels[0])}</span>` : ''}
            </div>
            <div class="card-body">
                <div class="card-top">
                    ${r.avatar_path ? `<img src="${r.avatar_path}" alt="" width="40" height="40" style="border-radius:50%;object-fit:cover;" class="card-avatar">` : ''}
                    <div>
                        <h3>${escapeHtml(r.business_name)}</h3>
                        <div class="location">📍 ${escapeHtml([r.suburb, r.city].filter(Boolean).join(', ') || 'South Africa')}${r.distance_km !== null ? ` · ${r.distance_km} km away` : ''}</div>
                    </div>
                </div>
                ${r.specialties ? `<div class="tags">${escapeHtml(r.specialties)}</div>` : ''}
                <div class="card-footer">
                    ${r.rating ? `<span class="rating">★ ${r.rating} (${r.review_count})</span>` : '<span></span>'}
                    ${r.price_range ? `<span class="price">${escapeHtml(r.price_range)}</span>` : ''}
                </div>
                <div style="margin-top:10px;"><span class="view-link">View profile →</span></div>
            </div>
        `;
        list.appendChild(card);

        if (r.lat && r.lng) {
            const marker = L.marker([r.lat, r.lng], { icon: BEAUTYSPOT_MARKER_ICON }).addTo(discover_map);
            marker.bindTooltip(r.business_name);
            marker.on('click', () => { window.location.href = r.profile_url; });
            discover_markers.push(marker);
            boundsPoints.push([r.lat, r.lng]);
        }
    });

    discover_map.invalidateSize();
    if (boundsPoints.length > 0) {
        discover_map.fitBounds(boundsPoints, { padding: [30, 30], maxZoom: 14 });
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
}

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('q').value = INITIAL.q || '';
    document.getElementById('loc').value = INITIAL.loc || '';
    if (INITIAL.radius) document.getElementById('radius').value = INITIAL.radius;
    if (INITIAL.category) document.getElementById('category').value = INITIAL.category;

    initDiscoverMap();
});
