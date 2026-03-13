
<form wire:submit.prevent autocomplete="off">
    @csrf
    <button class="btn btn-outline-primary" onclick="exportData()"><i class="fal fa-print"></i> GENERATE CSV </button>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="farmitems_table" class="table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Quantity On-Hand</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {!! $items !!}
                    </tbody>
                </table>
            </div>
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


