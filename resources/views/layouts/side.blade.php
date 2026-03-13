<div class="sidebar" data-color="dark">
    <div class="sidebar-wrapper">

        {{-- ── LOGO ── --}}
        <div class="logo">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="
                width:32px;height:32px;
                background:linear-gradient(135deg,#2f81f7,#a371f7);
                border-radius:9px;flex-shrink:0;
                display:flex;align-items:center;justify-content:center;
                font-size:11px;font-weight:700;color:#fff;
                ">BG</div>
                <div style="line-height:1.25;">
                <strong style="display:block;font-size:13px;font-weight:600;color:#e6edf3;">Brookside</strong>
                <span style="font-size:11px;color:#7d8590;font-weight:400;">Group of Companies</span>
                </div>
            </div>
        </div>

        {{-- ── NAV ── --}}
        {{-- @livewire('navigation.side-nav') renders the actual nav items --}}
        {{-- All items will be styled by zen-side.blade.php global CSS --}}
        @livewire('navigation.side-nav')

    </div>
</div>