@extends('layouts.app')

@section('title')
	{{ __('Transaction Type') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Transaction Type') }}
						<a href="{{ route('transaction.type.list') }}" class="btn btn-primary text-primary">
							<i class="fas fa-list"></i> SHOW LIST
						</a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								@inject('acc', '\App\Http\Controllers\AccessController')
								@if($acc::checkAccess(Auth::id(), 'transtype_add'))
									<div class="card-header">
										<h5 class="card-title"><i class="fas fa-plus"></i> Create Transaction Type</h5>
									</div>
									@livewire('transaction-type.transaction-type')
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



