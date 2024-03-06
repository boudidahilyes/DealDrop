document.addEventListener("DOMContentLoaded", function () {
  var coords = [{% for delivery in deliveries %}[{{ delivery.coordinates }}], {% endfor %}];
var map = L.map('map').setView([{{ deliveries| first.coordinates }}], 14);
L.tileLayer('https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map);
var markerGroup = L.featureGroup().addTo(map);



var apiKey = '5b3ce3597851110001cf6248c7ddd0a26f424500a914813c8440fef9';
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function (position) {
    let currentLatitude = position.coords.latitude;
    let currentLongitude = position.coords.longitude;
    let currentPosition = [currentLatitude, currentLongitude];
    var pointDistance = [];
    coords.forEach((coord) => {
      let distance = Math.sqrt((currentPosition[0] - coord[0]) * (currentPosition[0] - coord[0]) + (currentPosition[1] - coord[1]) * (currentPosition[1] - coord[1]));
      pointDistance.push([coord, distance]);
    })

    pointDistance.sort((a, b) => {
      return a[1] - b[1]
    });
    const colors = [
      { name: 'Red', hex: '#FF5733' },
      { name: 'Blue', hex: '#3384FF' },
      { name: 'Green', hex: '#33FF57' },
      { name: 'Yellow', hex: '#FFD033' },
      { name: 'Orange', hex: '#FF9333' },
      { name: 'Purple', hex: '#7D33FF' },
      { name: 'Cyan', hex: '#33FFF2' },
      { name: 'Magenta', hex: '#FF33A5' },
      { name: 'Teal', hex: '#33FFD9' },
      { name: 'Indigo', hex: '#3343FF' },
      { name: 'Lime', hex: '#8EFF33' },
      { name: 'Pink', hex: '#FF33D5' },
      { name: 'Amber', hex: '#FFC133' },
      { name: 'Brown', hex: '#8B4513' },
      { name: 'Maroon', hex: '#800000' },
      { name: 'Navy', hex: '#000080' },
      { name: 'Olive', hex: '#808000' },
      { name: 'Peach', hex: '#FFDAB9' },
      { name: 'Slate', hex: '#708090' },
      { name: 'Violet', hex: '#9400D3' }
    ];
    var a = 0;
    console.log(pointDistance);
    pointDistance.forEach((p) => {
      var routingUrl = 'https://api.openrouteservice.org/v2/directions/driving-car?api_key=' + apiKey + '&start=' + currentPosition[1] + ',' + currentPosition[0] + '&end=' + p[0][1] + ',' + p[0][0];

      // Make the routing request
      axios.get(routingUrl).then(function (response) {
        var routeCoordinates = response.data.features[0].geometry.coordinates;
        for (let i = 0; i < routeCoordinates.length; i++) {
          let aux = routeCoordinates[i][0];
          routeCoordinates[i][0] = routeCoordinates[i][1];
          routeCoordinates[i][1] = aux;
        }
        // Create a polyline using the route coordinates

        var customIcon = L.divIcon({
          html: '<i class="fa-solid fa-location-dot" style="color: ' + colors[a].hex + ';font-size: 32px"></i>',
          iconSize: [30, 32],
          iconAnchor: [15, 32]
        });
        L.marker(p[0], { 'icon': customIcon }).addTo(map);
        var polyline = L.polyline(routeCoordinates, { color: colors[a].hex }).addTo(map);
        console.log(routingUrl);

        a++;
        // Fit the map view to the polyline bounds
        map.fitBounds(polyline.getBounds());
        lastCoords = routeCoordinates;
      }).catch(function (error) {
        console.error('Error getting route:', error);
      });

      currentPosition = p[0];
    })

  }, function (error) {
    console.error('Error getting location:', error.message);
  });
} else {
  console.error('Geolocation is not supported by this browser.');
}
  })