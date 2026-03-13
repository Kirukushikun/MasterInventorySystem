<div>
    <p>
        {{-- {{ $item_type }} {{ $item_id }} --}}
    </p>
    {{-- {{ $this->deleted_status }} --}}
</div>
@php
    $previousUrl = url()->previous();
@endphp

@push('scripts')
    <script>
        Swal.fire({
            title: 'Delete?',
            html: "<p>Are you sure you want to delete <b>" + "{{ $this->item_name }}" + "</b>?</p>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '{{ $previousUrl }}';
            } else {
                Livewire.emit('deleteData'); // emit an event with the value
                Livewire.on('deletedStatus', (status) => {
                    if (status == 1) {
                        window.location.href = '{{ $previousUrl }}?del=1&cat=' + `{{ $item_type }}`;
                    } else {
                        window.location.href = '{{ $previousUrl }}?del=0&cat=' + `{{ $item_type }}`;
                    }
                    console.log(status); // This should show the updated value
                });
            }
        });

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
@endpush


