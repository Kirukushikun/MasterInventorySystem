@if(config('app.env') == 'production')
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.6.0/dt-1.12.1/datatables.min.js"></script>
@else
	<script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
@endif