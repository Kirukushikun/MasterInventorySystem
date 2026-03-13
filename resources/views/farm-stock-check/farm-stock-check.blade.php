@extends('layouts.app')

@section('title')
	{{ __('Check Farm Stock') }}
@endsection

@section('content')
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="header">
					<h4 class="title">
						{{ __('Check Farm Stock') }}  <i class="fas fa-inventory"></i>
					</h4>
				</div>
				<div class="content">
					<hr>
					@livewire('for-approval.farm-stock-check', ['id' => $farm_stock_check_id])
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
