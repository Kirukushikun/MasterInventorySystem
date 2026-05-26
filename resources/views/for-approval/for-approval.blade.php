@extends('layouts.app')

@section('title')
	{{ __('For Approval List') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Manage Requests') }}
					</h4>
				</div>
				<div class="content">
					<hr>
					@livewire('for-approval.for-approval-list')
				</div>
			</div>
		</div>
		@inject('acc', '\App\Http\Controllers\AccessController')
		@if($acc::checkAccess(Auth::id(), 'forapprovals_update_mod'))
			<div class="row">
				<div class="col-md-12">
					<div class="header">
						<h4 class="title">
							{{ __('For Approvals (Farm Level Inventory Update)') }}
						</h4>
					</div>
					<div class="content">
						<hr>
						@livewire('for-approval.for-approval-inventory-update')
					</div>
				</div>
			</div>
		@endif
        @if($acc::checkAccess(Auth::id(), 'forapprovals_inv_create_mod'))
			<div class="row">
				<div class="col-md-12">
					<div class="header">
						<h4 class="title">
							{{ __('For Approvals (Inventory Creation/Additions)') }}
						</h4>
					</div>
					<div class="content">
						<hr>
						@livewire('for-approval.for-approval-inv-create')
					</div>
				</div>
			</div>
		@endif
	</div>
</div>
@endsection

@section('scriptss')
	<script>
		@if (session()->has('success'))
			Swal.fire('Success!', '{{ session('success') }}', 'success');
		@elseif(session()->has('failed'))
			Swal.fire('Failed!', '{{ session('failed') }}', 'error');
		@endif
	</script>
@endsection



