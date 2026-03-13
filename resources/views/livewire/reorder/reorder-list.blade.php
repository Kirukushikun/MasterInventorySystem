<div class="row">
    <div class="col-md-12">
        <div class="" style="width: 100%;">
            <table id="reorder_table" class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Image</th>
                        <th class="text-center">Item Name</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Sub Category</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Bin Location</th>
                        <th class="text-center">Re-Order</th>
                        <th class="text-center">Quantity On Hand</th>
                        {{-- <th class="text-center">Excess</th>
                        <th class="text-center">Percentage</th> --}}
                        <th class="text-center">Status</th>
                        <th class="text-center">Status Bar</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i)
                        @php $al = $i['model']; @endphp

                        <tr>
                            <td>{{ $al->id }}</td>

                            <td>
                                <img src="{{ asset('photos/'.$al->item_image) }}"
                                    class="img-thumbnail imageZoomButton"
                                    data-imageurl="{{ asset('photos/'.$al->item_image) }}"
                                    style="width: 80px; height: 80px;">
                            </td>

                            <td>{{ $al->itemName->item_name }}</td>
                            <td>{{ $al->category->category_name }}</td>
                            <td>{{ $al->subcategory->subcategory_name }}</td>
                            <td>{{ $al->product->product_name }}</td>
                            <td>{{ $al->location->location_name }}</td>

                            <td>
                                <span class="badge badge-info">{{ $al->reorder_threshold }}</span>
                            </td>

                            <td>
                                <span class="badge {{ $i['badge'] }}">{{ $al->quantity }}</span>
                            </td>

                            <td>
                                <span class="badge {{ $i['badge'] }}">{{ $i['status'] }}</span>
                            </td>

                            <td style="width:200px;">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $i['bg'] }}"
                                        style="width: {{ $i['perc'] }}%">
                                        {{ $i['perc'] }}%
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if (\App\Http\Controllers\AccessController::checkAccess(Auth::id(), 'reorder_edit'))
                                    <a href="{{ route('reorder.div.show', ['id' => Crypt::encryptString($al->id)]) }}"
                                    class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Set Re-Order
                                    </a>
                                @else
                                    <button class="btn btn-info btn-sm" disabled>N/A</button>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

            <!-- PAGINATION -->
            <div class="mt-3">
                {{ $items->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        const imageButtons = document.querySelectorAll('.imageZoomButton');
        imageButtons.forEach(button => {
            button.addEventListener('click', () => {
                const imageUrl = button.getAttribute('data-imageurl');
                Swal.fire({
                    imageUrl: imageUrl,
                    imageAlt: 'Image',
                    showConfirmButton: true,
                    customClass: {
                        image: 'zoomable-image',
                    },
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    imageWidth: 500, // Set the width here (e.g., 400px)
                    imageHeight: 500, // Set the height here (e.g., 300px)
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            Livewire.hook('element.updated', (el, component) => {
                if (el.id === 'reorder_table') {
                    $(el).DataTable().destroy(); // Destroy DataTable
                     $('#reorder_table').DataTable(); // Reinitialize DataTable
                    console.log('Updated DataTable: ' + el.id);
                }
            });
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


