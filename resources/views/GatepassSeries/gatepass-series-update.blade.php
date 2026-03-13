@extends('layouts.app')

@section('title')
	{{ __('Update Gate Pass Series') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('UPDATE ASSIGNED GATE PASS SERIES') }}
						<a href="{{ route('gatepass.series.list') }}" class="btn btn-primary text-primary">
                            <i class="fas fa-list"></i> SHOW LIST
                        </a>
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								{{-- @if($acc::checkAccess(Auth::user()->id, 'boar_sow_add')) --}}
								{{-- <div class="card-header">
								<h3 class="card-title"><i class="fas fa-plus"></i> ADD BOAR/SOW CODE</h3>
								</div> --}}
								@livewire('gtpss-srs.gatepass-series-update', ['id' => $gate_series_id])
								{{-- @endif --}}
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



