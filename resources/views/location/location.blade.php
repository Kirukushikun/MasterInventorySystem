@extends('layouts.app')

@section('title')
	{{ __('Bin Location') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Bin Location') }}
						<a href="{{ route('location.list') }}" class="btn btn-primary text-primary">
							<i class="fas fa-list"></i> SHOW LIST
						</a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								@inject('acc', '\App\Http\Controllers\AccessController')
								@if($acc::checkAccess(Auth::id(), 'location_add'))
									<div class="card-header">
										<h5 class="card-title"><i class="fas fa-plus"></i> Create Location</h5>
									</div>
									@livewire('location.location')
								@endif
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



