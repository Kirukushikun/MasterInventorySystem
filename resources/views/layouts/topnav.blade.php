<style>
  .mims-topbar {
    height: 60px;
    background: #161b22;
    border-bottom: 1px solid #21262d;
    display: flex;
    align-items: center;
    padding: 0 24px;
    position: sticky;
    top: 0;
    z-index: 50;
    gap: 12px;
  }

  .mims-topbar-title {
    flex: 1;
    font-size: 15px;
    font-weight: 600;
    color: #e6edf3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .mims-topbar-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }

  .mims-topbar-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(47,129,247,0.12);
    color: #2f81f7;
    border: 1px solid rgba(47,129,247,0.3);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.15s;
    white-space: nowrap;
  }
  .mims-topbar-badge:hover {
    background: rgba(47,129,247,0.2);
    color: #2f81f7;
    text-decoration: none;
  }

  .mims-topbar-divider {
    width: 1px;
    height: 24px;
    background: #21262d;
    margin: 0 4px;
    flex-shrink: 0;
  }

  .mims-topbar-user {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }

  .mims-topbar-avatar {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: linear-gradient(135deg, #2f81f7, #a371f7);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: 0.03em;
  }

  .mims-topbar-userinfo {
    line-height: 1.25;
    text-align: left;
  }
  .mims-topbar-userinfo strong {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #e6edf3;
    white-space: nowrap;
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .mims-topbar-userinfo span {
    font-size: 11px;
    color: #7d8590;
  }

  .mims-topbar-logout {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(248,81,73,0.08);
    color: #f85149;
    border: 1px solid rgba(248,81,73,0.25);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s;
    white-space: nowrap;
  }
  .mims-topbar-logout:hover {
    background: rgba(248,81,73,0.16);
    color: #f85149;
  }

  .mims-topbar-hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 5px;
    width: 34px;
    height: 34px;
    background: #1c2330;
    border: 1px solid #21262d;
    border-radius: 8px;
    cursor: pointer;
    padding: 0;
    flex-shrink: 0;
  }
  .mims-topbar-hamburger span {
    display: block;
    width: 16px;
    height: 2px;
    background: #7d8590;
    border-radius: 2px;
    transition: background 0.15s;
  }
  .mims-topbar-hamburger:hover span { background: #e6edf3; }

  /* ── Responsive ── */
  @media (max-width: 768px) {
    .mims-topbar { padding: 0 16px; gap: 8px; }
    .mims-topbar-badge-text { display: none; }
    .mims-topbar-badge { padding: 5px 8px; border-radius: 8px; }
    .mims-topbar-userinfo { display: none; }
    .mims-topbar-logout-text { display: none; }
    .mims-topbar-logout { padding: 5px 8px; }
    .mims-topbar-hamburger { display: flex; }
  }

  @media (max-width: 480px) {
    .mims-topbar-divider { display: none; }
  }
</style>

@php
  $authUser  = Auth::user();
  $nameParts = explode(' ', $authUser->name ?? 'User');
  $initials  = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
@endphp

<nav class="mims-topbar">

  {{-- ── LEFT: PAGE TITLE ── --}}
  <span class="mims-topbar-title">@yield('title')</span>

  {{-- ── RIGHT: ACTIONS ── --}}
  <div class="mims-topbar-actions">

    {{-- Inventory badge --}}
    <a href="{{ route('dash') }}" class="mims-topbar-badge">
      <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6z"/>
      </svg>
      <span class="mims-topbar-badge-text">Inventory</span>
    </a>

    {{-- Notification bell (Livewire, access-controlled) --}}
    @inject('acc', '\App\Http\Controllers\AccessController')
    @if($acc::checkAccess(Auth::id(), 'reorder_mod'))
      @livewire('notif.notif')
    @endif

    {{-- Divider --}}
    <div class="mims-topbar-divider"></div>

    {{-- Avatar + name --}}
    <div class="mims-topbar-user">
      <div class="mims-topbar-avatar">{{ $initials }}</div>
      <div class="mims-topbar-userinfo">
        <strong>{{ $authUser->name ?? 'User' }}</strong>
        <span>User</span>
      </div>
    </div>

    {{-- Logout --}}
    <button class="mims-topbar-logout" onclick="confirmLogout()" type="button">
      <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor">
        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
      </svg>
      <span class="mims-topbar-logout-text">Log out</span>
    </button>

    {{-- Mobile hamburger (toggles sidebar) --}}
    <button class="mims-topbar-hamburger" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
      <span></span>
      <span></span>
      <span></span>
    </button>

  </div>
</nav>