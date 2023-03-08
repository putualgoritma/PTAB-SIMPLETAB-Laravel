@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.work_type_day.create") }}">
                {{ trans('global.add') }} {{ trans('global.work_type_day.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.work_type_day.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.work_type_day.fields.day') }}
                        </th>
                        <th>
                            {{ trans('global.work_type_day.fields.category') }}
                        </th>
                        <th>
                            {{ trans('global.work_type_day.fields.time') }}
                        </th>
                        <th>
                            {{ trans('global.work_type_day.fields.duration') }}
                        </th>

                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($work_type_days as $key => $work_type_day)
                        <tr data-entry-id="{{ $work_type_day->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $work_type_day->day ?? '' }}
                            </td>
                            <td>
                                {{ $work_type_day->category ?? '' }}
                                </td>
                            <td>
                            {{ $work_type_day->time ?? '' }}
                            </td>
                            <td>
                                {{ $work_type_day->duration ?? '' }}
                            </td>
                       
                            <td>
                                @can('user_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.work_type_day.show', $work_type_day->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                {{-- @if ($work_type_day->id != 1 && $work_type_day->id != 2) --}}
@if ($work_type_day->type == "presence" && $work_type_day->queue == "1")
@can('user_edit')
<a class="btn btn-xs btn-info" href="{{ route('admin.work_type_day.edit', $work_type_day->id) }}">
    {{ trans('global.edit') }}
</a>
@endcan
@endif
                              
                                @can('user_delete')
                                    <form action="{{ route('admin.work_type_day.destroy', $work_type_day->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                                                                    
                                {{-- @endif --}}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.work_type_day.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('user_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
@endsection