@extends('layouts.admin')
@section('content')

<div class="card">
  <div class="card-header">
    {{ trans('global.edit') }} Status Tindakan
  </div>

  <div class="card-body">
    <form action="{{route('admin.actions.actionStaffUpdate')}}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <input type="hidden" name='action_id' value='{{$action->id}}'>
      <input type="hidden" name='ticket_id' value='{{$action->ticket_id}}'>
      <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
        <label for="code">{{ trans('global.action.fields.code') }}*</label>
        <input type="text" disabled id="code" name="code" class="form-control" value="{{$action->ticket->code}}">
        @if($errors->has('code'))
        <em class="invalid-feedback">
          {{ $errors->first('code') }}
        </em>
        @endif
      </div>
      <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
        <label for="description">{{ trans('global.action.fields.description') }}*</label>
        <input type="text" disabled id="description" name="description" class="form-control" value="{{$action->description}}">
        @if($errors->has('description'))
        <em class="invalid-feedback">
          {{ $errors->first('description') }}
        </em>
        @endif
      </div>

      <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
        <label for="status">{{ trans('global.action_staff.fields.status') }}*</label>
        <select id="status" name="status" class="form-control" value="{{ old('status', isset($action) ? $action->status : '') }}">
          <option value="">--Pilih status--</option>
          <option value="pending" {{$action->status == 'pending' ? 'selected' :''}}>Pending</option>
          <option value="active" {{$action->status == 'active' ? 'selected' :''}}>Active</option>
          <option value="close" {{$action->status == 'close' ? 'selected' :''}}>Close</option>
        </select>
        @if($errors->has('status'))
        <em class="invalid-feedback">
          {{ $errors->first('status') }}
        </em>
        @endif
      </div>

      <div class="form-group {{ $errors->has('todo') ? 'has-error' : '' }}">
        <label for="todo">{{ trans('global.action_staff.fields.todo') }}*</label>
        <select id="todo" name="todo" class="form-control" value="{{ old('todo', isset($action) ? $action->todo : '') }}">
          <option value="">--Pilih Pekerja--</option>
          <option value="Internal" {{$action->todo == 'Internal' ? 'selected' :''}}>Internal</option>
          <option value="Pihak ke-3" {{$action->todo == 'Pihak ke-3' ? 'selected' :''}}>Pihak ke-3</option>
        </select>
        @if($errors->has('todo'))
        <em class="invalid-feedback">
          {{ $errors->first('todo') }}
        </em>
        @endif
      </div>

      <div class="form-group {{ $errors->has('memo') ? 'has-error' : '' }}">
        <label for="memo">{{ trans('global.action.fields.memo') }}*</label>
        <textArea id="memo" name="memo" class="form-control">{{$action->memo}}</textArea>
        @if($errors->has('memo'))
        <em class="invalid-feedback">
          {{ $errors->first('memo') }}
        </em>
        @endif
      </div>
      <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
        <label for="image">{{ trans('global.ticket.fields.image') }}*</label>
        {{-- <input type="file" id="image" name="image" class="form-control" value="{{ old('image', isset($ticket) ? $ticket->image : '') }}">
        @if($errors->has('image'))
        <em class="invalid-feedback">
          {{ $errors->first('image') }}
        </em>
        @endif --}}
        <div class="input-group control-group increment">
          <input type="file" name="image[]" class="form-control">
          <div class="input-group-btn">
            <button class="btn btn-success btnImage" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
          </div>
        </div>
        <div class="cloneImage hide">
          <div class="control-group input-group" style="margin-top:10px">
            <input type="file" name="image[]" class="form-control">
            <div class="input-group-btn">
              <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
          </div>
        </div>
      </div>


      <div class="form-group {{ $errors->has('image_tools') ? 'has-error' : '' }}">
        <label for="image_tools">{{ trans('global.ticket.fields.image_tools') }}*</label>
        {{-- <input type="file" id="image_tools" name="image_tools" class="form-control" value="{{ old('image_tools', isset($ticket) ? $ticket->image_tools : '') }}">
        @if($errors->has('image_tools'))
        <em class="invalid-feedback">
          {{ $errors->first('image_tools') }}
        </em>
        @endif --}}
        <div class="input-group control-group increment">
          <input type="file" name="image_tools[]" class="form-control">
          <div class="input-group-btn">
            <button class="btn btn-success btnTool" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
          </div>
        </div>
        <div class="cloneTool hide">
          <div class="control-group input-group" style="margin-top:10px">
            <input type="file" name="image_tools[]" class="form-control">
            <div class="input-group-btn">
              <button class="btn btn-danger btnTooldanger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
          </div>
        </div>
      </div>


      {{-- test baru --}}
      {{-- image --}}
      <div class="form-group">
        <button type="button" class="btn btn-primary image" name="add" id="image"><i class="fas fa-plus"> Add image</i></button>
        <label>{{ trans('global.loan.fields.image') }}</label>

        <div id="loan_image" class="p-3 mb-2">
        </div>
      </div>

      <div class="form-group">
        <button type="button" class="btn btn-primary image_tools" name="add" id="image_tools"><i class="fas fa-plus"> Add image_tools</i></button>
        <label>{{ trans('global.loan.fields.image_tools') }}</label>

        <div id="loan_image_tools" class="p-3 mb-2">
        </div>
      </div>

      <div class="form-group">
        <button type="button" class="btn btn-primary image_done" name="add" id="image_done"><i class="fas fa-plus"> Add image_done</i></button>
        <label>{{ trans('global.loan.fields.image_done') }}</label>

        <div id="loan_image_done" class="p-3 mb-2">
        </div>
      </div>

      <div id="loan_image_prework'+i+'">
        <div class="form-group {{ $errors->has('img') ? 'has-error' : '' }}">
          <label for="image_prework_file">{{ trans('global.loan.fields.image_prework_file') }}</label>
          <div class="custom-file"><input id="image_prework_file" name="image_prework[]" type="file" class="custom-file-input" id="customFile" required>
            <label class="custom-file-label" for="customFile">Choose file</label>
          </div>
        </div>
        {{-- <button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_removeimage_prework">
        <i class="fas fa-minus"> hapus</i></button> --}}
      </div>


      {{-- test baru end --}}
      <div>
        <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
      </div>
    </form>
  </div>
