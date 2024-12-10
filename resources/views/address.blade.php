<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        #map {
            width: 100%;
            height: 500px;
            margin-bottom: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .msg {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .msg.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .msg.danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDdwlGhZKKQqYyw9f9iME40MzMgC9RL4ko&libraries=places,geometry"></script>
</head>
<body>
    <h1>Select Address</h1>
    <div id="map"></div>
    <div class="form-container">
        <!-- Display Messages -->
        @if(session('success'))
            <div class="msg success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="msg danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Display Validation Errors -->
        @if($errors->any())
            <div class="msg danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('address.save') }}">
            @csrf
            <input type="hidden" name="request_id" value="<?= isset($_GET['data']) ? $_GET['data'] : "" ?>">
            <div class="form-group">
                <label for="search">Search Address:</label>
                <input type="text" id="search" placeholder="Search for an address">
            </div>
            <div class="form-group">
                <label for="address">Selected Address:</label>
                <input type="text" id="address" name="address" readonly>
            </div>
            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="number" id="latitude" name="latitude" step="any" readonly>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="number" id="longitude" name="longitude" step="any" readonly>
            </div>
            <div class="form-group d-none">
                <label for="distance">Distance (meters):</label>
                <input type="number" id="distance" name="distance" step="any" readonly>
            </div>
            <button type="submit">Save Address</button>
        </form>
    </div>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            const initialLocation = { lat: 25.276987, lng: 55.296249 }; // Default location
            map = new google.maps.Map(document.getElementById('map'), {
                center: initialLocation,
                zoom: 12,
            });

            marker = new google.maps.Marker({
                position: initialLocation,
                map,
                draggable: true,
            });

            const geocoder = new google.maps.Geocoder();
            const searchInput = document.getElementById('search');

            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);

            // When a place is selected from the autocomplete
            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    alert("No details available for input: '" + place.name + "'");
                    return;
                }

                map.setCenter(place.geometry.location);
                map.setZoom(15);
                marker.setPosition(place.geometry.location);

                updateFormFields(place.geometry.location, place.formatted_address);
            });

            // When the marker is dragged to a new location
            google.maps.event.addListener(marker, 'dragend', function () {
                const position = marker.getPosition();
                geocoder.geocode({ location: position }, function (results, status) {
                    if (status === 'OK' && results[0]) {
                        updateFormFields(position, results[0].formatted_address);
                    }
                });
            });

            updateFormFields(initialLocation, "Default Location");
        }

        function updateFormFields(location, address) {
            document.getElementById('latitude').value = location.lat();
            document.getElementById('longitude').value = location.lng();
            document.getElementById('address').value = address;

            // Calculate distance from a fixed point (Optional)
            const fixedPoint = new google.maps.LatLng(25.276987, 55.296249); // Replace with your reference location
            const selectedPoint = new google.maps.LatLng(location.lat(), location.lng());
            const distance = google.maps.geometry.spherical.computeDistanceBetween(fixedPoint, selectedPoint);
            document.getElementById('distance').value = distance.toFixed(2);
        }

        window.onload = initMap;
    </script>
</body>
</html>
