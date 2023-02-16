@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.geolocation_off.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.geolocation_off.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('staff_id') ? 'has-error' : '' }}">
                <label for="staff_id">staff*</label>
                <select id="staff_id" name="staff_id" class="form-control" value="{{ old('staff_id', isset($shift_staff) ? $shift_staff->staff_id : '') }}">
                    <option value="">--Pilih staff--</option>
                    @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
           
                </select>
                @if($errors->has('staff_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('staff_id') }}
                    </em>
                @endif
            </div>

            <div class="form-group {{ $errors->has('start') ? 'has-error' : '' }}">
                <label for="start">{{ trans('global.geolocation_off.fields.start') }}*</label>
                <input type="date" id="start" name="start" class="form-control" value="{{ old('start', isset($geolocation_off) ? $geolocation_off->start : '') }}" required>
                @if($errors->has('start'))
                    <em class="invalid-feedback">
                        {{ $errors->first('start') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.geolocation_off.fields.start_helper') }}
                </p>
            </div>
            
            <div class="form-group {{ $errors->has('end') ? 'has-error' : '' }}">
                <label for="end">{{ trans('global.geolocation_off.fields.end') }}*</label>
                <input type="date" id="end" name="end" class="form-control" value="{{ old('end', isset($geolocation_off) ? $geolocation_off->end : '') }}" required>
                @if($errors->has('end'))
                    <em class="invalid-feedback">
                        {{ $errors->first('end') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.geolocation_off.fields.end_helper') }}
                </p>
            </div>
    
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('global.action.fields.description') }}*</label>
                <textArea id="description" name="description" class="form-control" required>{{ old('name', isset($geolocation_off) ? $geolocation_off->description : '') }}</textArea>
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
            </div>


            <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                <label for="file">{{ trans('global.geolocation_off.fields.file') }}*</label>
                <input type="file" id="file" name="image" class="form-control" value="{{ old('file', isset($geolocation_off) ? $geolocation_off->file : '') }}" required>
                @if($errors->has('file'))
                    <em class="invalid-feedback">
                        {{ $errors->first('file') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.geolocation_off.fields.file_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection