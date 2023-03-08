@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.workUnit.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.workUnit.update", [$work_unit->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('global.workUnit.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($work_unit) ? $work_unit->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.workUnit.fields.name_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('serial_number') ? 'has-error' : '' }}">
                <label for="serial_number">{{ trans('global.workUnit.fields.serial_number') }}*</label>
                <input type="number" id="serial_number" name="serial_number" class="form-control" value="{{ old('serial_number', isset($work_unit) ? $work_unit->serial_number : '') }}" required>
                @if($errors->has('serial_number'))
                    <em class="invalid-feedback">
                        {{ $errors->first('serial_number') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.workUnit.fields.serial_number_helper') }}
                </p>
            </div>

            <input
            id="pac-input"
            class="controls"
            type="text"
            placeholder="Search Box"
          />

            <div id="map" style="height: 500px;"></div>

            <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
            {{-- <script
              src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxJpfNfWPonmRTm-TktgyaNEVyQxpBHd0&callback=initMap&v=weekly&channel=2"
              async
            ></script> --}}
        
            <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDJyer8hPQZAOmynZbfVkizngMNZ3Hkkw&callback=initMap&v=weekly&libraries=places"
            defer
          ></script>

          <div class="form-group {{ $errors->has('lat') ? 'has-error' : '' }}">
            <label for="lat">{{ trans('global.workUnit.fields.lat') }}*</label>
            <input type="text" id="lat" name="lat" class="form-control" value="{{ old('lat', isset($work_unit) ? $work_unit->lat : '') }}" required>
            @if($errors->has('lat'))
                <em class="invalid-feedback">
                    {{ $errors->first('lat') }}
                </em>
            @endif
            <p class="helper-block">
                {{ trans('global.workUnit.fields.lat_helper') }}
            </p>
        </div>
        
        <div class="form-group {{ $errors->has('lng') ? 'has-error' : '' }}">
            <label for="lng">{{ trans('global.workUnit.fields.lng') }}*</label>
            <input type="text" id="lng" name="lng" class="form-control" value="{{ old('lng', isset($work_unit) ? $work_unit->lng : '') }}" required>
            @if($errors->has('lng'))
                <em class="invalid-feedback">
                    {{ $errors->first('lng') }}
                </em>
            @endif
            <p class="helper-block">
                {{ trans('global.workUnit.fields.lng_helper') }}
            </p>
        </div>

        <div class="form-group {{ $errors->has('radius') ? 'has-error' : '' }}">
            <label for="radius">{{ trans('global.workUnit.fields.radius') }} (Meter)*</label>
            <input type="number" id="radius" min = "1" name="radius" class="form-control" value="{{ old('radius', isset($work_unit) ? $work_unit->radius : '') }}" required>
            @if($errors->has('radius'))
                <em class="invalid-feedback">
                    {{ $errors->first('radius') }}
                </em>
            @endif
            <p class="helper-block">
                {{ trans('global.workUnit.fields.radius_helper') }}
            </p>
        </div>


            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<!-- jsFiddle will insert css and js -->

<script type="text/javascript">


function initMap() {
    var cityCircle = [];
if (navigator.geolocation) {
navigator.geolocation.getCurrentPosition(showPosition);

} else {
x.innerHTML = "Geolocation is not supported by this browser.";
// const myLatlng = { lat: -8.459556, lng: 115.046600 };

}
function showPosition(position) {
      console.log(position.coords.latitude)
      const myLatlng = { lat: position.coords.latitude, lng: position.coords.longitude };

      const map = new google.maps.Map(document.getElementById("map"), {
zoom: 14,
center: myLatlng,
});

function addCircle(location) {    
  // Add the circle for this city to the map.    
    cityCircle = new google.maps.Circle({    
      strokeColor: '#FF0000',    
      strokeOpacity: 0.8,    
      strokeWeight: 2,    
      fillColor: '#FF0000',    
      fillOpacity: 0.35,    
      map: map,    
      center: location,    
      radius: parseFloat(document.getElementById("radius").value),  
      draggable:false  
    });  
} 

// const input = document.getElementById("pac-input") as HTMLInputElement;
// const searchBox = new google.maps.places.SearchBox(input);

// Create the initial InfoWindow.
// let infoWindow = new google.maps.InfoWindow({
// content: "Posisi anda sekarang",
// position: myLatlng,
// });

// infoWindow.open(map);
// Configure the click listener.
// map.addListener("click", (mapsMouseEvent) => {
// document.getElementById("lat").value = mapsMouseEvent.latLng.toJSON().lat;
// document.getElementById("lng").value = mapsMouseEvent.latLng.toJSON().lng;
// // console.log(mapsMouseEvent.latLng.toJSON().lat);
// // Close the current InfoWindow.
// infoWindow.close();
// // Create a new InfoWindow.
// infoWindow = new google.maps.InfoWindow({
//   position: mapsMouseEvent.latLng,
// });
// infoWindow.setContent(
//   JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
// );
// infoWindow.open(map);
// });



 // Create the search box and link it to the UI element.
 const input = document.getElementById("pac-input");
  const searchBox = new google.maps.places.SearchBox(input);

  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  // Bias the SearchBox results towards current map's viewport.
  map.addListener("bounds_changed", () => {
    searchBox.setBounds(map.getBounds());
  });

  let markers = [];

  // Listen for the event fired when the user selects a prediction and retrieve
  // more details for that place.
  searchBox.addListener("places_changed", () => {
    const places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    // Clear out the old markers.
    markers.forEach((marker) => {
      marker.setMap(null);
    });
    markers = [];

    // marker.setMap(null);

    // For each place, get the icon, name and location.
    const bounds = new google.maps.LatLngBounds();

    places.forEach((place) => {
      if (!place.geometry || !place.geometry.location) {
        console.log("Returned place contains no geometry");
        return;
      }
console.log('sss',place.geometry.location.lat())

// if (marker && marker.setMap) {
//                     marker.setMap(null);
//                 }

// hideMarkers();


// const marker = new google.maps.Marker({
//     position: {lat : place.geometry.location.lat(), lng : place.geometry.location.lng()},
//     map: map,
//     draggable:true,
// });
marker.setPosition({lat : place.geometry.location.lat(), lng : place.geometry.location.lng()});

const d = {lat : place.geometry.location.lat(), lng : place.geometry.location.lng()} 
                if (cityCircle && cityCircle.setMap) {
                    cityCircle.setMap(null);
                }
               
                addCircle(d)

document.getElementById("lat").value = place.geometry.location.lat();
                document.getElementById("lng").value = place.geometry.location.lng();
// marker.setMap(null);

console.log('tess marker', marker)
// marker = [];

      const icon = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25),
      };

      // Create a marker for each place.
    //   markers.push(
    //     new google.maps.Marker({
    //       map,
    //       icon,
    //       title: place.name,
    //       position: place.geometry.location,
    //     })
    //   );
      
      
      if (place.geometry.viewport) {
        // Only geocodes have viewport.
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }
    });
    map.fitBounds(bounds);
  });


const marker = new google.maps.Marker({
  position: myLatlng,
  map: map,
   draggable:true,
});

                if (cityCircle && cityCircle.setMap) {
                    cityCircle.setMap(null);
                }
               
                addCircle(myLatlng)

// marker.setMap(null);

google.maps.event.addListener(marker, 'dragend', function() 
{
    geocodePosition(marker.getPosition());
});

radius.addEventListener("input", function (e) {
     cityCircle.setRadius(parseFloat(document.getElementById("radius").value))
    // alert('tess')
});

function geocodePosition(pos) 
{
   geocoder = new google.maps.Geocoder();
   geocoder.geocode
    ({
        latLng: pos
    }, 
        function(results, status) 
        {
            if (status == google.maps.GeocoderStatus.OK) 
            {
                console.log(results[0].geometry.location.lat())
                document.getElementById("lat").value = results[0].geometry.location.lat();
                document.getElementById("lng").value = results[0].geometry.location.lng();
                
                const d = {lat :results[0].geometry.location.lat(), lng : results[0].geometry.location.lng()} 
                if (cityCircle && cityCircle.setMap) {
                    cityCircle.setMap(null);
                }
               
                addCircle(d)
               
                console.log(cityCircle)
                // $("#mapSearchInput").val(myLatln);
                // $("#mapErrorMsg").hide(100);
            } 
            else 
            {
                $("#mapErrorMsg").html('Cannot determine address at this location.'+status).show(100);
            }
        }
    );
}


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
@endsection