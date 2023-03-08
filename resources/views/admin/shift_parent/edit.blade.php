@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.shift_parent.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.shift_parent.update", [$shift_parent->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('global.shift_parent.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($shift_parent) ? $shift_parent->title : '') }}" required>
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.shift_parent.fields.title_helper') }}
                </p>
            </div>

            {{-- <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                <label for="type">{{ trans('global.staff.fields.dapertement') }}*</label>
                <select id="type" name="type" class="form-control" value="{{ old('type', isset($user) ? $user->type : '') }}">
                    <option value="">--Pilih Dapertement--</option>
                    <option value="reguler"  {{"reguler" == $shift_parent->type ? 'selected' : ''}}>reguler</option>
                    <option value="shift"  {{"shift" == $shift_parent->type ? 'selected' : ''}}>shift</option>     
                </select>
                @if($errors->has('type'))
                    <em class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </em>
                @endif
            </div> --}}

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection