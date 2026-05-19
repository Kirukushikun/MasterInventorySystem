
<form wire:submit.prevent autocomplete="off">
    @csrf
    <div class="mims-table-card">
        <div class="mims-table-toolbar">
            <div>
                <h3 class="mims-table-title">Farm Inventory</h3>
                <p class="mims-table-subtitle">Monitor farm items, quantities, and assigned locations.</p>
            </div>
            <button class="btn btn-outline-primary" type="button" onclick="exportData()"><i class="fal fa-print"></i> Generate CSV</button>
        </div>
        <div class="mims-table-wrap">
            <table id="farmitems_table" class="table table-hover mims-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Quantity On-Hand</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="td-num">{{ str_pad($item['number'], 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $item['item_name'] }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $item['quantity'] }}</span>
                            </td>
                            <td>{{ $item['location'] }}</td>
                            <td>
                                <div class="mims-table-actions">
                                    @if($item['can_edit'])
                                        <a href="{{ $item['withdraw_url'] }}" class="btn btn-success btn-sm {{ $item['can_withdraw'] ? '' : 'disabled' }}"><i class="fas fa-edit"></i> Withdraw</a>
                                    @endif
                                    @if($item['can_delete'])
                                        <a href="{{ $item['delete_url'] }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                    @endif
                                    @if($item['can_view_details'])
                                        <a href="{{ $item['details_url'] }}" class="btn btn-warning btn-sm"><i class="fas fa-info"></i> Details</a>
                                    @endif
                                    @if(!$item['can_edit'] && !$item['can_delete'] && !$item['can_view_details'])
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No farm inventory records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

@push('scripts')
    <script>

        // const imageButtons = document.querySelectorAll('.imageZoomButton');
        // imageButtons.forEach(button => {
        //     button.addEventListener('click', () => {
        //         const imageUrl = button.getAttribute('data-imageurl');
        //         Swal.fire({
        //             imageUrl: imageUrl,
        //             imageAlt: 'Image',
        //             showConfirmButton: true,
        //             customClass: {
        //                 image: 'zoomable-image',
        //             },
        //             allowOutsideClick: true,
        //             allowEscapeKey: true,
        //             imageWidth: 500, // Set the width here (e.g., 400px)
        //             imageHeight: 500, // Set the height here (e.g., 300px)
        //         });
        //     });
        // });
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.hook('element.updated', (el, component) => {
                if (el.id === 'farmitems_table') {
                    $(el).DataTable().destroy(); // Destroy DataTable
                     $('#farmitems_table').DataTable(); // Reinitialize DataTable
                    console.log('Updated DataTable: ' + el.id);
                }
            });
        });
        const showAlertButton = document.getElementById('showAlert');

        if (showAlertButton) {
            showAlertButton.addEventListener('click', function(event) {
                // event.preventDefault();

            Swal.fire({
                title: 'Checkout Error!',
                text: `{{ $alertPhrase }}`,
                icon: 'error',
                confirmButtonText: 'Okay'
              }).then((result) => {
                if (result.isConfirmed) {
                  // Reload the page
                  location.reload();
                }
              });
            });
        }

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
    @php
        $currentRoute = request()->route();
        $currentUrlWithoutQuery = strtok(url()->full(), '?');
        $currentRoute->uriWithoutQuery = $currentUrlWithoutQuery;
    @endphp
    @if(isset($_GET["del"]))
        <script>
            Swal.fire({
                title: 'Deleted!',
                text: '{{ $_GET["cat"] }} Succesfully Deleted!',
                icon: 'success'
            }).then(() => {
                window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
            });
        </script>
    @endif
    <script type="text/javascript">
        function exportData() {
            var table = document.getElementById("farmitems_table"); // Change "table-id" to "farmitems_table"

            var rows = [];

            for (var i = 0, row; (row = table.rows[i]); i++) {
                var rowData = [];

                for (var j = 1; j < row.cells.length - 1; j++) {
                    rowData.push('"' + row.cells[j].innerText + '"');
                }

                rows.push(rowData);
            }

            var csvContent = "data:text/csv;charset=utf-8,";

            rows.forEach(function (rowArray) {
                var row = rowArray.join(",");
                csvContent += row + "\r\n";
            });

            var currentDate = new Date();
            var dateString =
                currentDate.getFullYear() +
                "-" +
                (currentDate.getMonth() + 1) +
                "-" +
                currentDate.getDate();

            var fileName = "Farm_Inventory_Report_" + dateString + ".csv";

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", fileName);
            document.body.appendChild(link);
            link.click();
        }
    </script>



@endpush


