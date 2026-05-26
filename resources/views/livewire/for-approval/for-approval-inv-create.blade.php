<div class="mims-table-card">
    <div class="mims-table-toolbar">
        <div>
            <h3 class="mims-table-title">Inventory Creation / Additions</h3>
            <p class="mims-table-subtitle">Review and approve or deny pending new inventory creation requests.</p>
        </div>
    </div>
    <div>
        <table id="forapprovals_inv_create_mod" class="table table-hover mims-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IMAGE</th>
                    <th>ITEM NAME</th>
                    <th>TOTAL QTY ON-HAND</th>
                    <th>CATEGORY</th>
                    <th>SUB CATEGORY</th>
                    <th>PRODUCT</th>
                    <th>BIN LOCATION</th>
                    <th>UOM</th>
                    <th>REORDER THRESHOLD</th>
                    <th>LAST PURCHASE COST</th>
                    <th>LAST PURCHASE DATE</th>
                    <th>LAST EXPIRY DATE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
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
                        <td wire:defer>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-2" style="min-width:0;">
                                    <div class="d-flex flex-column" style="gap:4px;">
                                        {!! $item['action'] !!}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('#forapprovals_inv_create_mod').DataTable({
            scrollX: true,
            language: { emptyTable: 'No pending inventory creation requests.' },
        });
    });

    $(document).on('click', '[id^="showNotifCreateInv"]:not([id*="Deny"])', function (e) {
        e.preventDefault();
        const val = $(this).data('value');
        Swal.fire({
            title: 'Are you sure?', text: 'Do You Want To Approve This Additional in Inventory?',
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I approve!', allowOutsideClick: false, allowEscapeKey: false
        }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('approve', val));
    });

    $(document).on('click', '[id^="showNotifCreateInvDeny"]', function (e) {
        e.preventDefault();
        const val = $(this).data('value');
        Swal.fire({
            title: 'Are you sure?', text: 'Do You Want To Deny This Additional Inventory?',
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            confirmButtonText: 'Yes', allowOutsideClick: false, allowEscapeKey: false
        }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('deny', val));
    });
</script>
@endpush
