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
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291215909470925, 123.89940193806856])
                                    .setContent('<img src="images/map/carbon sign.png" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291237021510245, 123.89944219021179]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291237021510245, 123.89944219021179])
                                    .setContent('<img src="images/map/entrance.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291332059976034, 123.89950387731199]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291332059976034, 123.89950387731199])
                                    .setContent('<img src="images/map/cr.png" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291466651894634, 123.89942072883565]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291466651894634, 123.89942072883565])
                                    .setContent('<img src="images/map/24 rice.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291474569064535, 123.89936172023958]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291474569064535, 123.89936172023958])
                                    .setContent('<img src="images/map/22 drygoods.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291108399750037, 123.89960848344734]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291108399750037, 123.89960848344734])
                                    .setContent('<img src="images/map/fruits.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291612475349783, 123.89950116816983]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291612475349783, 123.89950116816983])
                                    .setContent('<img src="images/map/side.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });
            
        //another marker
        var marker = L.marker([10.291031882617716, 123.89893790429807]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291031882617716, 123.89893790429807])
                                    .setContent('<img src="images/map/center.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291414546128543, 123.89887353129085]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291414546128543, 123.89887353129085])
                                    .setContent('<img src="images/map/stairs.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291237729253266, 123.89898081964739]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291237729253266, 123.89898081964739])
                                    .setContent('<img src="images/map/13 flowers.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.29152802556346, 123.89892985767804]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.29152802556346, 123.89892985767804])
                                    .setContent('<img src="images/map/12 fruits.jpg" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.291124249713949, 123.89912834113761]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.291124249713949, 123.89912834113761])
                                    .setContent('<img src="images/map/pasulod.png" alt="" width="400">');
            newPopup.openOn(map);
        });

        //another marker
        var marker = L.marker([10.29101077013382, 123.89854630180344]).addTo(map);
        var customIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [19, 30],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        marker.setIcon(customIcon);
        marker.on('click', function() {
            var newPopup = L.popup({ maxWidth: 400 }).setLatLng([10.29101077013382, 123.89854630180344])
                                    .setContent('<img src="images/map/back.png" alt="" width="400">');
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
