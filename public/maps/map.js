var currentLatitude;
var currentLongitude;
var map;
var markerGroup;
let icon = L.icon({
  iconUrl: "/maps/images/location.png", // URL to the icon image
  iconSize: [20, 30], // size of the icon
  iconAnchor: [10, 30], // point of the icon which will correspond to marker's location
  popupAnchor: [0, 0], // point from which the popup should open relative to the iconAnchor
});
var points = [];

function getCurrentLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        currentLatitude = position.coords.latitude;
        currentLongitude = position.coords.longitude;
        map = L.map("map").setView([currentLatitude, currentLongitude], 11);
        L.tileLayer(
          "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
          { attribution: "© OpenStreetMap contributors" }
        ).addTo(map);
        var markerGroup = L.featureGroup().addTo(map);
        var areaGroup = L.featureGroup().addTo(map);
        map.on("click", function (event) {
          let point = [event.latlng.lat, event.latlng.lng];
          points.push(point);
          areaGroup.clearLayers();
          markerGroup.clearLayers();
          if (points.length >= 3)
            points = turf.convex(turf.points(points)).geometry.coordinates[0];
          var area = L.polygon(points);

          points.forEach((p) => {
            let ancher = L.marker(p, { icon: icon });
            ancher.on("click", function () {
              let pos = [this.getLatLng().lat, this.getLatLng().lng];
              
              let index = points.findIndex(point => JSON.stringify(point) === JSON.stringify(pos));
              points.splice(index, 1);
              markerGroup.removeLayer(this);
              area.setLatLngs(points);
              areaGroup.clearLayers();
              area.addTo(areaGroup);
            });
            ancher.addTo(markerGroup);
          });
          area.addTo(areaGroup);
          $('#area').val(points);
        });
      },
      function (error) {
        console.error("Error getting location:", error.message);
      }
    );
  } else {
    console.error("Geolocation is not supported by this browser.");
  }
}
getCurrentLocation();
