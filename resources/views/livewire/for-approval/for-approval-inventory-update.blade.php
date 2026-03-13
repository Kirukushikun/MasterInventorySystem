<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="for_update_table" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID.</th>
                        <th>ITEM NAME</th>
                        <th>STATUS</th>
                        <th>CATEGORY</th>
                        <th>SUB CATEGORY</th>
                        <th>PRODUCT</th>
                        <th>U/M</th>
                        <th>CURRENT QUANTITY</th>
                        <th>QUANTITY TO REDUCE</th>
                        <th>DATE</th>
                        <th class="text-nowrap">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $fal)
                        <tr>
                            <td>{{ $fal['id'] }}</td>
                            <td>{{ $fal['item_name'] }}</td>
                            <td>{!! $fal['status'] !!}</td>
                            <td>{{ $fal['category'] }}</td>
                            <td>{{ $fal['subcategory'] }}</td>
                            <td>{{ $fal['product'] }}</td>
                            <td>{{ $fal['uom'] }}</td>
                            <td>{{ $fal['quantity'] }}</td>
                            <td>{{ $fal['quantity_to_remove'] }}</td>
                            <td class="text-nowrap">{{ $fal['date'] }}</td>
                            <td wire:defer class="text-nowrap">{!! $fal['action'] !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@push('scripts')

    <script>
        $(document).ready(function() {
            @if($this->ris != '' || !empty($this->ris) || $this->ris != null || !is_null($this->ris))
                @foreach($this->ris as $key => $ris)
                    $("#showNotifUpdate{{ $ris }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do You Want To Approve This Update?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, I approve!',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                Swal.fire({
                                    title: 'Action Cancelled',
                                    icon: 'error',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: true
                                });
                            } else {
                                Livewire.emit('approve', valueToSend); // emit an event with the value
                            }
                        });
                    });

                    $("#showNotifUpdateDeny{{ $ris }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to deny this request?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                Swal.fire({
                                    title: 'Action Cancelled',
                                    icon: 'error',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: true
                                });
                            } else {
                                Livewire.emit('deny', valueToSend); // emit an event with the value
                            }
                        });
                    });
                @endforeach
            @endif
        });
    </script>
    {{-- <script>
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
    </script> --}}


@endpush


