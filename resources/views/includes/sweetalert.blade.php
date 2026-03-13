@if(config('app.env') == 'production')
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@else
	<script type="text/javascript" src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endif