</div>

@endsection
@section('scripts')
@parent
<script>
  $(document).ready(function() {
    var i = 1;
    $('#image').click(function() {
      i++;
      $('#loan_image').append(' <div id="loan_image' + i + '" ><div class="form-group {{ $errors->has('
        img ') ? '
        has - error ' : '
        ' }}"><label for="image_file">{{ trans('
        global.loan.fields.image_file ') }}</label><div class="custom-file"><input id="image_file" name="image[]" type="file" class="custom-file-input" id="customFile" required><label class="custom-file-label" for="customFile">Choose file</label></div></div><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_removeimage"><i class="fas fa-minus"> hapus</i></button></div>');
      $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
      });

    });

    $(document).on('click', '.btn_removeimage', function() {
      var button_id = $(this).attr("id");
      $('#loan_image' + button_id + '').remove();
    });

    var a = 1;
    $('#image_tools').click(function() {
      a++;
      $('#loan_image_tools').append(' <div id="loan_image_tools' + a + '" ><div class="form-group {{ $errors->has('
        img ') ? '
        has - error ' : '
        ' }}"><label for="image_tools_file">{{ trans('
        global.loan.fields.image_tools_file ') }}</label><div class="custom-file"><input id="image_tools_file" name="image_tools[]" type="file" class="custom-file-input" id="customFile" required><label class="custom-file-label" for="customFile">Choose file</label></div></div><button type="button" name="remove" id="' + a + '" class="btn btn-danger btn_removeimage_tools"><i class="fas fa-minus"> hapus</i></button></div>');
      $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
      });

    });

    $(document).on('click', '.btn_removeimage_tools', function() {
      var button_id = $(this).attr("id");
      $('#loan_image_tools' + button_id + '').remove();
    });

    var b = 1;
    $('#image_done').click(function() {
      b++;
      $('#loan_image_done').append(' <div id="loan_image_done' + b + '" ><div class="form-group {{ $errors->has('
        img ') ? '
        has - error ' : '
        ' }}"><label for="image_done_file">{{ trans('
        global.loan.fields.image_done_file ') }}</label><div class="custom-file"><input id="image_done_file" name="image_done[]" type="file" class="custom-file-input" id="customFile" required><label class="custom-file-label" for="customFile">Choose file</label></div></div><button type="button" name="remove" id="' + b + '" class="btn btn-danger btn_removeimage_done"><i class="fas fa-minus"> hapus</i></button></div>');
      $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
      });

    });

    $(document).on('click', '.btn_removeimage_done', function() {
      var button_id = $(this).attr("id");
      $('#loan_image_done' + button_id + '').remove();
    });


  });

  $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
  });
</script>
@endsection