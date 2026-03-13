@extends('layouts.app')

@section('title')
	{{ __('Item List') }}
@endsection

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="header">
                        <h4 class="title">
                            {{ __('Item List') }}
                            <a href="{{ route('item.div') }}" class="btn btn-primary text-primary">
                            <i class="fas fa-plus"></i> ADD
                            </a>
                            {{-- | <button id="refresh" class="btn btn-link text-decoration-none"><i class="fa fa-sync"></i> REFRESH</button> --}}
                        </h4>
                    </div>
                    <div class="content">
                        <hr>
                        @livewire('item.item-list')
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

        // $(document).ready(function() {
		//     var table = $('#items_table').DataTable({
		//         "order": [[1, 'asc']], // Set default sorting by the ID column
		//         "dom": 'lfrtip', // Customize the table layout if needed
		//     });

		//     // Apply column-specific search filtering
		//     table.columns().every(function(index) {
		//         if (index !== 0 && index !== 1 && index !== 2 && index !== table.columns().indexes().length - 1 && index !== table.columns().indexes().length - 2) {
		//             var column = this;

		//             // Create the input element for filtering
		//             var input = $('<input type="text" style="height: 17px; width: 200px;" placeholder="Search" class="form-control form-control-sm">')
		//                 .appendTo($(column.header()))
		//                 .on('keyup change', function() {
		//                     column.search($(this).val(), false, false, true).draw();
		//                 });
		//         }
		//     });

		//     // Initialize FixedColumns extension
		//     new $.fn.dataTable.FixedColumns(table, {
		//         rightColumns: 1 // Number of columns to fix on the right
		//     });
		// });

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



