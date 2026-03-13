@if(config('app.env') == 'production')
	<script  type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@else
	<script type="text/javascript"  src="{{ asset('js/jquery.min.js') }}"></script>
@endif
