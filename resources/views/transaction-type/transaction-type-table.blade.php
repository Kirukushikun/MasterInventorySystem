@extends('layouts.app')

@section('title')
	{{ __('Transaction Type List') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('TRANSACTION TYPE LIST') }}
						<a href="{{ route('transaction.type.div') }}" class="btn btn-primary text-primary">
		          <i class="fas fa-plus"></i> ADD
		        </a> {{-- | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
					</h4>
				</div>
				<div class="content">
						<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
                                <table id="transaction_type" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>NAME</th>
                                            <th>DESCRIPTION</th>
                                            <th>DATE CREATED</th>
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
		let transaction_type = null;
        $(document).ready(function () {
            transaction_type = jQuery('#transaction_type').DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                columnDefs: [
                    { targets: [ 0, 1, 2, 3, 4 ] }
                ],

                ajax: {
                    url: "{{ route('transaction.type.list') }}",
                    type: "GET",
                    dataSrc: function (json) {
                        return json.data;
                    }
                },

                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'date_created', name: 'date_created'},
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
            transaction_type = $('#transaction_type').DataTable();
                $('#series_location').val('');
            var nurl = "/farm/location/list/all/fl/rf";
            transaction_type.ajax.url(nurl).load();

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


