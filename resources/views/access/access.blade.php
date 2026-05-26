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
                        <i class="fas fa-user-shield"></i> {{ __('ACCESS LIST') }}
                    </h4>
                </div>
                <div class="content">
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="accesses" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>FULL NAME</th>
                                            <th>FARM LOCATION</th>
                                            <th>DEPARTMENT/DIVISION</th>
                                            <th>ACCESS</th>
                                            <th>ACTION</th>
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
    <script>
        $(document).ready(function () {
            $('#accesses').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                columnDefs: [
                    { className: "dt-center", targets: [0, 1, 2, 3, 4, 5] }
                ],
                fixedColumns: {
                    rightColumns: 1,
                    leftColumns: 0
                },
                ajax: "{{ route('access') }}",
                columns: [
                    { data: 'id',                  name: 'id' },
                    { data: 'full_name',            name: 'full_name' },
                    { data: 'farm_location',        name: 'farm_location' },
                    { data: 'department_division',  name: 'department_division' },
                    { data: 'access',               name: 'access' },
                    { data: 'action',               name: 'action', orderable: false, searchable: false, className: 'bg-light' },
                ],
                pagingType: 'full_numbers',
                language: {
                    emptyTable: 'No record available.'
                },
                searching: true,
            });
        });
    </script>
@endsection



