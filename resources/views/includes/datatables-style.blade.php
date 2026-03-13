@if(config('app.env') == 'production')
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css"/>
@else
	<link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}"/>
@endif
