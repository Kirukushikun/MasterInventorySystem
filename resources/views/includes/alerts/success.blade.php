@if(session('success'))
	<div class="alert bg-success text-center top-space">
		<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<b>{{ session('success') }}</b>
	</div>
@endif