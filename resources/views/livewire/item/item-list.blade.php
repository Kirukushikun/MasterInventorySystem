<div class="mims-table-card">
    <div class="mims-table-toolbar">
        <div>
            <h3 class="mims-table-title">Item List</h3>
            <p class="mims-table-subtitle">Manage inventory items — checkout, update, delete, and view details.</p>
        </div>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportData()">
            <i class="fal fa-print"></i> Export CSV
        </button>
    </div>
    <div class="mims-table-wrap">
        <table id="items_table" class="table table-hover mims-table">
            <thead>
                <tr>
                    <th>ITEM NAME</th>
                    <th>TOTAL QTY ON-HAND</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($itemData as $al)
                    <tr>
                        <td>{{ $al->itemName->item_name }}</td>
                        <td>{{ $al->quantity }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    @if ($canCheckout)
                                        @if ($al->quantity < 1)
                                            <a href="#" class="dropdown-item pogi-sige-na">
                                                <i class="fas fa-shopping-cart text-accent mr-2"></i> Checkout
                                            </a>
                                        @else
                                            <a href="{{ route('item.div.checkout', ['id' => Crypt::encryptString($al->id)]) }}" class="dropdown-item">
                                                <i class="fas fa-shopping-cart text-accent mr-2"></i> Checkout
                                            </a>
                                        @endif
                                    @endif
                                    @if ($canEdit)
                                        <a href="{{ route('item.div.show', ['id' => Crypt::encryptString($al->id)]) }}" class="dropdown-item">
                                            <i class="fas fa-edit text-green mr-2"></i> Update
                                        </a>
                                    @endif
                                    @if ($canViewDetails)
                                        <a href="{{ route('item.div.details', ['id' => Crypt::encryptString($al->id)]) }}" class="dropdown-item">
                                            <i class="fas fa-info text-orange mr-2"></i> Details
                                        </a>
                                    @endif
                                    @if ($canDelete)
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('delete.item', ['type' => 'Item', 'id' => Crypt::encryptString($al->id)]) }}" class="dropdown-item" style="color: var(--red) !important;">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td>
                        <a id="buttonToShow" href="#" class="btn btn-primary btn-sm" style="display:none;">
                            <i class="fas fa-shopping-cart"></i> MULTIPLE CHECKOUT
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="mt-3 px-3 pb-3">
            {{ $itemData->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        function initTable() {
            if ($.fn.DataTable.isDataTable('#items_table')) {
                $('#items_table').DataTable().destroy();
            }
            $('#items_table').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
            });
        }

        initTable();

        Livewire.on('refreshDataTable', () => {
            setTimeout(initTable, 50);
        });
    });

    $(document).on('click', '.pogi-sige-na', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Checkout Error!',
            text: `{{ $alertPhrase }}`,
            icon: 'error',
            confirmButtonText: 'Okay'
        }).then(result => { if (result.isConfirmed) location.reload(); });
    });

    @if(isset($_GET["ids"]))
        window.location.href = "{{ route('item.div.multiple.checkout', ['ids' => \Crypt::encryptString($_GET["ids"])]) }}";
    @endif
    @if(isset($_GET["idss"]) && isset($_GET["reqid"]) && isset($_GET["reqqty"]) && isset($_GET["requestid"]))
        window.location.href = "{{ route('item.div.multiple.checkout', ['ids' => \Crypt::encryptString($_GET["idss"])]) }}?reqqty={{ $_GET["reqqty"] }}&reqid={{ $_GET["reqid"] }}&requestid={{ $_GET["requestid"] }}&checkout={{ $_GET["checkout"] }}";
    @endif

    @if (session()->has('success'))
        Swal.fire('Success!', '{{ session('success') }}', 'success');
    @elseif(session()->has('failed'))
        Swal.fire('Failed!', '{{ session('failed') }}', 'error');
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
            Swal.fire({ title: 'Deleted!', text: '{{ $_GET["cat"] }} Succesfully Deleted!', icon: 'success' })
                .then(() => { window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; });
        </script>
    @else
        <script>
            Swal.fire({ title: 'Delete Failed!', html: '{{ $_GET["cat"] }} Parent Database Dependency!<br>Please Remove Child Database Dependency First', icon: 'error' })
                .then(() => { window.location.href = "{{ $currentRoute->uriWithoutQuery }}"; });
        </script>
    @endif
@endif

<script>
    function exportData() {
        var table = document.getElementById('items_table');
        var rows = [];
        for (var i = 0, row; (row = table.rows[i]); i++) {
            var rowData = [];
            for (var j = 1; j < row.cells.length - 1; j++) {
                rowData.push('"' + row.cells[j].innerText + '"');
            }
            rows.push(rowData);
        }
        var csvContent = 'data:text/csv;charset=utf-8,';
        rows.forEach(function (rowArray) { csvContent += rowArray.join(',') + '\r\n'; });
        var d = new Date();
        var fileName = 'Main_Inventory_Report_' + d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate() + '.csv';
        var link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
    }
</script>
@endpush
