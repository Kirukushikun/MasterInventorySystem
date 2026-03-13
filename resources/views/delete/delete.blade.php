@extends('layouts.app')

@section('title')
	{{ __('Delete') }}
@endsection

@section('content')
	@livewire('delete.delete', ['type' => $item_type, 'id' => $item_id])
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
	</script>
@endsection



