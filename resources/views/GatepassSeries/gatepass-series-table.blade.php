@extends('layouts.app')

@section('title')
	{{ __('Gate Pass Series List') }}
@endsection

@section('content')
@php
	$gatepassSeriess = \DB::table('gatepass_series')->orderBy('to', 'DESC')->first();
	$y = 0;

	if ($gatepassSeriess !== null) {
	    $y = (int) $gatepassSeriess->to;
	} else {
	    // Handle the case when no records were found
	    $y = 0; // Or any other default value you prefer
	}
@endphp
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('ASSIGNED LIST(GATE PASS SERIES)') }}
						<a href="{{ route('gatepass.series') }}" class="btn btn-primary text-primary">
		          <i class="fas fa-plus"></i> ADD
		        </a> {{-- | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
					</h4>
				</div>
				<div class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Filter: </h4>

                        </div>
                    </div>
                    <div class="row">
                        <label for="series_from" class="col-md-1 col-form-label text-right">Series From: </label>
                        <div class="col-md-2">
                            <input type="number" id="series_from" name="series_from" class="form-control text-center" placeholder="-- Series From --" oninput="this.value = this.value.replace(/[^0-9]/g, '')" {{ $y == 0 ? 'disabled' : '' }}>
                        </div>
                        <label for="series_to" class="col-md-1 col-form-label text-right">Series To: </label>
                        <div class="col-md-2">
                            <input type="number" id="series_to" name="series_to" class="form-control text-center" placeholder="-- Series To --" oninput="this.value = this.value.replace(/[^0-9]/g, '')" {{ $y == 0 ? 'disabled' : '' }}>
                        </div>
                        <div class="col-md-2">
                            <select id="series_location" class="form-control text-center" id="status" {{ $y == 0 ? 'disabled' : '' }}>
                                <option class="text-center" hidden selected value="">-- Select Farm Location --</option>
                                @foreach($farm_location_list as $fll)
                                    <option class="text-center" value="{{ $fll->id }}">{{ $fll->farm_location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="series_department" class="form-control text-center" id="status" {{ $y == 0 ? 'disabled' : '' }}>
                                <option class="text-center" hidden selected value="">-- Select Department/Division --</option>
                                @foreach($department_division_list as $ddl)
                                    <option class="text-center" value="{{ $ddl->id }}">{{ $ddl->department_division }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="clear_filter" class="btn btn-warning" {{ $y == 0 ? 'disabled' : '' }}>REMOVE FILTER</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="assigned_series" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>GATE PASS SERIES</th>
                                            <th>LOCATION</th>
                                            <th>DEPARTMENT/DIVISION</th>
                                            <th>DATE ASSIGNED</th>
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
		let assigned_series = null;
    $(document).ready(function () {
      assigned_series = jQuery('#assigned_series').DataTable({
        processing: true,
        serverSide: false,
        scrollX: true,
        columnDefs: [
          { targets: [ 0, 1, 2, 3, 4, 5 ] }
        ],

        ajax: {
          url: "{{ route('gatepass.series.list') }}",
          type: "GET",
          dataSrc: function (json) {
          	return json.data;
          }
        },

        columns: [
          {data: 'id', name: 'id'},
          {data: 'series', name: 'series'},
          {data: 'location', name: 'location'},
          {data: 'department_division', name: 'department_division'},
          {data: 'date_assigned', name: 'date_assigned'},
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
      assigned_series = $('#assigned_series').DataTable();
      $('#series_from').val('');
	    $('#series_to').val('');
	    $('#series_location').val('');
	    $('#series_department').val('');
      var nurl = "/gatepass/series/list/all/gs/rf";
      assigned_series.ajax.url(nurl).load();

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

    let series_from = '';
    let series_to = '';
    let series_location = '';
    let series_department = '';

    $('#series_from').val('');
    $('#series_to').val('');
    $('#series_location').val('');
    $('#series_department').val('');

    let typingTimer;
		const doneTypingInterval = 1700; // in milliseconds

		// Define "series_from" filter
		$('#series_from').on('blur', function () {
		  clearTimeout(typingTimer);
		  typingTimer = setTimeout(fromDoneTyping(parseInt($(this).val())), doneTypingInterval);
		});

		function fromDoneTyping(param_series_from = 0) {
		  series_from = param_series_from;
			$('#series_from').removeClass('is-invalid');
			const x = parseInt(series_from);
			@php
				$gatepassSeriess = \DB::table('gatepass_series')->orderBy('to', 'DESC')->first();
				$y = 0;

				if ($gatepassSeriess !== null) {
				    $y = (int) $gatepassSeriess->to;
				} else {
				    // Handle the case when no records were found
				    $y = 0; // Or any other default value you prefer
				}
			@endphp
			const y = parseInt("{{ $y }}");

			//check if series_from is greater than series to
			if (x > y) {
				//if True then Error in Range
				Swal.fire({
					title: 'Error in Series Range!',
					text: "Cannot Find Seies \"To\", The latest Series \"To\" Record is " + "{{ $y }}",
					icon: 'error',
					timer: 3000,
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Close'
				});

				series_from = null;
				$('#series_from').val('');
			}
			else{
				//else, then Filter
				if(series_from == null || series_from == '') {

					// if ((series_from != null || series_from != '') || (series_department != null || series_department != '') || (series_location != null || series_location != '')){
					// 	newurl = "/gatepass/series/list/all/gs/" + series_from + "/0/" + series_location + "/" + series_department;
					// 	assigned_series.ajax.url(newurl).load();
					// }
					$('#series_to').val('');
					$('#series_location').val('');
					$('#series_department').val('');
					series_from = null;
					series_to = null;
					series_location = null;
					series_department = null;
					var nurl = "/gatepass/series/list/all/gs";
					assigned_series.ajax.url(nurl).load();
					console.log(1)
				}
				else if(series_to != null && series_from != null && series_from <= series_to) {
					if((series_location != null || series_location != '') && (series_department != null || series_department != '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location + "/" + series_department;
						assigned_series.ajax.url(newurl).load();
					}
					else if((series_location != null || series_location != '') && (series_department == null || series_department == '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location;
						assigned_series.ajax.url(newurl).load();
					}
					else if((series_location == null || series_location == '') && (series_department != null || series_department != '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/null/" + series_department;
						assigned_series.ajax.url(newurl).load();
					}
				}
				{{--
				else if(series_to != '' && series_from > series_to) {
					Swal.fire({
						title: 'Error in Time Range!',
						text: "Please check From and To Series",
						icon: 'error',
						timer: 3000,
						showCancelButton: false,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Close'
					});

					series_from = null;
					$('#series_from').val('');
				}
				--}}
			}
		}

		$('#series_to').on('blur', function () {
		  clearTimeout(typingTimer);
		  typingTimer = setTimeout(toDoneTyping(parseInt($(this).val())), doneTypingInterval);
		});

		// Define "series_from" filter
		function toDoneTyping(param_series_to = 0) {
			series_to = param_series_to;
			// if(series_from == null || series_from == '') {

			// 	if ((series_from != null || series_from != '') || (series_department != null || series_department != '') || (series_location != null || series_location != '')){
			// 		newurl = "/gatepass/series/list/all/gs/" + series_from + "/0/" + series_location + "/" + series_department;
			// 		assigned_series.ajax.url(newurl).load();
			// 	}
			// }
			if(($('#series_to').val() == null || $('#series_to').val() == '' || $('#series_to').val() == 0) && ($('#series_from').val() == null || $('#series_from').val() == '' || $('#series_from').val() == 0)) {
				Swal.fire({
					title: 'Error On Input!',
					text: "Please Select Series From and Series To First.",
					icon: 'error',
					timer: 3000,
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Close'
				});
			}
			else if(series_from != null && series_from > series_to) {

				Swal.fire({
					title: 'Error in Series Range!',
					html: "<p>Please Check Series From an To.<br>Series From Must Be Less Than The Series To.</p>",
					icon: 'error',
					timer: 3000,
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Close'
				});

				series_to = null;
				$('#series_to').val('');
				series_from = null;
				$('#series_from').val('');
			}
			else if(series_from != null && series_from > series_to) {

				Swal.fire({
					title: 'Error in Series Range!',
					text: "Please Check Series From an To.",
					icon: 'error',
					timer: 3000,
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Close'
				});

				series_to = null;
				$('#series_to').val('');
			}
			else {
				if(series_location == null && series_department == null) {
					newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to;
					assigned_series.ajax.url(newurl).load();
				}
				else {
					if((series_location != null || series_location != '') && (series_department != null || series_department != '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location + "/" + series_department;
						assigned_series.ajax.url(newurl).load();
					}
					else if((series_location != null || series_location != '') && (series_department == null || series_department == '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location + "/0";
						assigned_series.ajax.url(newurl).load();
					}
					else if((series_location == null || series_location == '') && (series_department != null || series_department != '')) {
						newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/0/" + series_department;
						assigned_series.ajax.url(newurl).load();
					}
				}
			}
		}

		// Define "series_location" filter
		$('#series_location').change(function() {
			series_location = $(this).val();

			if((isNaN(series_from) && isNaN(series_to)) || isNaN(series_from) || isNaN(series_to))
			{
				series_from = '';
				series_to = '';
			}

			if ((series_from == null || series_from == '') && (series_to == null || series_to == ''))
			{
				newurl = "/gatepass/series/list/location/" + series_location;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl)
			}
			else if ((series_from == null || series_from == '') && (series_to == null || series_to == '') && (series_department != '' || series_department != null))
			{
				newurl = "/gatepass/series/list/all/gs/0/0/" + series_location + "/" + series_department;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl)
			}
			else if ((series_from != null || series_from != '') && (series_to != null || series_to != '') && (series_department != '' || series_department != null))
			{
				newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location + "/" + series_department;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl + " 214")
			}
		});

		// Define "series_department" filter
		$('#series_department').change(function() {
			series_department = $(this).val();

			if((isNaN(series_from) && isNaN(series_to)) || isNaN(series_from) || isNaN(series_to))
			{
				series_from = '';
				series_to = '';
			}

			if ((series_from == null || series_from == '') && (series_to == null || series_to == ''))
			{
				newurl = "/gatepass/series/list/department/" + series_department;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl)
			}
			else if ((series_from == null || series_from == '') && (series_to == null || series_to == '') && (series_location != '' || series_location != null))
			{
				newurl = "/gatepass/series/list/all/gs/0/0/" + series_location + "/" + series_department;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl)
			}
			else if ((series_from != null || series_from != '') && (series_to != null || series_to != '') && (series_location != '' || series_location != null))
			{
				newurl = "/gatepass/series/list/all/gs/" + series_from + "/" + series_to + "/" + series_location + "/" + series_department;
				assigned_series.ajax.url(newurl).load();
				console.log(newurl + " 214")
			}

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
		      $('#series_from').val('');
		      $('#series_to').val('');
		      $('#series_location').val('');
		      $('#series_department').val('');
		      series_from = null;
		      series_to = null;
		      series_location = null;
		      series_department = null;
		      var nurl = "/gatepass/series/list/all/gs/rf";
		      assigned_series.ajax.url(nurl).load();
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
@endsection


