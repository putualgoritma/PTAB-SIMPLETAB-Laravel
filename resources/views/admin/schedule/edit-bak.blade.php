@extends('layouts.admin')
@section('content')
{{-- @can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.schedule.create") }}">
                {{ trans('global.add') }} {{ trans('global.schedule.title_singular') }}
            </a>
        </div>
    </div>
@endcan --}}
<div class="card">
    <div class="card-header">
        Statistik
    </div>

    <div class="card-body row">
     
        <div class = "col-md-6">
	<h2>Statistic Absen Karyawan (Seluruh)</h2>
 
 
 
	<div style="width: 500px;height: 500px">
		<canvas id="AllAttendance"></canvas>
	</div>
</div>

<div class = "col-md-6">
	<h2>Statistic Absen Karyawan (Perunit)</h2>
    <div style="width: 500px;height: 500px">
		<canvas id="UnitAttendance"></canvas>
	</div>
</div>

    </div>
</div>
@section('scripts')
@parent
<script>
    var ctx = document.getElementById("AllAttendance").getContext('2d');
    var AllAttendance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["mei", "juni", "agustus"],
            datasets: <?php echo $test1 ?> 
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function (value) {
      return value.toLocaleString('de-DE', {style:'percent'});
    },
                    }
                }]
            }
        }
    });


    var ctx1 = document.getElementById("UnitAttendance").getContext('2d');
    var UnitAttendance = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ["mei", "juni", "agustus"],
            datasets: <?php echo $test ?> 
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function (value) {
      return value.toLocaleString('de-DE', {style:'percent'});
    },
                    }
                }]
            }
        }
    });
</script>
@endsection
@endsection