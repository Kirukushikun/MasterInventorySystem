@php
    $accessController = app('\App\Http\Controllers\AccessController');
@endphp

@if (
    $accessController::checkAccess(Auth::id(), 'inventory_mod') &&
    $accessController::checkAccess(Auth::id(), 'inventory_add') &&
    $accessController::checkAccess(Auth::id(), 'inventory_edit') &&
    $accessController::checkAccess(Auth::id(), 'inventory_checkout') &&
    $accessController::checkAccess(Auth::id(), 'inventory_details') &&
    $accessController::checkAccess(Auth::id(), 'inventory_multiple') &&
    $accessController::checkAccess(Auth::id(), 'inventory_diminish')
)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('item.list') }}">
            <span>Inventory <i class="fas fa-inventory"></i></span>
        </a>
    </li>
@endif

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell"></i>
        &nbsp;
        @if($ctr != 0)
            <span class="badge badge-danger" style="max-width: 100px; max-height: 18px;">{{ $ctr }}</span>
        @endif
    </a>
    <div class="dropdown-menu">
        @if(empty($notif_items))
            <a class="dropdown-item" href="#">No Items Are Below Or Equal To Their Re-Order Threshold</a>
        @else
            @foreach($notif_items as $ni)
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ $accessController::checkAccess(\Auth::id(), 'reorder_edit') ? route('reorder.div.show', ['id' => \Crypt::encryptString($ni['re_order_id'])]) : route('reorder.list') }}">
                    {!! $ni['item_name'] . ' ' . $ni['stats'] !!}
                    <div class="progress">
                        <div id="inventory-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated {{ $ni['bg'] }}"
                            role="progressbar" style="width: {{ max(0, $ni['perc']) }}%;"
                            aria-valuenow="{{ $ni['quantity_on_hand'] }}" aria-valuemin=""
                            aria-valuemax="{{ $ni['reorder'] * 2 }}">
                            <span style="color: {{ in_array($ni['bg'], ['bg-danger', 'bg-secondary', 'bg-warning']) ? 'black' : 'white' }};">{{ $ni['perc'] }}%</span>
                        </div>
                    </div>
                    On-Hand: <span class="badge {{ $ni['badge'] }}">{{ $ni['quantity_on_hand'] }}</span>
                    Re-Oder: <span class="badge badge-{{ $ni['reorder'] == 0 ? 'dark' : 'info' }}">{{ $ni['reorder'] }}</span>
                </a>
            @endforeach
        @endif
    </div>
</li>
