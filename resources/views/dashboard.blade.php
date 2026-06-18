@extends('layouts.app')

@section('title', __('Dashboard Overview'))
@section('page-title', __('Dashboard'))

@section('content')
<div class="container-fluid">
    
    {{-- STATS CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Total Users --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success small">+12%</span>
                    </div>
                    <h6 class="text-secondary mb-1">{{ __('Total Users') }}</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Courses --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <span class="badge bg-info-subtle text-info small">{{ __('Active') }}</span>
                    </div>
                    <h6 class="text-secondary mb-1">{{ __('Total Courses') }}</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($totalCourses) }}</h3>
                </div>
            </div>
        </div>

        {{-- Monthly Earnings --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <span class="text-success small fw-bold">{{ __('This Month') }}</span>
                    </div>
                    <h6 class="text-secondary mb-1">{{ __('Monthly Revenue') }}</h6>
                    <h3 class="fw-bold mb-0">${{ number_format($earningsThisMonth, 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- Total Earnings --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="text-warning small fw-bold">{{ __('All Time') }}</span>
                    </div>
                    <h6 class="text-secondary mb-1">{{ __('Total Revenue') }}</h6>
                    <h3 class="fw-bold mb-0">${{ number_format($totalEarnings, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- PERIOD REVENUE BREAKDOWN --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <p class="text-secondary mb-1">{{ __('Weekly Revenue') }}</p>
                    <h4 class="fw-bold text-dark">${{ number_format($earningsThisWeek, 2) }}</h4>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <p class="text-secondary mb-1">{{ __('Monthly Revenue') }}</p>
                    <h4 class="fw-bold text-dark">${{ number_format($earningsThisMonth, 2) }}</h4>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <p class="text-secondary mb-1">{{ __('Yearly Revenue') }}</p>
                    <h4 class="fw-bold text-dark">${{ number_format($earningsThisYear, 2) }}</h4>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- EARNINGS GRAPH --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">{{ __('Earnings Overview') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- RECENT TRANSACTIONS --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">{{ __('Recent Payments') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-light rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-secondary small"></i>
                                            </div>
                                            <div class="small">
                                                <div class="fw-bold">{{ $payment->user->name ?? __('User') }}</div>
                                                <div class="text-muted" style="font-size: 11px;">{{ $payment->course->title ?? __('Course') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="fw-bold small text-success">+${{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-muted" style="font-size: 10px;">{{ $payment->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted small">{{ __('No recent payments') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-center">
                    <a href="{{ route('reports.payments') }}" class="btn btn-sm btn-light text-primary fw-bold">{{ __('View All Reports') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    
    .icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    
    // Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: "{{ __('Earnings') }} ($)",
                data: {!! json_encode($data) !!},
                borderColor: '#0d6efd',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: isDark ? '#1e293b' : '#fff',
                pointBorderColor: '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, borderDash: [5, 5], drawBorder: false },
                    ticks: {
                        color: textColor,
                        callback: function(value) { return '$' + value; },
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 11 } }
                }
            }
        }
    });
</script>
@endpush
