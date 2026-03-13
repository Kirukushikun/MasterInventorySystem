@extends('layouts.app')

@section('title')
	{{ __('Inventory - Item Details') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Item Details') }}
			      @if(\Auth::check())
							<a href="{{ route('item.list') }}" class="btn btn-primary text-primary">
			          <i class="fas fa-list"></i> SHOW LIST
			        </a>
			      @endif
					</h4>
				</div>
				<div class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								@livewire('item.item-details', ['id' => $item_detail_id])
							</div>
						</div>
					</div>
				</div>
			</div>   
		</div>
    @if(\Auth::check())
			<div class="row">
				<div class="col-md-12">
					<div class="header">
						<h4 class="title">
							{{ __('Item History') }}
						</h4>
					</div>
					<div class="content">
						<div class="container-fluid">
							<div class="row">
								<div class="col-sm-12">
									@livewire('item.item-history', ['id' => $item_detail_id])
								</div>
							</div>
						</div>
					</div>
				</div>   
			</div>
    @endif
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



