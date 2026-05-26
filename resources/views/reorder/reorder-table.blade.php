@extends('layouts.app')

@section('title')
	{{ __('Item List - Re-Order') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Item List - Re-Order') }}
					</h4>
				</div>
				<div class="content">
					<hr>
					@livewire('reorder.reorder-list')
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scriptss')
	<script>
        // document.addEventListener('livewire:load', function () {
	    //     $('#reorder_table').DataTable();
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
    {{-- @php
        $currentRoute = request()->route();
        $currentUrlWithoutQuery = strtok(url()->full(), '?');
        $currentRoute->uriWithoutQuery = $currentUrlWithoutQuery;
    @endphp --}}
    {{-- @if(isset($_GET["del"]))
        <script>
            Swal.fire({
                title: 'Deleted!',
                text: '{{ $_GET["cat"] }} Succesfully Deleted!',
                icon: 'success'
            }).then(() => {
                window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
            });
        </script>
    @endif --}}
@endsection


