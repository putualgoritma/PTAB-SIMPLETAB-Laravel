@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('global.workUnit.title') }}
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>

                {{-- get lnglat --}}
                <input type="hidden" id="lat" value="{{ $work_unit->lat }}">
                <input type="hidden" id="lng" value="{{ $work_unit->lng }}">

                <tr>
                    <th>
                        {{ trans('global.workUnit.fields.code') }}
                    </th>
                    <td>
                        {{ $work_unit->code }}
                    </td>
                </tr>

                <tr>
                    <th>
                        {{ trans('global.workUnit.fields.name') }}
                    </th>
                    <td>
                        {{ $work_unit->name }}
                    </td>
                </tr>

                <tr>
                    <th>
                        {{ trans('global.workUnit.fields.serial_number') }}
                    </th>
                    <td>
                        {{ $work_unit->serial_number }}
                    </td>
                </tr>
                
                <tr>
                    <th>
                        {{ trans('global.workUnit.fields.location') }}
                    </th>
                    <td>
                        <div id="map" style="height: 500px;"></div>
                    </td>
              
            </tr>
                <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
                {{-- <script
                  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxJpfNfWPonmRTm-TktgyaNEVyQxpBHd0&callback=initMap&v=weekly&channel=2"
                  async
                ></script> --}}
            
                <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDJyer8hPQZAOmynZbfVkizngMNZ3Hkkw&callback=initMap&v=weekly"
                defer
              ></script>

              <tr>
                <th>
                    {{ trans('global.workUnit.fields.radius') }}
                </th>
                <td>
                   {{$work_unit->radius}} Meter
                </td>
          
        </tr>

            </tbody>
        </table>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<!-- jsFiddle will insert css and js -->

<script type="text/javascript">

function initMap() {
    // if(document.getElementById("lat").value != ""){
    const myLatLng = { lat: parseFloat(document.getElementById("lat").value) , lng: parseFloat(document.getElementById("lng").value) };
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 14,
      center: myLatLng,
    });
  
    new google.maps.Marker({
      position: myLatLng,
      map,
      title: "Hello World!",
    });
    console.log(parseFloat(document.getElementById("lng").value))
  }

  
  window.initMap = initMap;
// }



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


