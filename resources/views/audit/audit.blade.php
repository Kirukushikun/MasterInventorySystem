@extends('layouts.app')

@section('title')
  AUDIT TRAIL
@endsection

@section('content')<br>
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit"></i> {{ __('AUDIT TRAIL') }} </h3>
                {{-- <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
              </div>
              {{-- <p>
                @php
                  var_dump($sample);
                @endphp
              </p> --}}
              <div class="card-body">
                <div class="table-responsive">
                  <table id="audits" class="table table-bordered table-hover text-nowrap" style="width: 100%;">
                    <thead>
                      <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">USERS</th>
                        <th class="text-center">TABLE</th>
                        <th class="text-center">ACTION</th>
                        <th class="text-center">NEW VALUE</th>
                        <th class="text-center">OLD VALUE</th>
                        <th class="text-center">DATE / TIME</th>
                        <th class="text-center">VIEW</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection

@section('scriptss')
    @php
        $currentRoute = request()->route();
        $currentUrlWithoutQuery = strtok(url()->full(), '?');
        $currentRoute->uriWithoutQuery = $currentUrlWithoutQuery;
    @endphp
    @if(isset($_GET["del"]))
        <script>
            Swal.fire({
                title: 'Deleted!',
                text: '{{ $_GET["cat"] }} Succesfully Deleted!',
                icon: 'success'
            }).then(() => {
                window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
            });
        </script>
    @endif
  <script>
    @if(session('success'))
            Swal.fire(
              'Success!',
              '{{ session('success') }}',
              'success'
            );
        @elseif(session('failed'))
            Swal.fire(
              'Failed!',
              '{{ session('failed') }}',
              'error'
            );
        @endif
    // $(document).ready(function () {
    //   let audits = $('#audits').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     scrollX: true,
    //     columnDefs: [
    //       { className: "dt-center", targets: [ 0, 1, 2, 3, 4, 5, 6, 7 ] }
    //     ],

    //     ajax: "{{ route('audit') }}",
    //     columns: [
    //       {data: 'id', name: 'id'},
    //       {data: 'user', name: 'user'},
    //       {data: 'table', name: 'table'},
    //       {data: 'action', name: 'action'},
    //       {data: 'new_value', name: 'new_value'},
    //       {data: 'old_value', name: 'old_value'},
    //       {data: 'date_time', name: 'date_time'},
    //       {data: 'view', name: 'view'},
    //     ],
    //     pagingType: 'full_numbers',
    //     language: {
    //       "emptyTable": "No record available."
    //     },
    //     searching: true,
    //   });
    // });

    $(document).on('click', '#refresh', function(e) {
      var audits = $('#audits').DataTable();
      audits.ajax.reload();
    });

    $(document).ready(function () {
      let audits = jQuery('#audits').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        columnDefs: [
          { className: "dt-center", targets: [ 0, 1, 2, 3, 4, 5, 6, 7 ] }
        ],

        ajax: {
          url: "{{ route('audit') }}",
          type: "GET",
          dataSrc: function (json) {
          return json.data;
          }
        },

        columns: [
          {data: 'id', name: 'id'},
          {data: 'user', name: 'user'},
          {data: 'table', name: 'table'},
          {data: 'action', name: 'action'},
          {data: 'new_value', name: 'new_value'},
          {data: 'old_value', name: 'old_value'},
          {data: 'date_time', name: 'date_time'},
          {data: 'view', name: 'view'},
        ],

        pagingType: 'full_numbers',
        language: {
          "emptyTable": "No record available."
        },
        searching: true,
      });
    });
  </script>
@endsection
