@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.shift_group.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.shift_group.scheduleUpdate", [$schedule->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('global.shift_group.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($schedule) ? $schedule->title : '') }}" required>
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_group.fields.title_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('time') ? 'has-error' : '' }}">
                <label for="time">{{ trans('global.shift_group.fields.time') }}*</label>
                <input type="time" id="time" name="time" class="form-control" value="{{ old('time', isset($schedule) ? $schedule->time : '') }}" required>
                @if($errors->has('time'))
                    <em class="invalid-feedback">
                        {{ $errors->first('time') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_group.fields.time_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('start') ? 'has-error' : '' }}">
                <label for="start">{{ trans('global.shift_group.fields.start') }}*</label>
                <input type="time" id="start" name="start" class="form-control" value="{{ old('start', isset($schedule) ? $schedule->start : '') }}" required>
                @if($errors->has('start'))
                    <em class="invalid-feedback">
                        {{ $errors->first('start') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_group.fields.start_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('end') ? 'has-error' : '' }}">
                <label for="end">{{ trans('global.shift_group.fields.end') }}*</label>
                <input type="time" id="end" name="end" class="form-control" value="{{ old('end', isset($schedule) ? $schedule->end : '') }}" required>
                @if($errors->has('end'))
                    <em class="invalid-feedback">
                        {{ $errors->first('end') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_group.fields.end_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('duration') ? 'has-error' : '' }}">
                <label for="duration">{{ trans('global.shift_group.fields.duration') }}*</label>
                <input type="number" id="duration" name="duration" class="form-control" value="{{ old('duration', isset($schedule) ? $schedule->duration : '') }}" required>
                @if($errors->has('duration'))
                    <em class="invalid-feedback">
                        {{ $errors->first('duration') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_group.fields.duration_helper') }}
                </p>
            </div>

           

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection