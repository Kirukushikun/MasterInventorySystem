<button class="btn btn-outline-primary" onclick="exportData()"><i class="fal fa-print"></i> GENERATE CSV </button>
<hr>
<div class="row">
    <div class="col-12">
        <div class="table-responsive" style="width: 100%; overflow-x: auto;">
            <table id="items_table" class="table table-hover text-nowrap">
                <thead>
                    <tr style="align-items: center;">
                        <th class="text-center">Item Name</th>
                        <th class="text-center">Total Quantity On-Hand</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itemData as $al)
                        <tr>
                            <td>{{ $al->itemName->item_name }}</td>
                            <td>{{ $al->quantity }}</td>
                            <td>

                                @if ($canCheckout)
                                    @if ($al->quantity < 1)
                                        <a href="#" id="showAlert" class="btn btn-primary btn-sm pogi-sige-na">
                                            <i class="fas fa-edit"></i> CHECKOUT
                                        </a>
                                    @else
                                        <a href="{{ route('item.div.checkout', ['id' => Crypt::encryptString($al->id)]) }}"
                                        class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> CHECKOUT
                                        </a>
                                    @endif
                                @endif

                                @if ($canEdit)
                                    <a href="{{ route('item.div.show', ['id' => Crypt::encryptString($al->id)]) }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-edit"></i> UPDATE
                                    </a>
                                @endif

                                @if ($canDelete)
                                    <a href="{{ route('delete.item', ['type' => 'Item', 'id' => Crypt::encryptString($al->id)]) }}"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> DELETE
                                    </a>
                                @endif

                                @if ($canViewDetails)
                                    <a href="{{ route('item.div.details', ['id' => Crypt::encryptString($al->id)]) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-info"></i> DETAILS
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td>
                            <a id="buttonToShow" href="#" class="btn btn-primary btn-sm" style="display: none;">
                                <i class="fas fa-shopping-cart"></i>
                                MULTIPLE CHECKOUT
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- PAGINATION -->
            <div class="mt-3">
                {{ $itemData->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>


        document.addEventListener('livewire:load', function () {
            // init / re-init DataTable but disable its paging so Livewire pagination works
            function initTable() {
                if ($.fn.DataTable.isDataTable('#items_table')) {
                    $('#items_table').DataTable().destroy();
                }

                $('#items_table').DataTable({
                    paging: false,        // <-- disable DataTables pagination
                    searching: false,     // optional: disable searching if Livewire handles it
                    info: false,
                    ordering: false,
                    // other options you need...
                });
            }

            // initial init
            initTable();

            // re-init after Livewire updates (you already emit('refreshDataTable'))
            Livewire.on('refreshDataTable', () => {
                // small delay to ensure DOM updated
                setTimeout(initTable, 50);
            });
        });
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

        @if(isset($_GET["ids"]))
            window.location.href = "{{ route('item.div.multiple.checkout', ['ids' => \Crypt::encryptString($_GET["ids"])]) }}";
        @endif
        @if(isset($_GET["idss"]) && isset($_GET["reqid"]) && isset($_GET["reqqty"]) && isset($_GET["requestid"]))
            window.location.href = "{{ route('item.div.multiple.checkout', ['ids' => \Crypt::encryptString($_GET["idss"])]) }}?reqqty={{ $_GET["reqqty"] }}&reqid={{ $_GET["reqid"] }}&requestid={{ $_GET["requestid"] }}&checkout={{ $_GET["checkout"] }}";
        @endif


        // document.addEventListener('DOMContentLoaded', function () {
        //     Livewire.hook('element.updated', (el, component) => {
        //         if (el.id === 'items_table') {
        //             $(el).DataTable().destroy(); // Destroy DataTable
        //              $('#items_table').DataTable();
        //             console.log('Updated DataTable: ' + el.id);
        //         }
        //     });
        // });


        // const checkboxes = document.querySelectorAll('.my-checkbox');
        // const button = document.getElementById('buttonToShow');

        // checkboxes.forEach(checkbox => {
        //     checkbox.addEventListener('change', updateButtonVisibility);
        // });

        // function updateButtonVisibility() {
        //     const checkedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);

        //     if (checkedCheckboxes.length < 2) {
        //         button.style.display = 'none';
        //     } else if (checkedCheckboxes.length > 1) {
        //         button.style.display = 'block';
        //     } else {
        //         button.style.display = 'none';
        //     }

        //     //Update the selected values array
        //     const selectedValues = checkedCheckboxes.map(checkbox => checkbox.value);
        //     const encodedSelectedValues = encodeURIComponent(JSON.stringify(selectedValues));
        //     const linkHref = '{{ route('item.list') }}?ids=[' + selectedValues + ']';
        //     button.href = linkHref;
        // }


        const showAlertButtons = document.querySelectorAll('.pogi-sige-na');

        showAlertButtons.forEach(showAlertButton => {
            showAlertButton.addEventListener('click', pogiSigeNa);
        });


        function pogiSigeNa(){
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
         @if($_GET["del"] == 1)
            <script>
                Swal.fire({
                    title: 'Deleted!',
                    text: '{{ $_GET["cat"] }} Succesfully Deleted!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
                });
            </script>
        @else
            <script>
                Swal.fire({
                    title: 'Delete Failed!',
                    html: '{{ $_GET["cat"] }} Parent Database Dependency!<br>Please Remove Child Database Dependency First',
                    icon: 'error'
                }).then(() => {
                    window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; // Replace with your actual route name
                });
            </script>
        @endif
    @endif
    <script type="text/javascript">
        function exportData() {
            var table = document.getElementById("items_table"); // Change "table-id" to "items_table"

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

            var fileName = "Main_Inventory_Report_" + dateString + ".csv";

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", fileName);
            document.body.appendChild(link);
            link.click();
        }
    </script>
@endpush


