<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Menu Management</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{asset('css/menu-management.css')}}" rel="stylesheet">
</head>
<body>
<div class="container-fluid p-3">
  <div class="row g-3">

    <!-- SIDEBAR -->
    <div class="col-lg-2">
      <div class="sidebar d-flex flex-column">
        <div class="brand-box">
          <div class="brand-logo">
             <img src="{{ asset('image/lolaysLogo.jpg') }}" alt="">
          </div>
          <div class="brand-name">Lolay's Catering<br>Services</div>
        </div>
        <nav class="flex-grow-1">
          <a href="{{ route('admin.dashboard') }}" class="nav-link-custom"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
          <a href="{{ route('admin.reservation') }}" class="nav-link-custom"><i class="bi bi-journal-text"></i> Reservation</a>
          <a href="{{ route('admin.calendar') }}" class="nav-link-custom"><i class="bi bi-calendar3"></i> Calendar/ Schedule</a>
          <a href="{{ route('admin.packages') }}" class="nav-link-custom"><i class="bi bi-box-seam"></i> Packages</a>
          <a href="{{ route('admin.menu') }}" class="nav-link-custom active"><i class="bi bi-egg-fried"></i> Menu</a>
          <a href="{{ route('admin.customer') }}" class="nav-link-custom"><i class="bi bi-person"></i> Customer</a>
          <a href="reports.html" class="nav-link-custom"><i class="bi bi-bar-chart"></i> Reports</a>
          <a href="reviews.html" class="nav-link-custom"><i class="bi bi-star"></i> Reviews</a>
          <a href="settings.html" class="nav-link-custom"><i class="bi bi-gear"></i> Settings</a>
        </nav>
        <div class="promo-card p-3 text-center mt-auto">
          <div class="promo-ico rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
            <img src="{{ asset('image/download.png') }}" alt="">
          </div>
          <h4 class="mb-1">Delicious Events<br>Memorable Moments</h4>
          <p class="mb-0">We make every occasion extra special.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
          @csrf
          <button type="submit" class="logout-link d-flex align-items-center justify-content-center gap-2 py-2 w-100 border-0 bg-transparent">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </form>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-lg-10">

      <!-- Topbar -->
      <div class="topbar">
        <h1 class="page-title">Menu Management</h1>
        <div class="d-flex align-items-center gap-3">
          <div class="icon-btn"><i class="bi bi-bell"></i></div>
          <div class="dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
              <img src="https://i.pravatar.cc/100?img=12" class="admin-avatar" alt="Admin">
              <div>
                <div style="font-weight:600;font-size:13px;">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div style="font-size:10.5px;color:var(--muted);">Administrator</div>
              </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="bi bi-box-arrow-right"></i> Logout
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <!-- Stat cards -->
      <div class="row g-3 mb-3 align-items-stretch">
        <div class="col-6 col-lg-2">
          <div class="stat-card-menu h-100">
            <div class="stat-ico-menu ico-green"><i class="bi bi-egg-fried"></i></div>
            <div>
              <div class="stat-label-menu">Total Menu Items</div>
              <div class="stat-value-menu" id="statTotalItems">{{ $stats['total'] }}</div>
              <div class="stat-sub-menu">Active Items</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card-menu h-100">
            <div class="stat-ico-menu ico-yellow"><i class="bi bi-grid-fill"></i></div>
            <div>
              <div class="stat-label-menu">Total Categories</div>
              <div class="stat-value-menu" id="statTotalCategories">{{ $stats['totalCategories'] }}</div>
              <div class="stat-sub-menu">Menu Categories</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card-menu h-100">
            <div class="stat-ico-menu ico-pink"><i class="bi bi-tags-fill"></i></div>
            <div>
              <div class="stat-label-menu">Most Popular Category</div>
              <div class="stat-value-menu" style="font-size:14px;" id="statPopularCategory">{{ $stats['popularCategory'] }}</div>
              <div class="stat-sub-menu">Based on Bookings</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card-menu h-100">
            <div class="stat-ico-menu ico-teal"><i class="bi bi-graph-up"></i></div>
            <div>
              <div class="stat-label-menu">Total Sale (This Month)</div>
              <div class="stat-value-menu" id="statTotalSale">₱{{ number_format($stats['totalSales'], 2) }}</div>
              <div class="stat-sub-menu">From Menu Items</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-2 d-flex">
          <a href="{{ route('admin.menu.add') }}" id="addNewMenuBtn" class="btn btn-add-menu flex-fill d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Add New Menu
          </a>
        </div>
      </div>

      <div class="row g-3">
        <!-- LEFT: table -->
        <div class="col-lg-9">

          <!-- Filter bar -->
          <form method="GET" action="{{ route('admin.menu') }}" class="filter-bar-menu d-flex align-items-center gap-3 mb-3 flex-wrap" id="filterForm">
            <div class="search-wrap flex-fill">
              <i class="bi bi-search"></i>
              <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Search menu items">
            </div>

            <select name="category" id="categoryFilter" class="form-select" style="width:auto;" onchange="this.form.submit()">
              <option value="All Categories">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
              @endforeach
            </select>

            <select name="status" id="statusFilter" class="form-select" style="width:auto;" onchange="this.form.submit()">
              <option value="All Status">All Status</option>
              <option value="Active" @selected(request('status') === 'Active')>Active</option>
              <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
            </select>

            <div class="d-flex align-items-center gap-2">
              <span class="filter-label-menu">Sort by</span>
              <select name="sort" id="sortFilter" class="form-select" style="width:auto;" onchange="this.form.submit()">
                <option @selected(request('sort', 'Newest First') === 'Newest First')>Newest First</option>
                <option @selected(request('sort') === 'Oldest First')>Oldest First</option>
                <option @selected(request('sort') === 'Price: High to Low')>Price: High to Low</option>
                <option @selected(request('sort') === 'Price: Low to High')>Price: Low to High</option>
              </select>
            </div>

            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
          </form>

          <!-- Tabs -->
          <div class="menu-tabs mb-2">
            <a href="{{ route('admin.menu', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
               class="menu-tab {{ request('tab', 'all') === 'all' ? 'active' : '' }}" id="tabAll">All Menu Items</a>
            <a href="{{ route('admin.menu', array_merge(request()->except('tab'), ['tab' => 'inactive'])) }}"
               class="menu-tab {{ request('tab') === 'inactive' ? 'active' : '' }}" id="tabInactive">Inactive Items</a>
          </div>

          <!-- Table -->
          <div class="table-card">
            <div class="table-responsive">
              <table class="table mb-0 align-middle menu-mgmt-table">
                <thead>
                  <tr>
                    <th>Menu Items</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @forelse($menuItems as $item)
                    <tr data-id="{{ $item->id }}">
                      <td class="d-flex align-items-center gap-2">
                        <img src="{{ $item->image_path ? asset('storage/'.$item->image_path) : asset('image/no-image.png') }}"
                             alt="{{ $item->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                        <div>
                          <div style="font-weight:600;">{{ $item->name }}</div>
                          <div style="font-size:11px;color:var(--muted);">{{ \Illuminate\Support\Str::limit($item->description, 40) }}</div>
                        </div>
                      </td>
                      <td>{{ $item->category }}</td>
                      <td>₱{{ number_format($item->price, 2) }}</td>
                      <td>
                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                          {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>
                        <a href="{{ route('admin.menu.edit', $item) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this menu item?');">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center">No menu items found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="table-footer" id="tableFooter">
              Showing {{ $menuItems->firstItem() ?? 0 }}&ndash;{{ $menuItems->lastItem() ?? 0 }} of {{ $menuItems->total() }} items
            </div>
            <div class="p-2">
              {{ $menuItems->links() }}
            </div>
          </div>

        </div>

        <!-- RIGHT: categories + tips -->
        <div class="col-lg-3 right-col-sticky">
          <div class="panel-white mb-3">
            <div class="panel-heading">Menu Categories</div>
            <div id="categoriesList" class="categories-list">
              @forelse($categoryCounts as $cat => $count)
                <div class="category-item d-flex justify-content-between">
                  <span>{{ $cat }}</span>
                  <span>{{ $count }}</span>
                </div>
              @empty
                <div class="category-empty" id="categoriesEmpty">No categories yet.</div>
              @endforelse
            </div>
            <button type="button" class="btn-manage-category" id="manageCategoryBtn">
              <i class="bi bi-gear"></i> Manage Category
            </button>
          </div>

          <div class="menu-tips-card">
            <div class="menu-tips-ico"><i class="bi bi-lightbulb-fill"></i></div>
            <div>
              <div class="menu-tips-title">Menu Tips</div>
              <div class="menu-tips-text">Keep your menu updated with seasonal items to attract more customers.</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('js/menu-management.js')}}"></script>
</body>
</html>