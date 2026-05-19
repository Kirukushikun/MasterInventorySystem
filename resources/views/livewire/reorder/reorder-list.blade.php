<div class="mims-table-card">
    <div class="mims-table-toolbar">
        <div>
            <h3 class="mims-table-title">Re-Order Monitoring</h3>
            <p class="mims-table-subtitle">Review stock thresholds, quantity on hand, and reorder status.</p>
        </div>
    </div>
    <div class="mims-table-wrap">
            <table id="reorder_table" class="table table-hover mims-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Product</th>
                        <th>Bin Location</th>
                        <th>Re-Order</th>
                        <th>Quantity On Hand</th>
                        {{-- <th class="text-center">Excess</th>
                        <th class="text-center">Percentage</th> --}}
                        <th>Status</th>
                        <th>Status Bar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i)
                        @php $al = $i['model']; @endphp

                        <tr>
                            <td class="td-num">{{ str_pad($al->id, 2, '0', STR_PAD_LEFT) }}</td>

                            <td>
                                <img src="{{ asset('photos/'.$al->item_image) }}"
                                    class="mims-table-media imageZoomButton"
                                    data-imageurl="{{ asset('photos/'.$al->item_image) }}"
                                    >
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

                            <td class="mims-table-progress">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $i['bg'] }}"
                                        style="width: {{ $i['perc'] }}%">
                                        {{ $i['perc'] }}%
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="mims-table-actions">
                                    @if (\App\Http\Controllers\AccessController::checkAccess(Auth::id(), 'reorder_edit'))
                                        <a href="{{ route('reorder.div.show', ['id' => Crypt::encryptString($al->id)]) }}"
                                        class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Set Re-Order
                                        </a>
                                    @else
                                        <button class="btn btn-info btn-sm" disabled>N/A</button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
    </div>
    <div class="mims-table-pagination">
        {{ $items->links('pagination::bootstrap-4') }}
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


