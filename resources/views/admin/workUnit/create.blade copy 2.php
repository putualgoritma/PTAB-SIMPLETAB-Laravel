@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.workUnit.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.workUnit.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                <label for="code">{{ trans('global.workUnit.fields.code') }}*</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($code) ? $code : '') }}" required>
                @if($errors->has('code'))
                    <em class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.workUnit.fields.code_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('global.workUnit.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($workUnit) ? $workUnit->name : '') }}" required>
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
                <input type="number" id="serial_number" name="serial_number" class="form-control" value="{{ old('serial_number', isset($serial_number) ? $serial_number : '') }}" required>
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
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDJyer8hPQZAOmynZbfVkizngMNZ3Hkkw&callback=initMap&v=weekly"
            defer
          ></script>

          <div class="form-group {{ $errors->has('lat') ? 'has-error' : '' }}">
            <label for="lat">{{ trans('global.workUnit.fields.lat') }}*</label>
            <input type="text" id="lat" name="lat" class="form-control" value="{{ old('lat', isset($workUnit) ? $workUnit->lat : '') }}" required>
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
            <input type="text" id="lng" name="lng" class="form-control" value="{{ old('lng', isset($workUnit) ? $workUnit->lng : '') }}" required>
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
            <input type="number" id="radius" min = "1" name="radius" class="form-control" value="{{ old('radius', isset($workUnit) ? $workUnit->radius : '') }}" required>
            @if($errors->has('radius'))
                <em class="invalid-feedback">
                    {{ $errors->first('radius') }}
                </em>
            @endif
            <p class="helper-block">
                {{ trans('global.workUnit.fields.radius_helper') }}
            </p>
        </div>
        {{-- <input type="text" name="lat" id="lat">
        
        <input type="text" name="lng" id="lng"> --}}
    

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

const marker = new google.maps.Marker({
  position: myLatlng,
  map: map,
   draggable:true,
});

google.maps.event.addListener(marker, 'dragend', function() 
{
    geocodePosition(marker.getPosition());
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