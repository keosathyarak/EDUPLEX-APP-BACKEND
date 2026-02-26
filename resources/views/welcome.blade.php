<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>

  <style>
    :root{
      --sidebar-w: 260px;
      --bg: #f6f7fb;
      --card: #ffffff;
      --text: #111827;
      --muted: #6b7280;
      --primary: #2563eb;
      --border: #e5e7eb;
    }

    body{
      background: var(--bg);
      color: var(--text);
      overflow-x: hidden;
    }

    /* Layout */
    .app{
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar */
    .sidebar{
      width: var(--sidebar-w);
      background: #0b1220;
      color: #d1d5db;
      position: sticky;
      top: 0;
      height: 100vh;
      padding: 16px 14px;
      border-right: 1px solid rgba(255,255,255,0.06);
    }

    .brand{
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 10px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      margin-bottom: 14px;
    }
    .brand .logo{
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: linear-gradient(135deg, #2563eb, #22c55e);
      display: grid;
      place-items: center;
      color: white;
      font-weight: 700;
    }
    .brand .title{
      line-height: 1.1;
    }
    .brand .title strong{ color: #fff; }
    .brand .title small{ color: #9ca3af; }

    .nav-section{
      margin-top: 10px;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #9ca3af;
      padding: 10px 10px 6px;
    }

    .side-link{
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 12px;
      color: #d1d5db;
      text-decoration: none;
      transition: 0.2s ease;
      margin: 4px 6px;
    }
    .side-link:hover{
      background: rgba(255,255,255,0.06);
      color: #fff;
    }
    .side-link.active{
      background: rgba(37,99,235,0.18);
      color: #fff;
      border: 1px solid rgba(37,99,235,0.35);
    }

    .sidebar-footer{
      position: absolute;
      bottom: 16px;
      left: 14px;
      right: 14px;
      padding-top: 12px;
      border-top: 1px solid rgba(255,255,255,0.08);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }
    .user-mini{
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .avatar{
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: rgba(255,255,255,0.1);
      display: grid;
      place-items: center;
    }

    /* Main */
    .main{
      flex: 1;
      padding: 18px 18px 28px;
    }

    /* Topbar */
    .topbar{
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
    }
    .search{
      max-width: 420px;
      width: 100%;
    }

    /* Cards */
    .stat-card{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: 0 10px 25px rgba(17,24,39,0.04);
      height: 100%;
    }
    .stat-card .icon{
      width: 40px;
      height: 40px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      background: rgba(37,99,235,0.10);
      color: var(--primary);
    }
    .stat-card .label{
      color: var(--muted);
      font-size: 13px;
    }
    .stat-card .value{
      font-size: 24px;
      font-weight: 800;
      letter-spacing: -0.02em;
      margin: 6px 0 0;
    }
    .badge-soft{
      background: rgba(34,197,94,0.12);
      color: #16a34a;
      border: 1px solid rgba(34,197,94,0.25);
      border-radius: 999px;
      font-size: 12px;
      padding: 4px 10px;
    }

    /* Panels */
    .panel{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(17,24,39,0.04);
    }
    .panel-header{
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }
    .panel-body{ padding: 16px; }

    /* Responsive sidebar */
    @media (max-width: 992px){
      .sidebar{
        position: fixed;
        left: -100%;
        top: 0;
        z-index: 1050;
        transition: 0.25s ease;
      }
      .sidebar.show{ left: 0; }
      .main{ padding: 14px; }
    }
  </style>
</head>

<body>
  <div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">
        <div class="logo">ED</div>
        <div class="title">
          <strong>EduPlex</strong><br />
          <small>Admin Dashboard</small>
        </div>
      </div>

      <div class="nav-section">Main</div>
      <a class="side-link active" href="#">
        <i class="bi bi-grid"></i> Dashboard
      </a>
      <a class="side-link" href="#">
        <i class="bi bi-book"></i> Courses
      </a>
      <a class="side-link" href="#">
        <i class="bi bi-people"></i> Users
      </a>
      <a class="side-link" href="#">
        <i class="bi bi-bar-chart"></i> Reports
      </a>

      <div class="nav-section">Settings</div>
      <a class="side-link" href="#">
        <i class="bi bi-gear"></i> Settings
      </a>
      <a class="side-link" href="#">
        <i class="bi bi-shield-check"></i> Security
      </a>

      <div class="sidebar-footer">
        <div class="user-mini">
          <div class="avatar"><i class="bi bi-person"></i></div>
          <div style="line-height:1.1">
            <div class="text-white fw-semibold" style="font-size:13px;">Admin</div>
            <div style="font-size:12px;color:#9ca3af;">admin@eduplex.com</div>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-light" type="button">
          <i class="bi bi-box-arrow-right"></i>
        </button>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-primary d-lg-none" id="btnMenu" type="button">
            <i class="bi bi-list"></i>
          </button>
          <div>
            <h5 class="mb-0 fw-bold">Dashboard</h5>
            <small class="text-secondary">Overview & quick insights</small>
          </div>
        </div>

        <div class="search">
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input class="form-control" placeholder="Search courses, users..." />
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-light border" type="button">
            <i class="bi bi-bell"></i>
          </button>
          <button class="btn btn-primary" type="button">
            <i class="bi bi-plus-lg me-1"></i> New Course
          </button>
        </div>
      </div>

      <!-- STATS -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
              <div class="icon"><i class="bi bi-people"></i></div>
              <span class="badge-soft">+12%</span>
            </div>
            <div class="label mt-3">Total Users</div>
            <div class="value">2,540</div>
            <small class="text-secondary">Active this month</small>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
              <div class="icon"><i class="bi bi-book"></i></div>
              <span class="badge-soft">+6%</span>
            </div>
            <div class="label mt-3">Courses</div>
            <div class="value">86</div>
            <small class="text-secondary">Published courses</small>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
              <div class="icon"><i class="bi bi-play-btn"></i></div>
              <span class="badge-soft">+18%</span>
            </div>
            <div class="label mt-3">Lessons Watched</div>
            <div class="value">12,430</div>
            <small class="text-secondary">Last 30 days</small>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
              <div class="icon"><i class="bi bi-star"></i></div>
              <span class="badge-soft">4.8</span>
            </div>
            <div class="label mt-3">Average Rating</div>
            <div class="value">4.8/5</div>
            <small class="text-secondary">From student reviews</small>
          </div>
        </div>
      </div>

      <!-- TABLE + ACTIVITY -->
      <div class="row g-3">
        <div class="col-12 col-xl-8">
          <div class="panel">
            <div class="panel-header">
              <div>
                <div class="fw-bold">Recent Users</div>
                <small class="text-secondary">Newest registrations</small>
              </div>
              <button class="btn btn-sm btn-outline-primary" type="button">
                View All
              </button>
            </div>

            <div class="panel-body">
              <div class="table-responsive">
                <table class="table align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>User</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th class="text-end">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="fw-semibold">Keo Sathyarak</td>
                      <td>keo@email.com</td>
                      <td><span class="badge text-bg-success">Active</span></td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td class="fw-semibold">Sopheak</td>
                      <td>sopheak@email.com</td>
                      <td><span class="badge text-bg-warning">Pending</span></td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td class="fw-semibold">Lina</td>
                      <td>lina@email.com</td>
                      <td><span class="badge text-bg-success">Active</span></td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <div class="col-12 col-xl-4">
          <div class="panel h-100">
            <div class="panel-header">
              <div>
                <div class="fw-bold">Activity</div>
                <small class="text-secondary">Latest actions</small>
              </div>
              <button class="btn btn-sm btn-outline-secondary" type="button">
                Export
              </button>
            </div>
            <div class="panel-body">
              <div class="d-flex gap-3 mb-3">
                <div class="icon" style="width:38px;height:38px;border-radius:14px;background:rgba(34,197,94,.12);color:#16a34a;display:grid;place-items:center;">
                  <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                  <div class="fw-semibold">Course published</div>
                  <small class="text-secondary">Laravel API (Ms. Lina)</small>
                </div>
              </div>

              <div class="d-flex gap-3 mb-3">
                <div class="icon" style="width:38px;height:38px;border-radius:14px;background:rgba(37,99,235,.12);color:#2563eb;display:grid;place-items:center;">
                  <i class="bi bi-person-plus"></i>
                </div>
                <div>
                  <div class="fw-semibold">New user registered</div>
                  <small class="text-secondary">keo@email.com</small>
                </div>
              </div>

              <div class="d-flex gap-3">
                <div class="icon" style="width:38px;height:38px;border-radius:14px;background:rgba(245,158,11,.14);color:#d97706;display:grid;place-items:center;">
                  <i class="bi bi-star"></i>
                </div>
                <div>
                  <div class="fw-semibold">New review</div>
                  <small class="text-secondary">5 stars on Flutter Dev</small>
                </div>
              </div>

              <hr class="my-4" />
              <button class="btn btn-primary w-100" type="button">
                <i class="bi bi-graph-up-arrow me-1"></i> View Analytics
              </button>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mobile sidebar toggle
    const btnMenu = document.getElementById('btnMenu');
    const sidebar = document.getElementById('sidebar');
    btnMenu?.addEventListener('click', () => sidebar.classList.toggle('show'));

    // Close sidebar when clicking outside (mobile)
    document.addEventListener('click', (e) => {
      if (window.innerWidth > 992) return;
      const isClickInside = sidebar.contains(e.target) || btnMenu.contains(e.target);
      if (!isClickInside) sidebar.classList.remove('show');
    });
  </script>
</body>
</html>
