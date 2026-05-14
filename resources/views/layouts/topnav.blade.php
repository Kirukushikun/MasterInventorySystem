<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">

    {{-- ── LEFT: PAGE TITLE ── --}}
    <div class="navbar-brand">
      <a href="{{ route('dash') }}" style="color:#e6edf3;text-decoration:none;">
        MIMS
      </a>
      <span style="color:#30363d;margin:0 8px;">—</span>
      <span style="font-weight:400;color:#7d8590;font-size:14px;">@yield('title')</span>
    </div>

    {{-- ── MOBILE TOGGLE ── --}}
    <button class="navbar-toggler navbar-toggler-right" type="button"
      data-toggle="collapse" data-target="#mims-nav"
      aria-controls="mims-nav" aria-expanded="false" aria-label="Toggle navigation"
      style="border-color:#30363d;">
      <span class="navbar-toggler-bar burger-lines" style="background:#7d8590;"></span>
      <span class="navbar-toggler-bar burger-lines" style="background:#7d8590;"></span>
      <span class="navbar-toggler-bar burger-lines" style="background:#7d8590;"></span>
    </button>

    {{-- ── RIGHT: ACTIONS ── --}}
    <div class="collapse navbar-collapse justify-content-end" id="mims-nav">
      <ul class="navbar-nav ml-auto" style="align-items:center;gap:4px;">

        {{-- Inventory badge --}}
        <li class="nav-item">
          <a href="{{ route('dash') }}" class="nav-link" style="
            background:rgba(47,129,247,0.12);
            color:#2f81f7;
            border:1px solid rgba(47,129,247,0.3);
            border-radius:20px;
            padding:4px 12px;
            font-size:12px;
            font-weight:500;
            display:flex;align-items:center;gap:6px;
            height:auto;line-height:1.4;
          ">
            <i class="fas fa-boxes" style="font-size:11px;"></i>
            Inventory
          </a>
        </li>

        {{-- Notification bell (Livewire, access-controlled) --}}
        @inject('acc', '\App\Http\Controllers\AccessController')
        @if($acc::checkAccess(Auth::id(), 'reorder_mod'))
          @livewire('notif.notif')
        @endif

        {{-- Logout --}}
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="confirmLogout()" style="
            background:rgba(248,81,73,0.1);
            color:#f85149;
            border:1px solid rgba(248,81,73,0.25);
            border-radius:8px;
            padding:6px 12px;
            font-size:12px;
            font-weight:500;
            height:auto;line-height:1.4;
            display:flex;align-items:center;gap:6px;
          ">
            Log out <i class="fas fa-sign-out-alt" style="font-size:11px;"></i>
          </a>
        </li>

        {{-- Divider --}}
        <li class="nav-item" style="width:1px;height:24px;background:#21262d;margin:0 6px;"></li>

        {{-- User info + avatar --}}
        <li class="nav-item" style="display:flex;align-items:center;gap:8px;">
          <div style="text-align:right;line-height:1.25;">
            <strong id="fullname" style="display:block;font-size:13px;font-weight:500;color:#e6edf3;"></strong>
            <span style="font-size:11px;color:#7d8590;">User</span>
          </div>
          <a href="#" style="display:block;flex-shrink:0;">
            <img
              id="profileImage"
              src="{{ asset('assets/img/default-avatar.png') }}"
              alt="Profile"
              style="
                width:34px;height:34px;
                border-radius:8px;
                border:1px solid #30363d;
                object-fit:cover;
              "
            >
          </a>
        </li>

      </ul>
    </div>

  </div>
</nav>