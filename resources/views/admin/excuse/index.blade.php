@extends('layouts.admin')
@section('content')
{{-- @can('excuse_create') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-3">
            <a class="btn btn-success" href="{{ route('admin.excuse.create',['type'=>'excuseIn']) }}">
                {{ trans('global.add') }} {{ trans('global.excuse.title_singular') }}
            </a>
        </div>
    </div>
    
{{-- @endcan --}}
<div class="card">

    <div class="card-header">
        {{ trans('global.excuse.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
    <div class="form-group">
        <div class="col-md-6">
             <form action="" id="filtersForm">
                <div class="input-group">
                    <select id="status" name="status" class="form-control">
                        <option value="">== Semua Status ==</option>
                        <option value="pending">pending</option>
                        <option value="approve">approve</option>
                        <option value="active">active</option>
                        <option value="reject">reject</option>
                        <option value="close">close</option>
                    </select>
                    <span class="input-group-btn">
                    &nbsp;&nbsp;<input type="submit" class="btn btn-primary" value="Filter">
                    </span>
                </div>                
             </form>
             </div> 
        </div>
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable ajaxTable datatable-excuse">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            No.
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.staff_name') }}
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.category') }}
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.type') }}
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.start') }}
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.time') }}
                        </th>

                        <th>
                            {{ trans('global.excuse.fields.description') }}
                        </th>
                        <th>
                            {{ trans('global.excuse.fields.status') }}
                        </th>
                   
                        {{-- <th>
                            {{ trans('global.excuse.fields.upstartd_at') }}
                        </th> --}}
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                
            </table>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $(function () {
        let searchParams = new URLSearchParams(window.location.search)
        let status = searchParams.get('status')
        if (status) {
            $("#status").val(status);
        }else{
            $("#status").val('');
        }

        // console.log('type : ', type);

  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "",
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
    @can('excuse_delete')
    dtButtons.push(deleteButton)
    @endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })

  let dtOverrideGlobals = {
    buttons: dtButtons,
    serverSide: true,
    aaSorting: [],
    ajax: {
      url: "{{ route('admin.excuse.index') }}",
      data: {
        'status': $("#status").val(),
      },
      dataType: "JSON"
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'DT_RowIndex', name: 'no', searchable : false },
        { data: 'staff_name', name: 'staffs.name' },
        { data: 'category', name: 'category' },
        { data: 'type', name: 'type' },
        { data: 'start', name: 'start' },
        { data: 'time', name: 'time' },
        { data: 'description', name: 'description' },
        { data: 'status', name: 'status' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    pageLength: 100,
  };

  $('.datatable-excuse').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
@endsection