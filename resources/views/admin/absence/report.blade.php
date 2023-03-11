@extends('layouts.admin')
@section('content')
{{-- @can('absence_create') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.absence.create') }}">
                {{ trans('global.add') }} {{ trans('global.absence.title_singular') }}
            </a>
        </div>
    </div>
    
{{-- @endcan --}}
<div class="card">

    <div class="card-header">
        {{ trans('global.absence.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="card-body">
    <div class="form-group">
        <div class="col-md-6">
             <form action="" id="filtersForm">
                <div class="input-group">
                    <select id="type" name="type" class="form-control">
                        <option value="">== Semua Tipe ==</option>
                        <option value="absence">Pelanggan</option>
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
            <table class=" table table-bordered table-striped table-hover datatable ajaxTable datatable-absence">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            No.
                        </th>
                        <th>
                            {{ trans('global.absence.fields.staff_code') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.staff_name') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.job_name') }}
                        </th>
                        {{-- <th>
                            {{ trans('global.absence.fields.lat') }}
                        </th> --}}
                        {{-- <th>
                            {{ trans('global.absence.fields.lng') }}
                        </th> --}}
                        <th>
                            {{ trans('global.absence.fields.abtotal') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.jumlah_sabtuminggu') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.total_efektif_kerja') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.hadir') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.izin') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.dinas_luar') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.cuti') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.jam_hadir') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.jam_istirahat') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.jam_lembur') }}
                        </th>
                        <th>
                            {{ trans('global.absence.fields.jam_dinas_dalam') }}
                        </th>
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
    @can('absence_delete')
    dtButtons.push(deleteButton)
    @endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })

  let dtOverrideGlobals = {
    buttons: dtButtons,
    serverSide: true,
    aaSorting: [],
    ajax: {
      url: "{{ route('admin.absence.reportAbsence') }}",
      data: {
        'type': $("#type").val(),
      },
      dataType: "JSON"
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'DT_RowIndex', name: 'no' },
        { data: 'staff_code', name: 'staff_code' },
        { data: 'staff_name', name: 'staff_name' },
        { data: 'job_name', name: 'job_name' },
        { data: 'abtotal', name: 'abtotal' },
        { data: 'jumlah_sabtuminggu', name: 'jumlah_sabtuminggu' },
        { data: 'efective_kerja', name: 'efective_kerja' },
        { data: 'hadir', name: 'hadir' },
        { data: 'izin', name: 'izin' },
        { data: 'dinas_luar', name: 'dinas_luar' },
        { data: 'cuti', name: 'cuti' },
        { data: 'jam_hadir', name: 'jam_hadir' },
        { data: 'jam_istirahat', name: 'jam_istirahat' },
        { data: 'jam_lembur', name: 'jam_lembur' },
        { data: 'jam_dinas_dalam', name: 'jam_dinas_dalam' },
        // { data: 'late', name: 'late' },
        // { data: 'image', name: 'image' ,  render: function( data, type, full, meta ) {
        //                 return "<img src=\"{{ asset('') }}"+ data + "\" width=\"150\"/>";
        //             }},
        // { data: 'staff_image', name: 'staff_image' ,  render: function( data, type, full, meta ) {
        //                 return "<img src=\"{{ asset('') }}"+ data + "\" width=\"150\"/>";
        //             }},
        // { data: 'updated_at', name: 'updated_at' },
        { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    pageLength: 100,
  };

  $('.datatable-absence').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
@endsection