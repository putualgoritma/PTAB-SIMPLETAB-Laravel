<!DOCTYPE html>
<html>
  <head>
    <title>Event Click LatLng</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <!-- jsFiddle will insert css and js -->

    <script type="text/javascript">

function initMap() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  
  } else {
    x.innerHTML = "Geolocation is not supported by this browser.";
    const myLatlng = { lat: -8.459556, lng: 115.046600 };

  }
  function showPosition(position) {
          console.log(position.coords.latitude)
          const myLatlng = { lat: position.coords.latitude, lng: position.coords.longitude };
          document.getElementById("lat").value = position.coords.latitude;
    document.getElementById("lng").value = position.coords.longitude;
          const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 14,
    center: myLatlng,
  });
  // const input = document.getElementById("pac-input") as HTMLInputElement;
  // const searchBox = new google.maps.places.SearchBox(input);

  // Create the initial InfoWindow.
  let infoWindow = new google.maps.InfoWindow({
    content: "Posisi anda sekarang",
    position: myLatlng,
  });

  infoWindow.open(map);
  // Configure the click listener.
  map.addListener("click", (mapsMouseEvent) => {
    document.getElementById("lat").value = mapsMouseEvent.latLng.toJSON().lat;
    document.getElementById("lng").value = mapsMouseEvent.latLng.toJSON().lng;
    // console.log(mapsMouseEvent.latLng.toJSON().lat);
    // Close the current InfoWindow.
    infoWindow.close();
    // Create a new InfoWindow.
    infoWindow = new google.maps.InfoWindow({
      position: mapsMouseEvent.latLng,
    });
    infoWindow.setContent(
      JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
    );
    infoWindow.open(map);
  });
}
  // x.innerHTML = "Latitude: " + position.coords.latitude + 
  // "<br>Longitude: " + position.coords.longitude;
}



    </script>

    <style type="text/css">
        
        /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
  height: 100%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}

    </style>
  </head>
  <body>
    <div id="map" style="height: 500px;"></div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    {{-- <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxJpfNfWPonmRTm-TktgyaNEVyQxpBHd0&callback=initMap&v=weekly&channel=2"
      async
    ></script> --}}

    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDJyer8hPQZAOmynZbfVkizngMNZ3Hkkw&callback=initMap&v=weekly"
    defer
  ></script>

<input type="text" name="lat" id="lat">

<input type="text" name="lng" id="lng">
  </body>
</html>