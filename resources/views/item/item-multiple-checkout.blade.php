@extends('layouts.app')

@section('title')
	{{ __('Item Multiple Checkout') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Item Multiple Checkout') }}
						<a href="{{ route('item.list') }}" class="btn btn-primary text-primary">
				          <i class="fas fa-list"></i> SHOW LIST
				        </a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-12">
								{{-- @if($acc::checkAccess(Auth::user()->id, 'boar_sow_add')) --}}
								{{-- <div class="card-header">
								<h3 class="card-title"><i class="fas fa-plus"></i> ADD BOAR/SOW CODE</h3>
								</div> --}}
								@livewire('item.item-multiple-checkout', [
									'ids' => $item_multiple_checkout_ids,
									'reqid' => $_GET['reqid'] ?? 0,
									'reqqty' => $_GET['reqqty'] ?? null,
									'request_id' => $_GET['requestid'] ?? 0,
                                    'checkoutType' => $_GET['checkout'] ?? null
								])
								{{-- @endif --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div
</div>
@endsection
@section('scripts')
	<script>
		// $('#selectUser').select2();
        // $('#selectLocation').select2();
        //
        $('#selectUser').select2();

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



