{{-- <button class="btn btn-outline-primary" onclick="exportData()"><i class="fal fa-print"></i> GENERATE REPORT</button> --}}
<hr>
<div class="row">
    @inject('itemlist', 'App\Models\ItemList')
    @inject('itemname', 'App\Models\ItemName')
    @inject('itm', 'App\Models\Item')
    @inject('prd', 'App\Models\Product')
    @inject('uom', 'App\Models\UnitOfMeasurement')
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="items_table" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID.</th>
                        <th>SERIES NO.</th>
                        <th>REQUESTED BY</th>
                        <th>STATUS</th>
                        <th>ITEM REQUESTED</th>
                        <th>DEPARTMENT</th>
                        <th>FARM LOCATION</th>
                        <th>DATE REQUESTED</th>
                        <th>DATE NEEDED</th>
                        <th>COMMENTS/NOTES</th>
                        <th>ATTACHMENT</th>
                        <th class="text-nowrap">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $fal)
                        <tr>
                            <td>{{ $fal['id'] }}</td>
                            <td>{{ $fal['series_number'] }}</td>
                            <td>{{ $fal['requested_by'] }}</td>
                            <td>{!! $fal['status'] !!}</td>
                            <td>{!! $fal['items_requested'] !!}</td>
                            <td>{{ $fal['department_division'] }}</td>
                            <td>{{ $fal['location'] }}</td>
                            <td>{{ $fal['date_requested'] }}</td>
                            <td>{{ $fal['date_needed'] }}</td>
                            <td>{!! $fal['comment'] !!}</td>
                            <td>{!! $fal['attachment'] !!}</td>
                            <td wire:defer class="text-nowrap">{!! $fal['action'] !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- <div class="col-md-12" style="display: none;">
        <div class="table-responsive">
            <table id="items_table_2" class="table table-hover" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID.</th>
                        <th>SERIES NO.</th>
                        <th>REQUESTED BY</th>
                        <th>LOCATION</th>
                        <th>DEPARTMENT</th>
                        <th>STATUS</th>
                        <th>ITEM NAME</th>
                        <th>CATEGORY</th>
                        <th>SUBCATEGORY</th>
                        <th>PRODUCT</th>
                        <th>UOM</th>
                        <th>QUANTITY REQUESTED</th>
                        <th>DATE REQUESTED</th>
                        <th>DATE NEEDED</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data2 as $fal2)
                        <tr>
                            <td>{{ $fal2['id'] }}</td>
                            <td>{{ $fal2['series_number'] }}</td>
                            <td>{{ $fal2['requested_by'] }}</td>
                            <td>{{ $fal2['location'] }}</td>
                            <td>{{ $fal2['department_division'] }}</td>
                            <td>{!! $fal2['status'] !!}</td>
                            <td>{{ $fal2['item_name'] }}</td>
                            <td>{{ $fal2['category'] }}</td>
                            <td>{{ $fal2['subcategory'] }}</td>
                            <td>{{ $fal2['product'] }}</td>
                            <td>{{ $fal2['uom'] }}</td>
                            <td>{{ $fal2['quantiy'] }}</td>
                            <td>{{ $fal2['date_requested'] }}</td>
                            <td>{{ $fal2['date_needed'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}

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
        $(document).ready(function() {
            @if($this->ris1 != '' || !empty($this->ris1) || $this->ris1 != null || !is_null($this->ris1))
                @foreach($this->ris1 as $key => $ris1)
                    $("#showNotif{{ $ris1 }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to approve this request?",
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
                                Livewire.emit('approved', valueToSend); // emit an event with the value
                            }
                        });
                    });
                    $('#checkout-btn{{ $ris1 }}').click(function(event) {
                        event.preventDefault(); // Prevent the default behavior of following the link

                        // Use SweetAlert to display a confirmation dialog
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'Do you want to checkout this item?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Checkout',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                    Swal.fire({
                                        title: 'How do you want to Checkout?',
                                        icon: 'question',
                                        showDenyButton: true,
                                        showCancelButton: true,
                                        confirmButtonColor: '#223a5e', // Checkout In Full
                                        denyButtonColor: '#fe840e',    // Checkout Partially
                                        cancelButtonColor: '#17a2b8',  // Check Farm Availability
                                        confirmButtonText: 'Checkout In Full',
                                        denyButtonText: 'Checkout Partially',
                                        cancelButtonText: 'Check Other Farms',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = $('#checkout-btn{{ $ris1 }}').attr('href') + '&checkout=full';
                                        } else if (result.isDenied) {
                                            window.location.href = $('#checkout-btn{{ $ris1 }}').attr('href') + '&checkout=partial';
                                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                                            // Redirect to your farm stock checking page with the request ID
                                            window.location.href = '{{ route("farm.stock.check", ["id" => encrypt(intval($ris1))]) }}';
                                        }
                                    });

                            }
                        });
                    });
                    $("#showNotifDeny{{ $ris1 }}").click(function(event) {
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
                                Livewire.emit('denied', valueToSend); // emit an event with the value
                            }
                        });
                    });
                    $("#showNotifForRelease{{ $ris1 }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want change the status to For Release?",
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
                                Livewire.emit('for_release', valueToSend); // emit an event with the value
                            }
                        });
                    });
                    $("#showNotifInTransit{{ $ris1 }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission

                        var valueToSend = $(this).data('value');

                        Swal.fire({
                            title: 'Are you sure?',
                            html: "<h4>Do you want change the status to in-transit?<h4><br><h5><span class='text-danger'>Note:</span> This will also print a Gate Pass</h5>",
                            icon: 'question',
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonColor: '#3085d6',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Cancel',
                            cancelButtonText: 'Yes',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {

                                Swal.fire({
                                    title: '',
                                    html: `
                                        <style>
                                            @media print {
                                                .swal2-confirm, .swal2-cancel, .form-container {
                                                    display: none !important;
                                                }
                                                @page {
                                                    size: auto;
                                                    margin: 0;
                                                }
                                            }
                                        </style>
                                        <div class="" style="width: 100%;">
                                            <div class="container-fluid" style="border: 1px solid black;">
                                                <div class="row pt-1">
                                                    <div class="col-sm-12 text-center"><img src="https://brooksidegroup.org/wp-content/uploads/2021/04/BGC-transparent.png" alt="BGC LOGO" style="width: 200px; height: auto;"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <h3>GATE PASS</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 text-left">
                                                        <h6><br>
                                                            UNIT: <u>{{ $dat2[$ris1]['location'] }} ({{ $dat2[$ris1]['abbrvtn'] }})</u>
                                                            <span class="text-danger float-right">NO: {{ str_pad(rand(10000, 99999), 7, '0', STR_PAD_LEFT) }}</span>
                                                        </h6>
                                                    </div>
                                                    <div class="col-sm-12 text-left">
                                                        <h6>DATE: <u>{{ date('m-d-Y') }}</u></h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-bordered dt-center">
                                                            <tbody>
                                                                <tr class="dt-center">
                                                                    <td><b>NO.</b></td>
                                                                    <td><b>IMAGE</b></td>
                                                                    <td><b>ITEM</b></td>
                                                                    <td><b>PRODUCT</b></td>
                                                                    <td><b>U/M</b></td>
                                                                    <td><b>QTY</b></td>
                                                                </tr>
                                                                @foreach ($itemlist::where('request_item_id', $ris1)->where('active_status', 1)->get() as $key => $il)
                                                                    @php
                                                                        $item_name = $itemname::findorfail($il->item_id);
                                                                        if ($il->item_partially_release_quantity > 0) {
                                                                            echo '
                                                                                <tr>
                                                                                    <td>' . ($key + 1) . '</td>
                                                                                    <td><img src="' . asset('photos/' . $itm::findorfail($il->item_id)->item_image) . '" class="img-thumbnail imageZoomButton" style="width: 80px; height: 80px;"></td>
                                                                                    <td>' . strtoupper($item_name->item_name ?? '-') . '</td>
                                                                                    <td>' . strtoupper($prd::findorfail($item_name->product_id)->product_name ?? '-') . '</td>
                                                                                    <td>' . strtoupper($uom::findorfail($il->uom_id)->abbreviation ?? '-') . '</td>
                                                                                    <td>' . ($il->item_partially_release_quantity ?? '-') . '</td>
                                                                                </tr>';
                                                                        }
                                                                    @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 text-left">
                                                        <h6>
                                                            DESTINATION FARM: <u>{{ $dat2[$ris1]['location'] }}</u> &nbsp;
                                                            DEPARTMENT: <u>{{ $dat2[$ris1]['department_division'] }}</u>
                                                        </h6>
                                                    </div>
                                                    <div class="col-sm-12 text-left">
                                                        <h6 style="position: relative;">
                                                            SENDER'S NAME/SIGNATURE: <u>{{ $dat2[$ris1]['sender_name'] }}</u>
                                                            <img src="{{ $dat2[$ris1]['signature'] }}" alt="E-Signature" style="max-width: 60px; max-height: 70px; position: absolute; top: -10px; left: 200px;">
                                                        </h6>
                                                    </div>
                                                    <div class="col-sm-12 text-left">
                                                        <h6>
                                                            APPROVED BY: <u>Donna Isla</u>
                                                            <img src="{{ $dat2[$ris1]['approvers_signature'] }}" alt="E-Signature" style="max-width: 60px; max-height: 70px; position: absolute; top: -35px; left: 130px; opacity: 0.59;">
                                                        </h6>
                                                    </div>

                                                    <!-- Delivered By input -->
                                                    <div class="col-sm-12 text-left form-container">
                                                        <h6>
                                                            DELIVERED BY: <input type="text" id="inputDeliveredBy" class="form-control d-inline-block" style="width: auto; border: none; border-bottom: 1px solid black;" placeholder="Enter name">
                                                        </h6>
                                                    </div>
                                                    <div class="col-sm-12 text-left">
                                                        <h6 id="deliveredByText" style="display: none;">
                                                            DELIVERED BY: <u id="deliveredByValue"></u>
                                                        </h6>
                                                    </div>

                                                    <!-- Received By -->
                                                    <div class="col-sm-12 text-left">
                                                        <h6>RECEIVED BY: <u>{{ $dat2[$ris1]['requested_by'] }}</u></h6>
                                                    </div>

                                                    <!-- Guard on Duty input -->
                                                    <div class="col-sm-12 text-left form-container">
                                                        <h6>
                                                            GUARD ON DUTY: <input type="text" id="inputGuardOnDuty" class="form-control d-inline-block" style="width: auto; border: none; border-bottom: 1px solid black;" placeholder="Enter name">
                                                        </h6>
                                                    </div>
                                                    <div class="col-sm-12 text-left">
                                                        <h6 id="guardOnDutyText" style="display: none;">
                                                            GUARD ON DUTY: <u id="guardOnDutyValue"></u>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `,
                                    showCancelButton: true,
                                    confirmButtonText: 'Print',
                                    cancelButtonText: 'Close',
                                    width: '770px',
                                    didOpen: () => {
                                        // Optional actions when modal opens
                                    },
                                    preConfirm: () => {
                                        // Replace inputs with plain text before printing
                                        const deliveredBy = document.getElementById("inputDeliveredBy").value;
                                        const guardOnDuty = document.getElementById("inputGuardOnDuty").value;

                                        document.getElementById("deliveredByValue").textContent = deliveredBy;
                                        document.getElementById("guardOnDutyValue").textContent = guardOnDuty;

                                        document.querySelectorAll('.form-container').forEach(el => el.style.display = 'none');
                                        document.getElementById("deliveredByText").style.display = 'block';
                                        document.getElementById("guardOnDutyText").style.display = 'block';

                                        // Print and emit event
                                        window.print();
                                        Livewire.emit('in_transit', valueToSend);
                                    }
                                });

                            }
                        });
                    });

                    $("#showNotifReceived{{ $ris1 }}").click(function(event) {
                        event.preventDefault(); // Prevent default form submission behavior

                        var valueToSend = $(this).data('value'); // Get the value from the data attribute
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want change the status to received?",
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
                                Livewire.emit('received', valueToSend); // emit an event with the value
                            }
                        });
                    });
                @endforeach
            @endif
        });

        // function exportData() {
        //     var table = document.getElementById("items_table_2"); // Change "table-id" to "items_table"

        //     var rows = [];

        //     for (var i = 0, row; (row = table.rows[i]); i++) {
        //         var rowData = [];

        //         for (var j = 1; j < row.cells.length; j++) {
        //             rowData.push('"' + row.cells[j].innerText + '"');
        //         }

        //         rows.push(rowData);
        //     }

        //     var csvContent = "data:text/csv;charset=utf-8,";

        //     rows.forEach(function (rowArray) {
        //         var row = rowArray.join(",");
        //         csvContent += row + "\r\n";
        //     });

        //     var currentDate = new Date();
        //     var dateString =
        //         currentDate.getFullYear() +
        //         "-" +
        //         (currentDate.getMonth() + 1) +
        //         "-" +
        //         currentDate.getDate();

        //     var fileName = "For_Approval_Report_" + dateString + ".csv";

        //     var encodedUri = encodeURI(csvContent);
        //     var link = document.createElement("a");
        //     link.setAttribute("href", encodedUri);
        //     link.setAttribute("download", fileName);
        //     document.body.appendChild(link);
        //     link.click();
        // }

        // document.addEventListener('DOMContentLoaded', function () {
        //     Livewire.hook('element.updated', (el, component) => {
        //         if (el.id === 'items_table') {
        //             $(el).DataTable().destroy(); // Destroy DataTable
        //             $('#items_table').DataTable(); // Reinitialize DataTable
        //             console.log('Updated DataTable: ' + el.id);
        //         }
        //     });
        // });
        // @if (session()->has('success'))

        //   Swal.fire(
        //     'Success!',
        //     '{{ session('success') }}',
        //     'success'
        //   );

        // @elseif(session()->has('failed'))

        //   Swal.fire(
        //     'Failed!',
        //     '{{ session('failed') }}',
        //     'error'
        //   );

        // @endif
    </script>


@endpush


