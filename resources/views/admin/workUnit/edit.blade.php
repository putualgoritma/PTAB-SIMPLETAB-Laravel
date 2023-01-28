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


            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection