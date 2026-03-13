@extends('layouts.app')

@section('title')
	{{ __('Update Re-Order') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Update Re-Order') }}
						<a href="{{ route('reorder.list') }}" class="btn btn-primary text-primary">
				          <i class="fas fa-list"></i> SHOW LIST
				        </a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								@livewire('reorder.reorder-update', ['id' => $reorder_id])
							</div>
						</div>
					</div>
				</div>
			</div>   
		</div>
	</div>
</div>
@endsection
@section('scripts')
	<script>
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



