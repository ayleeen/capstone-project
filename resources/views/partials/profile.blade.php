{{-- Admin profile dropdown (bell + Admin User menu). Include it on every admin page:
     @include('admin.partials.profile-menu')  --}}

@once
<style>
  .pm-icon-btn{
    width:38px; height:38px; border-radius:50%;
    background:#fff; display:flex; align-items:center; justify-content:center;
    box-shadow:0 0 0 1px #eee; color:#2b2f36; position:relative;
    border:none; cursor:pointer; font-size:17px;
  }
  .pm-icon-btn:hover{ background:#f4f4f4; }
  .pm-wrap{ position:relative; z-index:40; }

  .pm-chip{
    display:flex; align-items:center; gap:8px;
    padding:4px 8px; border:0; border-radius:12px;
    background:none; color:inherit; font-family:inherit; cursor:pointer;
  }
  .pm-chip:hover{ background:#fff; }
  .pm-avatar{ width:38px; height:38px; border-radius:50%; object-fit:cover; flex:none; }
  .pm-caret{ font-size:11px; color:var(--muted,#8a8f98); transition:transform .15s; }
  .pm-chip[aria-expanded="true"] .pm-caret{ transform:rotate(180deg); }

  .pm-menu{
    --bs-dropdown-link-active-bg: var(--green-light,#e6f4e8);
    --bs-dropdown-link-active-color: var(--green-dark,#256330);
    --bs-dropdown-link-hover-bg: var(--green-light,#e6f4e8);
    --bs-dropdown-link-hover-color: var(--green-dark,#256330);
    min-width:190px; margin-top:6px; padding:6px;
    border:1px solid var(--border,#eee); border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.08); font-size:13.5px;
  }
  .pm-menu .dropdown-item{
    display:flex; align-items:center; gap:10px;
    padding:8px 10px; border-radius:8px;
  }
  .pm-menu .dropdown-item.text-danger{ color:var(--red,#d64545) !important; }
  .pm-menu .dropdown-item.text-danger:hover,
  .pm-menu .dropdown-item.text-danger:focus,
  .pm-menu .dropdown-item.text-danger:active{
    background:#fbeaea;
    color:var(--maroon,#8e1b1b) !important;
  }
</style>
@endonce

<div class="d-flex align-items-center gap-3 pm-wrap">

  <button class="pm-icon-btn" type="button" aria-label="Notifications">
    <i class="bi bi-bell"></i>
  </button>

  <div class="dropdown">
    <button class="pm-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://i.pravatar.cc/100?img=12" class="pm-avatar" alt="">
      <span class="text-start">
        <span class="d-block" style="font-weight:600;font-size:13px;">
          {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
        </span>
        <span class="d-block" style="font-size:10.5px;color:var(--muted,#8a8f98);">
          {{ auth()->user()->is_admin ? 'Administrator' : 'User' }}
        </span>
      </span>
      <i class="bi bi-chevron-down pm-caret"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end pm-menu">
      <li>
        <a class="dropdown-item" href="{{ Route::has('admin.profile') ? route('admin.profile') : route('admin.settings') }}">
          <i class="bi bi-person"></i> My Profile
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('admin.settings') }}">
          <i class="bi bi-gear"></i> Settings
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="dropdown-item text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </div>

</div>