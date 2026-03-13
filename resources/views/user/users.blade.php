@extends('layouts.app')

@section('title')
    {{ __('Users List') }}
@endsection

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="header">
                    <h4 class="title">
                        <i class="fas fa-users"></i>
                        {{ __('Users List') }}
                        {{-- <a href="{{ route('users.div') }}" class="btn btn-primary text-primary"> --}}
                  {{-- <i class="fas fa-plus"></i> ADD --}}
                {{-- </a> | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
                    </h4>
                </div>
                <div class="content">
                        <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="users" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">FIRST NAME</th>
                                            <th class="text-center">LAST NAME</th>
                                            <th class="text-center">SYSTEM ACCESS</th>
                                            <th class="text-center">ROLE</th>
                                            <th class="text-center">ACTION</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptss')
    @if(isset($_GET["acc"]))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'Access Granted!',
                    text: 'Granted Access to User',
                    icon: 'success'
                }).then(() => {
                    window.location.href = "{{ route('user') }}"; // Replace with your actual route name
                });
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
        $(document).on('click', '#refresh', function(e) {
            var users = $('#users').DataTable();
            users.ajax.reload();
        });
        
        $(document).ready(function () {
            let users = jQuery('#users').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                columnDefs: [
                    { className: "dt-center", targets: [ 0, 1, 2, 3, 4, 5 ] }
                ],

                ajax: {
                    url: "{{ route('user') }}",
                    type: "GET",
                    dataSrc: function (json) {
                        return json.data;
                    }
                },

                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'system_access', name: 'system_access'},
                    {data: 'role', name: 'role'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],

                pagingType: 'full_numbers',
                language: {
                    "emptyTable": "No record available."
                },
                searching: true,
            });
        });

        @if (session()->has('success'))
            Swal.fire(
              'Success!',
              '{{ session('success') }}',
              'success'
            );
        @elseif(session()->has('failed'))
            Swal.fire(
              'Failed!',
              '{{ session('failed') }}',
              'error'
            );
        @endif
    </script>
@endsection



