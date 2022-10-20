@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} Data Device
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>

                <tr>
                    <th>
                        {{ trans('global.WaTemplate.fields.code') }}
                    </th>
                    <td>
                        {{ $deviceWa->serial }}
                    </td>
                </tr>

                <tr>
                    <th>
                        {{ trans('global.WaTemplate.fields.name') }}
                    </th>
                    <td>
                        {{ $deviceWa->name }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Nomor.Telp
                    </th>
                    <td>
                        +{{ $deviceWa->sender }} <br>
                        <a class="btn btn-xs btn-warning" href="{{ route('admin.devicewa.create') }}">ganti nomor</a>
                    </td>
                </tr>

                <tr>
                    <th>
                        Kuota
                    </th>
                    <td>
                        {{ $deviceWa->quota }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Expired Date
                    </th>
                    <td>
                        {{ $deviceWa->expired_date }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Active
                    </th>
                    <td>
                        {{ $deviceWa->active }}
                    </td>
                </tr>
                <tr>
                    <th>
                        Status
                    </th>
                    <td>
                        {{ $deviceWa->status }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Scan Untuk Mengaktifkan
                    </th>
                    <td>
                        <a class="btn btn-primary" href="{{ $scan }}" target="_blank">Scan Disini</a>
                    </td>
                </tr>
                <tr>
                    <th>
                        Disconect device
                    </th>
                    <td>
                        <a class="btn btn-danger" href="{{ route('admin.devicewa.disconect') }}" target="_blank">Disconect Disini</a>
                    </td>
                </tr>


            </tbody>
        </table>
    </div>
</div>

@endsection