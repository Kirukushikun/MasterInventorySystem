@extends('layouts.app')

@section('title')
	{{ __('Requested Item List') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('REQUESTED ITEM LIST') }}
						<a href="{{ route('request.item') }}" class="btn btn-primary text-primary">
		          <i class="fas fa-plus"></i> ADD
		        </a> {{-- | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
					</h4>
				</div>
				<div class="content">
						<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
                                <table id="req_items" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID.</th>
                                            <th>SERIES NO.</th>
                                            <th>REQUESTED BY</th>
                                            <th>LOCATION</th>
                                            <th>DEPARTMENT</th>
                                            <th>STATUS</th>
                                            <th>ITEM REQUESTED</th>
                                            <th>DATE REQUESTED</th>
                                            <th>DATE NEEDED</th>
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
    @php
        $currentRoute = request()->route();
        $currentUrlWithoutQuery = strtok(url()->full(), '?');
        $currentRoute->uriWithoutQuery = $currentUrlWithoutQuery;
    @endphp
    @if(isset($_GET["del"]))
         @if($_GET["del"] == 1)
            <script>
                Swal.fire({
                    title: 'Deleted!',
                    text: '{{ $_GET["cat"] }} Succesfully Deleted!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
                });
            </script>
        @else
            <script>
                Swal.fire({
                    title: 'Delete Failed!',
                    html: '{{ $_GET["cat"] }} Parent Database Dependency!<br>Please Remove Child Database Dependency First',
                    icon: 'error'
                }).then(() => {
                    window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
                });
            </script>
        @endif
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
		let req_items = null;
        $(document).ready(function () {
            req_items = jQuery('#req_items').DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                columnDefs: [
                    { targets: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] }
                ],

                ajax: {
                    url: "{{ route('request.item.list') }}",
                    type: "GET",
                    dataSrc: function (json) {
                        return json.data;
                    }
                },

                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'series_number', name: 'series_number'},
                    {data: 'requested_by', name: 'requested_by'},
                    {data: 'location', name: 'location'},
                    {data: 'department_division', name: 'department_division'},
                    {data: 'status', name: 'status'},
                    {data: 'items_requested', name: 'items_requested'},
                    {data: 'date_requested', name: 'date_requested'},
                    {data: 'date_needed', name: 'date_needed'},
                    {data: 'action', name: 'action'},
                ],

                pagingType: 'full_numbers',
                language: {
                    "emptyTable": "No record available."
                },
                searching: true,
            });
        });

        $(document).on('click', '#refresh', function(e) {
            req_items = $('#req_items').DataTable();
                $('#series_location').val('');
            var nurl = "/farm/location/list/all/fl/rf";
            req_items.ajax.url(nurl).load();

            Swal.fire({
                title: 'Table Has Been Refreshed!',
                text: "",
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Okay'
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


