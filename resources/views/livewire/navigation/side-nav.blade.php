<div>
    @php
        $grouped = collect($route_title)->groupBy('section');
    @endphp

    @foreach($grouped as $section => $items)
        <ul class="nav zen-nav">
            <li class="zen-nav-label">{{ $section }}</li>

            @foreach($items as $key => $value)
                <li class="zen-nav-item">
                    <a class="zen-nav-link {{ Route::is($value['route']) ? 'active' : '' }}"
                       href="{{ route($value['route']) }}">
                        <i class="zen-nav-icon {{ $value['icon'] }}"></i>
                        <p class="zen-nav-text">{{ $value['title'] }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>