<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="forapprovals_inv_create_mod" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Image</th>
                        <th class="text-center">Item Name</th>
                        <th class="text-center">Total Quantity On-Hand</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Sub Category</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Bin Location</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">Reorder Threshold</th>
                        <th class="text-center">Last Purchase Cost</th>
                        <th class="text-center">Last Purchase Date</th>
                        <th class="text-center">Last Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                            <tr>

                                <td>{{ $item['id'] }}</td>
                                <td>{!! $item['item_image'] !!}</td>
                                <td>{{ $item['item_name'] }}</td>
                                <td>{{ $item['quantity_on_hand'] }}</td>
                                <td>{{ $item['category'] }}</td>
                                <td>{{ $item['subcategory'] }}</td>
                                <td>{{ $item['product'] }}</td>
                                <td>{{ $item['location'] }}</td>
                                <td>{{ $item['uom'] }}</td>
                                <td>{{ $item['reorder_threshold'] }}</td>
                                <td>{{ $item['purchase_cost'] }}</td>
                                <td>{{ $item['purchase_date'] }}</td>
                                <td>{{ $item['expiry_date'] }}</td>
                                <td>{!! $item['action'] !!}</td>
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
                    $("#showNotifCreateInv{{ $ris }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do You Want To Approve This Additional in Inventory?",
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

                    $("#showNotifCreateInvDeny{{ $ris }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do You Want To Deny This Additional Inventory?",
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


