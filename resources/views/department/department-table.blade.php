@extends('layouts.app')

@section('title')
	{{ __('Department\Division List') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('DEPARTMENT\DIVISION LIST') }}
						<a href="{{ route('dept.div') }}" class="btn btn-primary text-primary">
		          <i class="fas fa-plus"></i> ADD
		        </a> {{-- | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
					</h4>
				</div>
				<div class="content">
						<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
                                <table id="department_division" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>DEPARTMENT</th>
                                            <th>DEPARTMENT CODE</th>
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
		let department_division = null;
        $(document).ready(function () {
            department_division = jQuery('#department_division').DataTable({
                processing: true,
                serverSide: false,
                scrollX: true,
                columnDefs: [
                    { targets: [ 0, 1, 2, 3 ] }
                ],

                ajax: {
                    url: "{{ route('department.list') }}",
                    type: "GET",
                    dataSrc: function (json) {
                        return json.data;
                    }
                },

                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'department', name: 'department'},
                    {data: 'abbreviation', name: 'abbreviation'},
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
            department_division = $('#department_division').DataTable();
                $('#series_location').val('');
            var nurl = "/farm/location/list/all/fl/rf";
            department_division.ajax.url(nurl).load();

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

    let series_location = '';

    $('#series_location').val('');

		// Define "series_location" filter
		$('#series_location').change(function() {
			series_location = $(this).val();

			newurl = "/farm/location/list/location/" + series_location;
			department_division.ajax.url(newurl).load();
			console.log(newurl);

		});


		// Clears the List
		$('#clear_filter').click(function () {
		  Swal.fire({
		    title: 'Are you sure you want to remove the filter?',
		    icon: 'info',
		    showCancelButton: true,
		    confirmButtonColor: '#3085d6',
		    cancelButtonColor: '#d33',
		    confirmButtonText: 'Yes, remove it!'
		  }).then((result) => {
		    if (result.isConfirmed) {
		      $('#series_location').val('');
		      series_location = null;
		      var nurl = "/farm/location/list/all/fl/rf";
		      department_division.ajax.url(nurl).load();
		      Swal.fire({
		        title: 'Filter Removed!',
		        text: "",
		        icon: 'success',
		        confirmButtonColor: '#3085d6',
		        confirmButtonText: 'Close'
		      });
		    }else{
		    	Swal.fire({
		        title: 'Action Cancelled!',
		        text: "",
		        icon: 'error',
		        confirmButtonColor: '#3085d6',
		        confirmButtonText: 'Close'
		      });
		    }
		  });
		});
	</script>

	{{-- <script>


	</script> --}}
	{{-- <script src="{{ asset('js/jquery.min.js') }}"></script>
	<script>
		$.ajax({
			type: "GET",
			url: "{{ config('app.root_domain') . config('app.user_details_slug') . \Crypt::encryptString(Auth::user()->id) }}",
			dataType: 'json',
			success: function(response){
				document.getElementById('fullname').innerHTML = response['first_name'] + " " + response['last_name'];
				document.getElementById('email').innerHTML = response['email'];
			}
		});
	</script> --}}
@endsection



