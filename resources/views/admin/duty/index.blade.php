@extends('layouts.admin')
@section('content')
{{-- @can('duty_create') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-2">
            <a class="btn btn-success" href="{{ route('admin.duty.create',['type'=>'visit']) }}">
                {{ trans('global.add') }} {{ trans('global.duty.title_singular') }}(Masuk)
            </a>
        </div>
        <div class="col-lg-6">
            <a class="btn btn-warning" href="{{ route('admin.duty.create',['type'=>'duty']) }}">
                {{ trans('global.add') }} {{ trans('global.duty.title_singular') }}(Keluar)
            </a>
        </div>
    </div>
    
{{-- @endcan --}}
<div class="card">

    <div class="card-header">
        {{ trans('global.duty.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
    <div class="form-group">
        <div class="col-md-12">
             <form action="" id="filtersForm">
                <div class="input-group">
                <div class="col-md-6">
                    <label for="">Tipe</label>
           
                   
                    <select id="category" name="category" class="form-control">
                        <option value="">== Semua Tipe ==</option>
                        <option value="duty">Duty</option>
                        <option value="visit">Visit</option>
                    </select>
                <label for="">Status</label>  
                   
                    <select id="status" name="status" class="form-control">
                        <option value="">== Semua Status ==</option>
                        <option value="pending">pending</option>
                        <option value="approve">approve</option>
                        <option value="active">active</option>
                        <option value="reject">reject</option>
                        <option value="close">close</option>
                    </select>
                </div>   
          


            <div class="col-md-6">
                <div class="form-group">
                    <label>Dari Tanggal</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                        <input id="from" placeholder="masukkan tanggal Awal" type="date" class="form-control datepicker" name="from" value = "{{request()->input('from') ? request()->input('from') : ""}}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Sampai Tanggal</label>
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                        <input id="to" placeholder="masukkan tanggal Akhir" type="date" class="form-control datepicker" name="to" value = "{{request()->input('to') ? request()->input('to') : date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

                    <span class="input-group-btn">
                    &nbsp;&nbsp;<input type="submit" class="btn btn-primary" value="Filter">
                    </span>
                            
             </form>
             </div> 
        </div>
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable ajaxTable datatable-duty">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            No.
                        </th>
                        {{-- <th>
                            {{ trans('global.duty.fields.id') }}
                        </th> --}}
                        <th>
                            {{ trans('global.duty.fields.staff_name') }}
                        </th>
                        <th>
                            {{ trans('global.duty.fields.category') }}
                        </th>
                        <th>
                            {{ trans('global.duty.fields.type') }}
                        </th>
                        <th>
                            {{ trans('global.duty.fields.start') }}
                        </th>

                        <th>
                            {{ trans('global.duty.fields.end') }}
                        </th>

                        <th>
                            {{ trans('global.duty.fields.description') }}
                        </th>
                        <th>
                            {{ trans('global.duty.fields.status') }}
                        </th>

                        <th>
                            {{ trans('global.duty.fields.created_at') }}
                        </th>
                   
                        {{-- <th>
                            {{ trans('global.duty.fields.upstartd_at') }}
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
        let category = searchParams.get('category')
        if (category) {
            $("#category").val(category);
        }else{
            $("#category").val('');
        }

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
    @can('duty_delete')
    dtButtons.push(deleteButton)
    @endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })

  let dtOverrideGlobals = {
    buttons: dtButtons,
    serverSide: true,
    aaSorting: [],
    ajax: {
      url: "{{ route('admin.duty.index') }}",
      data: {
        'category': $("#category").val(),
        'status': $("#status").val(),
        'from': $("#from").val(),
        'to': $("#to").val(),
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
        { data: 'end', name: 'end' },
        { data: 'description', name: 'description' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    pageLength: 100,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "order": [[ 9, "desc" ]]
  };

  $('.datatable-duty').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
@endsection