@extends('layouts.app')

@section('title')
	Dashboard
@endsection

@section('content')
	@livewire('dashboard')
@endsection

@section('scriptss')
	{{-- <script type="text/javascript">
		$(document).ready(function () {
	      document.querySelectorAll('.form-card-1').forEach(function (input) {
	          input.style.borderRight = '6px solid #007bff';
	      });
	      document.querySelectorAll('.form-card-2').forEach(function (input) {
	          input.style.borderRight = '6px solid #ffc107';
	      });
	      document.querySelectorAll('.form-card-3').forEach(function (input) {
	          input.style.borderRight = '6px solid #dc3545';
	      });
	      document.querySelectorAll('.form-card-4').forEach(function (input) {
	          input.style.borderRight = '6px solid #28a745';
	      });
	    });
	</script> --}}

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



