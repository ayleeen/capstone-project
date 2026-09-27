<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lolay's Catering Services - Packages</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/packages.css') }}" rel="stylesheet">
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
<nav class="flex-grow-1">

    <a href="{{ route('admin.dashboard') }}" class="nav-link-custom">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>

    <a href="{{ route('admin.reservation') }}" class="nav-link-custom ">
        <i class="bi bi-journal-text"></i> Reservation
    </a>

    <a href="{{ route('admin.calendar') }}" class="nav-link-custom">
        <i class="bi bi-calendar3"></i> Calendar/ Schedule
    </a>

    <a href="{{ route('admin.packages') }}" class="nav-link-custom active">
        <i class="bi bi-box-seam"></i> Packages
    </a>

    <a href="{{ route('admin.menu') }}" class="nav-link-custom">
        <i class="bi bi-egg-fried"></i> Menu
    </a>

    <a href="{{ route('admin.customer') }}" class="nav-link-custom">
        <i class="bi bi-person"></i> Customer
    </a>

    <a href="reports.html" class="nav-link-custom">
        <i class="bi bi-bar-chart"></i> Reports
    </a>

    <a href="reviews.html" class="nav-link-custom">
        <i class="bi bi-star"></i> Reviews
    </a>

    <a href="settings.html" class="nav-link-custom">
        <i class="bi bi-gear"></i> Settings
    </a>

</nav>
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
        <h1 class="page-title">Packages</h1>
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

      <!-- Stat cards -->
      <div class="row g-3 mb-3 align-items-stretch">
        <div class="col-6 col-lg-3">
          <div class="stat-card h-100 p-3 d-flex align-items-center gap-3">
            <div class="stat-ico rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-box-seam"></i>
            </div>
            <div>
              <div class="stat-label">Total Packages</div>
              <div class="stat-value" id="statTotal">
    {{ $packages->count() }}
</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card h-100 p-3 d-flex align-items-center gap-3">
            <div class="stat-ico rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-clock-history"></i>
            </div>
            <div>
              <div class="stat-label">Best Selling</div>
              <div class="stat-value" style="font-size:14px;">—</div>
              <div class="stat-sub">Package</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card h-100 p-3 d-flex align-items-center gap-3">
            <div class="stat-ico rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-check-lg"></i>
            </div>
            <div>
              <div class="stat-label">Average Price</div>
              <div class="stat-value">
    ₱{{ number_format($packages->avg('price') ?? 0, 2) }}
</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <div class="stat-card h-100 p-3 d-flex align-items-center gap-3">
            <div class="stat-ico rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
              <!-- <div class="stat-label">Total Sales</div>
              <div class="stat-value">$ 0</div> -->
              <div class="stat-label">Total Sales</div>
<div class="stat-value">
    ₱{{ number_format($packages->sum('price'), 2) }}
</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-1 add-package">
           <a href="{{ route('admin.packages.create') }}" id="addPackageBtn">
                <i class="bi bi-plus-circle-fill"></i> Add Package
            </a>
        </div>
      </div>

      <!-- FILTER BAR -->
      <div class="filter-bar d-flex align-items-center gap-3 mb-3 flex-wrap">
        <input type="text" id="searchInput" class="form-control" style="max-width:220px;" placeholder="Search package...">

        <div class="d-flex align-items-center gap-2">
          <span class="filter-label">Category</span>
          <select id="categoryFilter" class="form-select" style="width:auto;">
            <option>All Categories</option>
            <option>Wedding</option>
            <option>Birthday</option>
          </select>
        </div>

        <div class="d-flex align-items-center gap-2">
          <span class="filter-label">Status</span>
          <select id="statusFilter" class="form-select" style="width:auto;">
            <option>All Status</option>
            <option>Active</option>
            <option>Inactive</option>
          </select>
        </div>

        <div class="flex-fill"></div>

        <div class="d-flex align-items-center gap-2">
          <span class="filter-label">Sort by</span>
          <select id="sortFilter" class="form-select" style="width:auto;">
            <option>Newest First</option>
            <option>Oldest First</option>
            <option>Price: High to Low</option>
            <option>Price: Low to High</option>
          </select>
        </div>
      </div>

      <!-- TABLE + DETAIL PANEL -->
    
        <!-- TABLE + DETAIL PANEL -->
<div class="row g-3 align-items-start">

    <!-- TABLE -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Guest</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">

                        @forelse($packages as $package)

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">

                                        @if($package->photo)
                                            <img src="{{ asset('storage/' . $package->photo) }}"
                                                 alt="{{ $package->package_name }}"
                                                 style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                        @else
                                            <div style="width:50px;height:50px;background:#f1f1f1;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <strong>{{ $package->package_name }}</strong>

                                            <small class="d-block text-muted">
                                                {{ Str::limit($package->description, 40) }}
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td>{{ $package->category }}</td>

                                <td>
                                    ₱{{ number_format($package->price, 2) }}
                                </td>

                                <td>
                                    {{ $package->guest_min }} - {{ $package->guest_max }}
                                </td>

                                <td>
                                    @if($package->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    0
                                </td>

                                <td>

                                    <!-- VIEW -->
                                    <a href="{{ route('admin.packages.show', $package) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- EDIT -->
                                    <a href="{{ route('admin.packages.edit', $package) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('admin.packages.destroy', $package) }}"
                                          method="POST"
                                          style="display:inline;">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this package?')">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No packages found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- DETAILS PANEL -->
    <div class="col-lg-4">
        <div class="detail-panel p-3" id="detailPanel">
            asdad
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<!-- <script src="{{ asset('js/packages.js') }}"></script> -->
</body>
</html>
