<!DOCTYPE html>
<html lang="en">
<head>
    <title>Interactive Map</title>
    <link rel="icon" type="image/x-icon" href="images/title.ico">
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
            zoomControl: false,
            dragging: false,
            doubleClickZoom: false,
            scrollWheelZoom: false
        });

        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; <a href='https://openstreetmap.org/copyright'> Openstreet map</a> contributors",
        }); 

        osm.addTo(map);

        // Define a function to create markers with descriptions
        function createMarker(lat, lng, iconUrl, description, imageUrl) {
            var marker = L.marker([lat, lng]).addTo(map);
            
            var customIcon = L.icon({
                iconUrl: iconUrl,
                iconSize: [19, 30],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
            });

            marker.setIcon(customIcon);

            // Add a popup with the description and an image if available
            var popupContent = '<div>';
            popupContent += '<p>' + description + '</p>';
            if (imageUrl) {
                popupContent += '<img src="' + imageUrl + '" alt="" width="315" >';
            }
            popupContent += '</div>';

            marker.bindPopup(popupContent);
        }

        // Call the function to create markers with descriptions
        createMarker(10.291215909470925, 123.89940193806856, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Carbon Sign', 'images/map/carbon sign.png');
        createMarker(10.291237021510245, 123.89944219021179, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Entrance', 'images/map/entrance.jpg');
        createMarker(10.291332059976034, 123.89950387731199, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'CR', 'images/map/cr.png');
        createMarker(10.291466651894634, 123.89942072883565, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Rice', 'images/map/24 rice.jpg');
        createMarker(10.291474569064535, 123.89936172023958, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Dry Goods', 'images/map/22 drygoods.jpg');
        createMarker(10.291108399750037, 123.89960848344734, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Bagsakan Area', 'images/map/fruits.jpg');
        createMarker(10.291612475349783, 123.89950116816983, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Side', 'images/map/side.jpg');
        createMarker(10.291031882617716, 123.89893790429807, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Center', 'images/map/center.jpg');
        createMarker(10.291414546128543, 123.89887353129085, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Stairs', 'images/map/stairs.jpg');
        createMarker(10.291237729253266, 123.89898081964739, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Flowers', 'images/map/13 flowers.jpg');
        createMarker(10.29152802556346, 123.89892985767804, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Flowers', 'images/map/12 fruits.jpg');
        createMarker(10.291124249713949, 123.89912834113761, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Pasulod', 'images/map/pasulod.png');
        createMarker(10.29101077013382, 123.89854630180344, 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', 'Back', 'images/map/back.png');
    </script>
<?php include 'newfooter.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
