@if(config('app.env') == 'production')
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@else
	<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap5.min.css') }}"/>
@endif
