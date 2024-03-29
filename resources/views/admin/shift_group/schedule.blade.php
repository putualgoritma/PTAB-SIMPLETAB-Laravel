@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            {{-- <a class="btn btn-success" href="{{ route("admin.schedule.create") }}">
                {{ trans('global.add') }} {{ trans('global.schedule.title_singular') }}
            </a> --}}
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('global.schedule.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('global.schedule.fields.title') }}
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
                    @foreach($schedules as $key => $schedule)
                        <tr data-entry-id="{{ $schedule->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $schedule->title ?? '' }}
                            </td>
                            <td>
                            {{ $schedule->time ?? '' }}
                            </td>
                            <td>
                                {{ $schedule->duration ?? '' }}
                                </td>
                       
                            <td>
                                @if ($schedule->type == "presence" && $schedule->queue == "1")
                                    
                                @can('user_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.shift_group.scheduleEdit', $schedule->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                
                                @endif

                                {{-- @can('user_delete')
                                    <form action="{{ route('admin.schedule.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan --}}
                                                                    
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
{{-- <script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.schedule.massDestroy') }}",
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

</script> --}}
@endsection
@endsection