<div class="mims-table-card">
    <div class="mims-table-toolbar">
        <div>
            <h3 class="mims-table-title">Farm Level Inventory Update</h3>
            <p class="mims-table-subtitle">Review and approve or deny pending farm inventory update requests.</p>
        </div>
    </div>
    <div>
        <table id="for_update_table" class="table table-hover mims-table">
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
                    <th>ACTION</th>
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
                        <td wire:defer>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="dropdown" aria-expanded="false" title="Actions">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right p-2" style="min-width:0;">
                                    <div class="d-flex flex-column" style="gap:4px;">
                                        {!! $fal['action'] !!}
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
        $('#for_update_table').DataTable({
            scrollX: true,
            lengthChange: false,
            language: { emptyTable: 'No pending inventory update requests.' },
        });
    });

    $(document).on('click', '[id^="showNotifUpdate"]:not([id*="Deny"])', function (e) {
        e.preventDefault();
        const val = $(this).data('value');
        Swal.fire({
            title: 'Are you sure?', text: 'Do You Want To Approve This Update?',
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I approve!', allowOutsideClick: false, allowEscapeKey: false
        }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('approve', val));
    });

    $(document).on('click', '[id^="showNotifUpdateDeny"]', function (e) {
        e.preventDefault();
        const val = $(this).data('value');
        Swal.fire({
            title: 'Are you sure?', text: 'Do you want to deny this request?',
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            confirmButtonText: 'Yes', allowOutsideClick: false, allowEscapeKey: false
        }).then(r => r.dismiss === Swal.DismissReason.cancel ? showCancelled() : Livewire.emit('deny', val));
    });
</script>
@endpush
