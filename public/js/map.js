var map = L.map('mapid').setView([-33.867950, 151.209496], 11);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 18,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: 'mapbox.streets'
}).addTo(map);


var template;
var client = new XMLHttpRequest();

client.onload = function() {
    template = this.responseText;
    Mustache.parse(template);   // optional, speeds up future uses

    function generatePopupContent(feature, layer) {
        direction_url = "https://maps.google.fr/maps?f=q";
        direction_url += '&q=' + feature.geometry.coordinates[1];
        direction_url += ',' + feature.geometry.coordinates[0];
        var popupContent = Mustache.render(
            template,
            {
                name: feature.properties.name,
                direction_url: direction_url,
                label: feature.properties.label,
                categories: feature.properties.categories
            }
        );
        layer.bindPopup(popupContent);
    }

    /**
     * Callback function to mark the point on the map
     */
    function markPoints() {
        var coorsField = JSON.parse(this.responseText);
        var coorsLayer = L.geoJSON(coorsField, {
            onEachFeature: generatePopupContent
        }).addTo(map);
    }

    // Fetch all points from API and mark them on the map
    client.onload = markPoints;
    client.open("GET", js_court_list_url);
    client.send();
}
client.open("GET", "mustache/marker_popup.mst");
client.send();
