<!DOCTYPE html>
<html lang="en">
<head>
    <title>Interactive Map</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0" />

    <!-- leaflet files -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        #map {
            height: 850px;
            width: 100%;
        }
    </style>
</head>
<body>
<?php include 'newheader.php'; ?>

    <div id="map"></div>


    <script>
           var map = L.map('map', {
            center: [10.291464574605252, 123.89912966097384],
            zoom: 20,
            zoomControl: false, // Disable the zoom control
            dragging: false, // Disable map dragging
            doubleClickZoom: false, // Disable double-click zoom
            scrollWheelZoom: false // Disable scroll wheel zoom
        });

        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:
            "&copy; <a href='https://openstreetmap.org/copyright'> Openstreet map</a> contributors", 
        }); 

        osm.addTo(map);

        var marker = L.marker([10.291215909470925, 123.89940193806856]).addTo(map);

        // var imageUrl = 'https://via.placeholder.com/150'; // Replace this with your image URL

        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        // marker.bindPopup('<img src="images/map/carbon sign.png" alt="" width="200">').openPopup();
        marker.on('click', function() {
            var newPopup = L.popup().setLatLng([10.291215909470925, 123.89940193806856])
                                    .setContent('<img src="images/map/carbon sign.png" alt="" width="200">');
            newPopup.openOn(map);
        });
            
        //  var map;

        //  document.addEventListener("DOMContentLoaded", function() {
        //     initMap();
        // });

        // function initMap() {
        //     var map = L.map('map').setView([10.291464574605252, 123.89912966097384], 19); // Initial coordinates and zoom level
        //     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //         maxZoom: 20,
        //     }).addTo(map);
        //     addStaticPictureMarker();
        // }

        // function addStaticPictureMarker() {
        //     var imageUrl = 'https://via.placeholder.com/150'; // Replace this with your image URL

        //     var customIcon = L.icon({
        //         iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        //         iconSize: [25, 41],
        //         iconAnchor: [12, 41],
        //         popupAnchor: [1, -34],
        //     });

        //     var marker = L.marker([40.7128, -74.0060], { icon: customIcon }).addTo(map); // Marker placed at New York City
        //     marker.bindPopup('<img src="' + imageUrl + '" alt="Picture" width="150">').openPopup();
        // }
    </script>
<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
