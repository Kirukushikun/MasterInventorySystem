@extends('layouts.app')

@section('title')
	{{ __('Unit Of Measurements') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Unit Of Measurements') }}
						<a href="{{ route('uom.list') }}" class="btn btn-primary text-primary">
							<i class="fas fa-list"></i> SHOW LIST
						</a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								@inject('acc', '\App\Http\Controllers\AccessController')
								@if($acc::checkAccess(Auth::id(), 'uom_add'))
									<div class="card-header">
										<h5 class="card-title"><i class="fas fa-plus"></i> Add Unit Of Measurement</h5>
									</div>
									@livewire('unit-of-measurement.unit-of-measurement')
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



