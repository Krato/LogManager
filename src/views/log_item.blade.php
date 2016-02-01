@extends('admin.layout')

@section('custom_css')
<link type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/jquery-datatable/media/css/jquery.dataTables.css') }}">
<link type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css') }}">
<link media="screen" type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/datatables-responsive/css/datatables.responsive.css') }}">
    <style>
        .table tbody tr td[class*='sorting_']{
            color: #626262;
        }
    </style>
@endsection

@section('content-header')
	<section class="content-header">
	  <h1>
	    {{ trans('log.logs') }}
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url('admin') }}">Admin</a></li>
      <li><a href="{{ url('admin/log') }}">{{ trans('log.logs') }}</a></li>
	    <li class="active">{{ trans('log.logs') }}</li>
	  </ol>
	</section>
@endsection

@section('content')

  <a href="{{ url('admin/log') }}"><i class="fa fa-angle-double-left"></i> {{ trans('log.back_to_all_logs') }}</a><br><br>
<!-- Default box -->
  <div class="box">
    <div class="box-body">
      <h3>{{ Carbon::createFromTimeStamp($logs['data']['last_modified'])->formatLocalized('%d %B %Y') }}</h3>
        @if ($logs === null)
            <div>
                Log file >50M, please download it.
            </div>
        @else
            <table id="table-log" class="table table-striped">
                <thead>
                <tr>
                    <th>Level</th>
                    <th>Date</th>
                    <th>Content</th>
                </tr>
                </thead>
                <tbody>

                @foreach($logs as $key => $log)
                    @if($key != "data")
                        <tr>
                            <td class="text-{{{$log['level_class']}}}"><span class="glyphicon glyphicon-{{{$log['level_img']}}}-sign" aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                            <td class="date">{{{$log['date']}}}</td>
                            <td class="text">
                                @if ($log['stack']) <a class="pull-right expand btn btn-default btn-xs" data-display="stack{{{$key}}}"><span class="glyphicon glyphicon-search"></span></a>@endif
                                {{{$log['text']}}}
                                @if (isset($log['in_file'])) <br />{{{$log['in_file']}}}@endif
                                @if ($log['stack']) <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</div>@endif
                            </td>
                        </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        @endif

    </div><!-- /.box-body -->
  </div><!-- /.box -->

@endsection

@section('custom_js')
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/admin_theme/assets/plugins/datatables-responsive/js/datatables.responsive.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/datatables-responsive/js/lodash.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_theme/assets/plugins/pnotify.custom.min.js') }}" type="text/javascript"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.7/api/fnReloadAjax.js" type="text/javascript"></script>


    <script src="{{ asset('/admin_theme/assets/plugins/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#table-log').DataTable({
            "language": {
                "emptyTable":     "{{ _(trans('crud.emptyTable')) }}",
                "info":           "{{ _(trans('crud.info')) }}",
                "infoEmpty":      "{{ _(trans('crud.infoEmpty')) }}",
                "infoFiltered":   "{{ _(trans('crud.infoFiltered')) }}",
                "infoPostFix":    "{{ _(trans('crud.infoPostFix')) }}",
                "thousands":      "{{ _(trans('crud.thousands')) }}",
                "lengthMenu":     "{{ _(trans('crud.lengthMenu')) }}",
                "loadingRecords": "{{ _(trans('crud.loadingRecords')) }}",
                "processing":     "{{ _(trans('crud.processing')) }}",
                "search":         "{{ _(trans('crud.search')) }}",
                "zeroRecords":    "{{ _(trans('crud.zeroRecords')) }}",
                "paginate": {
                    "first":      "{{ _(trans('crud.paginate.first')) }}",
                    "last":       "{{ _(trans('crud.paginate.last')) }}",
                    "next":       "{{ _(trans('crud.paginate.next')) }}",
                    "previous":   "{{ _(trans('crud.paginate.previous')) }}"
                },
                "aria": {
                    "sortAscending":  "{{ _(trans('crud.aria.sortAscending')) }}",
                    "sortDescending": "{{ _(trans('crud.aria.sortDescending')) }}"
                }
            },
            "sDom": "<'table-responsive't><'row'<p i>>",
            "sPaginationType": "bootstrap",
            "destroy": true,
            "responsive": true,
            "order": [ 1, 'desc' ],
            "stateSave": true,
            "iDisplayLength": 15,
            "stateSaveCallback": function (settings, data) {
                window.localStorage.setItem("datatable", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
                var data = JSON.parse(window.localStorage.getItem("datatable"));
                if (data) data.start = 0;
                return data;
            }
        });


        $(document).on('click', '.expand', function(){
            $('#' + $(this).data('display')).toggle();
        });
        $('#delete-log').click(function(){
            return confirm('Are you sure?');
        });
    });
</script>
@endsection
