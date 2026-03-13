@extends('layouts.app')

@section('title')
    {{ __('Access List') }}
@endsection

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="header">
                    <h4 class="title">
                        <i class="fas fa-users"></i><i class="fas fa-check"></i>
                        {{ __('Access List') }}
                        {{-- <a href="{{ route('users.div') }}" class="btn btn-primary text-primary"> --}}
                          {{-- <i class="fas fa-plus"></i> ADD --}}
                        {{-- </a> | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
                    </h4>
                </div>
                <div class="content">
                        <hr>
                    <div class="row">
                        <div class="col-md-12">
                            {{-- @inject('acc', '\App\Http\Controllers\AccessController')
                            @if($acc::checkAccess(Auth::id(), 'access_mod')) --}}
                                <div class="table-responsive">
                                    <table id="accesses" class="table table-bordered table-hover text-nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">FULL NAME</th>
                                                <th class="text-center">FARM LOCATION</th>
                                                <th class="text-center">DEPARTMENT/DIVISION</th>
                                                <th class="text-center">ACCESS</th>
                                                <th class="text-center">ACTION</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptss')
    <script>
        $(document).ready(function () {
            let accessFarm = $('#accesses').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                columnDefs: [
                    { className: "dt-center", targets: [ 0, 1, 2, 3, 4, 5 ] }
                ],
                fixedColumns:   {
                    rightColumns: 1,
                    leftColumns: 0
                },
                ajax: "{{ route('access') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'farm_location', name: 'farm_location'},
                    {data: 'department_division', name: 'department_division'},
                    {data: 'access', name: 'access'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: 'bg-light'},
                ],
                pagingType: 'full_numbers',
                language: {
                    "emptyTable": "No record available."
                },
                searching: true,
            });
        });


        $(document).on('click', '#refresh', function(e) {
            var accessFarm = $('#accesses').DataTable();
            accessFarm.ajax.reload();
        });
    </script>
@endsection



