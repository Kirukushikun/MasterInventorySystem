
<div class="row">
    <div class="col-12">
        <div class="table-responsive" style="width: 100%; overflow-x: auto;">
            <table id="item_history_table" class="table table-hover text-nowrap">
                <thead>
                    <tr style="align-items: center;">
                        <th class="text-center">No.</th>
                        <th class="text-center">Transaction</th>
                        <th class="text-center">Stock Remaining</th>
                        <th class="text-center">Inputted Quantity</th>
                        {{-- <th class="text-center">Purchase date</th>
                        <th class="text-center">Expiry Date</th> --}}
                        <th class="text-center">Change Reason</th>
                        <th class="text-center">By</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ !in_array($item['transaction_type'], ["ADD", "CREATE", "RENEW"]) ? $item['transaction_type'] : "ADD" }}</td>
                            <td>{{ $item['new_quantity'] ?? 'N/A' }}</td>
                            <td>{{ $item['new_quantity'] - $item['previous_quantity'] ?? 'N/A' }}</td>
                            {{-- <td>{{ $item['new_purchase_date'] ?? 'N/A' }}</td>
                            <td>{{ $item['new_expiry_date'] ?? 'N/A' }}</td> --}}
                            <td>{{ strtoupper($item['reason']) ?? 'N/A' }}</td>
                            <td>{{ strtoupper($item['user']) }}</td>
                            <td>{{ $item['change_date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts_item_history')
    <script>
        $('#item_history_table').DataTable();
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.hook('element.updated', (el, component) => {
                if (el.id === 'item_history_table') {
                    $(el).DataTable().destroy(); // Destroy DataTable
                     $('#item_history_table').DataTable();
                    console.log('Updated DataTable: ' + el.id);
                }
            });
        });
    </script>
@endpush


