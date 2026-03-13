@extends('layouts.app')

@section('title')
	{{ __('Farm Inventory - Farm Item Details') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Farm Inventory - Farm Item Details') }}
						<a href="{{ route('farmitem.list') }}" class="btn btn-primary text-primary">
				          <i class="fas fa-list"></i> SHOW LIST
				        </a>
					</h4>
				</div>

				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								@livewire('farmitem.farmitem-details', ['id' => $farmitem_detail_id])
							</div>
						</div>
					</div>
				</div>
			</div>   
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('User Transactions') }}
					</h4>
				</div>

				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								@livewire('farmitem.farmitem-history', ['id' => $farmitem_detail_id])
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



