@extends('layouts.default')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.min.css" integrity="sha256-6N5jjMWxse9ctjpl9BXZOd811lGA2+MRswGwHpQ9ZaI=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.brighttheme.min.css" integrity="sha256-FOfyh9yXiYNhKvUKFXYZvMQnuHohW8VFaW+0F4wJ5ls=" crossorigin="anonymous" />
@endsection

@section('content')
<div class="container wrapper">
    <section class="content-header">
      <h1>
        {{ trans('backup.title') }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}">Admin</a></li>
        <li class="active">{{ trans('backup.title') }}</li>
      </ol>
    </section>

<!-- Default box -->
  <div class="box">
    <div class="box-body">
      <button id="create-new-backup-button" href="{{ route('backup.create') }}" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('backup.create') }}</span></button>
      <br>
      <h3>{{ trans('backup.existing') }}:</h3>
      <table class="table table-hover table-condensed">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('backup.header.date') }}</th>
            <th class="text-right">{{ trans('backup.header.size') }}</th>
            <th>{{ trans('backup.header.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($backups as $k => $b)
          <tr>
            <td scope="row">{{ $k+1 }}</td>
            <td>{{ \Carbon\Carbon::createFromTimeStamp($b['last_modified'])->formatLocalized('%d %B %Y, %H:%M') }}</td>
            <td class="text-right">{{ round((int)$b['file_size']/1048576, 2).' MB' }}</td>
            <td>
                <a class="btn btn-xs btn-default" href="{{ route('backup.download', '?disk='.$b['disk'].'&file_name='.urlencode($b['file_name'])) }}"><i class="fa fa-cloud-download"></i> {{ trans('backup.download') }}</a>
                <a class="btn btn-xs btn-danger" data-button-type="delete" href="{{ route('backup.delete', '?disk='.$b['disk'].'&file_name='.urlencode($b['file_name'])) }}"><i class="fa fa-trash-o"></i> {{ trans('backup.delete') }}</a>              
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div><!-- /.box-body -->
  </div><!-- /.box -->
</div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.0/spin.min.js" integrity="sha256-pqZ6Oldgr1fHcY0qoxHEl/8bvfZIHU0lSbLT5oNdEgY=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Ladda/1.0.0/ladda.min.js" integrity="sha256-/DTavTzjSAI87+voZGCTfhbioWGET1qDJKe76XuWQ5M=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pnotify/3.0.0/pnotify.js" integrity="sha256-GAfixIoH2j0mOpCXEYzQsHafULOiY3/pwkbVYTUFZgg=" crossorigin="anonymous"></script>

<script>
  jQuery(document).ready(function($) {

    // capture the Create new backup button
    $("#create-new-backup-button").click(function(e) {
        e.preventDefault();
        var create_backup_url = $(this).attr('href');

        console.log(create_backup_url)
        // Create a new instance of ladda for the specified button
        var l = Ladda.create( document.querySelector( '#create-new-backup-button' ) );

        // Start loading
        l.start();

        // Will display a progress bar for 10% of the button width
        l.setProgress( 0.3 );

        setTimeout(function(){ l.setProgress( 0.6 ); }, 2000);

        // do the backup through ajax
        $.ajax({
                url: create_backup_url,
                type: 'POST',
                beforeSend: function (request){
                    request.setRequestHeader("X-CSRF-TOKEN", $('[name="_token"]').val());
                },
                success: function(result) {
                    l.setProgress( 0.9 );
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('backup.create_confirmation_title') }}",
                        text: "{{ trans('backup.create_confirmation_message') }}",
                        type: "success"
                    });
                    // Stop loading
                    l.setProgress( 1 );
                    l.stop();

                    // refresh the page to show the new file
                    // setTimeout(function(){ location.reload(); }, 3000);
                },
                error: function(result) {
                    l.setProgress( 0.9 );
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('backup.create_error_title') }}",
                        text: "{{ trans('backup.create_error_message') }}",
                        type: "warning"
                    });
                    // Stop loading
                    l.stop();
                }
            });
    });

    // capture the delete button
    $("[data-button-type=delete]").click(function(e) {
        e.preventDefault();
        var delete_button = $(this);
        var delete_url = $(this).attr('href');

        if (confirm("{{ trans('backup.delete_confirm') }}") == true) {
            $.ajax({
                url: delete_url,
                type: 'DELETE',
                beforeSend: function (request){
                    request.setRequestHeader("X-CSRF-TOKEN", $('[name="_token"]').val());
                },
                success: function(result) {
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('backup.delete_confirmation_title') }}",
                        text: "{{ trans('backup.delete_confirmation_message') }}",
                        type: "success"
                    });
                    // delete the row from the table
                    delete_button.parentsUntil('tr').parent().remove();
                },
                error: function(result) {
                    // Show an alert with the result
                    new PNotify({
                        title: "{{ trans('backup.delete_error_title') }}",
                        text: "{{ trans('backup.delete_error_message') }}",
                        type: "warning"
                    });
                }
            });
        } else {
            new PNotify({
                title: "{{ trans('backup.delete_cancel_title') }}",
                text: "{{ trans('backup.delete_cancel_message') }}",
                type: "info"
            });
        }
      });

  });
</script>
@endsection
