@extends('layouts.admin')
@section('content')
{{-- @can('holiday_create') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.holiday.create') }}">
                {{ trans('global.add') }} {{ trans('global.holiday.title_singular') }}
            </a>
        </div>
    </div>
    
{{-- @endcan --}}
<div class="card">

    <div class="card-header">
        {{ trans('global.holiday.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
    <div class="form-group">
        <div class="col-md-6">
             <form action="" id="filtersForm">
                <div class="input-group">
                    <select id="type" name="type" class="form-control">
                        <option value="">== Semua Tipe ==</option>
                        <option value="holiday">Pelanggan</option>
                        <option value="public">Umum</option>
                    </select>
                    <span class="input-group-btn">
                    &nbsp;&nbsp;<input type="submit" class="btn btn-primary" value="Filter">
                    </span>
                </div>                
             </form>
             </div> 
        </div>
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable ajaxTable datatable-holiday">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            No.
                        </th>
                        {{-- <th>
                            {{ trans('global.holiday.fields.id') }}
                        </th> --}}
                        <th>
                            {{ trans('global.holiday.fields.title') }}
                        </th>
                        <th>
                            {{ trans('global.holiday.fields.day') }}
                        </th>
                        <th>
                            {{ trans('global.holiday.fields.date') }}
                        </th>
                        <th>
                            {{ trans('global.holiday.fields.description') }}
                        </th>
                   
                        {{-- <th>
                            {{ trans('global.holiday.fields.updated_at') }}
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
        let type = searchParams.get('type')
        if (type) {
            $("#type").val(type);
        }else{
            $("#type").val('');
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
    @can('holiday_delete')
    dtButtons.push(deleteButton)
    @endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })

  let dtOverrideGlobals = {
    buttons: dtButtons,
    serverSide: true,
    aaSorting: [],
    ajax: {
      url: "{{ route('admin.holiday.index') }}",
      data: {
        'type': $("#type").val(),
      },
      dataType: "JSON"
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'DT_RowIndex', name: 'no' },
        { data: 'title', name: 'title' },
        { data: 'day', name: 'day' },
        { data: 'date', name: 'date' },
        { data: 'description', name: 'description' },
        // { data: 'updated_at', name: 'updated_at' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    pageLength: 100,
  };

  $('.datatable-holiday').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
@endsection