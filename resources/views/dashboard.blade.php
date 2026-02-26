@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Total Users</small>
                    <h3 class="fw-bold mb-0">2,540</h3>
                </div>
                <div class="icon-box bg-primary text-white">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Courses</small>
                    <h3 class="fw-bold mb-0">86</h3>
                </div>
                <div class="icon-box bg-success text-white">
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Enrollments</small>
                    <h3 class="fw-bold mb-0">1,320</h3>
                </div>
                <div class="icon-box bg-warning text-white">
                    <i class="bi bi-mortarboard"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Rating</small>
                    <h3 class="fw-bold mb-0">4.8 ⭐</h3>
                </div>
                <div class="icon-box bg-danger text-white">
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===== OVERVIEW SECTION ===== --}}
<div class="row g-4 mb-4">

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Platform Overview</h5>
                <p class="text-muted mb-0">
                    EduPlex is growing steadily. User registrations, course
                    enrollments, and engagement are increasing month by month.
                    This dashboard helps administrators monitor platform health
                    and activity in real time.
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Quick Actions</h5>

                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Course
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="#" class="btn btn-outline-dark">
                        <i class="bi bi-bar-chart"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===== RECENT ACTIVITY ===== --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Recent Activity</h5>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Doe</td>
                        <td>Enrolled in Flutter Course</td>
                        <td>Today</td>
                        <td><span class="badge bg-success">Completed</span></td>
                    </tr>
                    <tr>
                        <td>Sarah Kim</td>
                        <td>Registered Account</td>
                        <td>Yesterday</td>
                        <td><span class="badge bg-primary">New</span></td>
                    </tr>
                    <tr>
                        <td>David Lee</td>
                        <td>Left a Review</td>
                        <td>2 days ago</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
