@extends('admin.layout')
@section('custom_css')
        <!-- DATA TABLES -->
<link type="text/css" rel="stylesheet" href="{{ asset('admin_theme/assets/plugins/sweetalert/sweetalert.css') }}">

@endsection
@section('content-header')
	<section class="content-header">
	  <h1>
	    {{ trans('log.logs') }}
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url('admin') }}">Admin</a></li>
	    <li class="active">{{ trans('log.logs') }}</li>
	  </ol>
	</section>
@endsection

@section('content')
<!-- Default box -->
  <div class="box">
    <div class="box-body">
      <table class="table table-hover table-condensed">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('log.date') }}</th>
            <th>{{ trans('log.last_modified') }}</th>
            <th class="text-right">{{ trans('log.file_size') }}</th>
            <th>{{ trans('log.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($logs as $k => $log)
          <tr>
            <td scope="row">{{ $k+1 }}</td>
            <td>{{ Carbon::createFromTimeStamp($log['last_modified'])->formatLocalized('%d %B %Y') }}</td>
            <td>{{ Carbon::createFromTimeStamp($log['last_modified'])->formatLocalized('%H:%M') }}</td>
            <td class="text-right">{{ round((int)$log['file_size']/1048576, 2).' MB' }}</td>
            <td>
              @if (\Entrust::can('preview-logs'))
                <a class="btn btn-xs btn-default" href="{{ url('admin/log/preview/'.$log['file_name']) }}"><i class="fa fa-eye"></i> {{ trans('log.preview') }}</a>
              @endif
              @if (\Entrust::can('download-logs'))
                <a class="btn btn-xs btn-default" href="{{ url('admin/log/download/'.$log['file_name']) }}"><i class="fa fa-cloud-download"></i> {{ trans('log.download') }}</a>
              @endif
              @if (\Entrust::can('delete-logs'))
                <a class="btn btn-xs btn-danger" data-button-type="delete" href="{{ url('admin/log/delete/'.$log['file_name']) }}"><i class="fa fa-trash-o"></i> {{ trans('log.delete') }}</a>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div><!-- /.box-body -->
  </div><!-- /.box -->
  <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
@endsection

@section('custom_js')
    <script src="{{ asset('/admin_theme/assets/plugins/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
<script>
  jQuery(document).ready(function($) {

    // capture the delete button
      $("[data-button-type=delete]").click(function(e) {
          e.preventDefault();
          var delete_button = $(this);
          var delete_url = $(this).attr('href');


          swal({  title: "<?php echo _(Lang::get('crud.delete_confirm')) ?>",
              text: "<?php echo _(Lang::get('crud.delete_info')) ?>",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "<?php echo _(trans('crud.delete_confirm_yes_delete')) ?>",
              cancelButtonText: "{{ _(trans('crud.delete_cancel')) }}",
              closeOnConfirm: true
          }, function(isConfirm){
              if (isConfirm) {

                  $.ajax({
                      url: delete_url,
                      beforeSend: function (request){
                          request.setRequestHeader("X-CSRF-TOKEN", $('[name="_token"]').val());
                      },
                      type: 'DELETE',
                      success: function(result) {
                          // Show an alert with the result
                          new PNotify({
                              title: "{{ _(trans('crud.delete_confirmation_title')) }}",
                              text: "{{ _(trans('crud.delete_confirmation_message')) }}",
                              type: "success"
                          });
                          // delete the row from the table
                          delete_button.parentsUntil('tr').parent().remove();
                      },
                      error: function(result) {
                          // Show an alert with the result
                          new PNotify({
                              title: "{{ _(trans('crud.delete_confirmation_not_title')) }}",
                              text: "{{ _(trans('crud.delete_confirmation_not_message')) }}",
                              type: "warning"
                          });
                      }
                  });

              } else {

                  new PNotify({
                      title: "{{ _(trans('crud.delete_confirmation_not_deleted_title')) }}",
                      text: "{{ _(trans('crud.delete_confirmation_not_deleted_message')) }}",
                      type: "info"
                  });

              }
          });


      });

  });
</script>
@endsection
