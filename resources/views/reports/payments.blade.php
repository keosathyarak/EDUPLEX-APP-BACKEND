@extends('layouts.app')

@section('title', 'Payment Reports')
@section('page-title', 'Payment Reports')

@section('content')
<div class="container-fluid">
    
    {{-- FILTER SECTION --}}
    <div class="card shadow-sm border-0 mb-4 d-print-none">
        <div class="card-body">
            <form action="{{ route('reports.payments') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('reports.payments') }}" class="btn btn-light border">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset') }}
                    </a>
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> {{ __('Print Report') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- REPORT CONTENT --}}
    <div id="printableReport">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-cash-stack me-2"></i>{{ __('Successful Payments Report') }}
                    </h5>
                    <small class="text-muted d-block mt-1">
                        @if(request('start_date') || request('end_date'))
                            {{ __('Period') }}: {{ request('start_date') ?? __('Beginning') }} {{ __('to') }} {{ request('end_date') ?? __('Today') }}
                        @else
                            {{ __('Showing all-time records') }}
                        @endif
                    </small>
                </div>
                <div class="text-end">
                    <div class="h5 mb-0 fw-bold text-success">${{ number_format($totalAmount, 2) }}</div>
                    <small class="text-secondary fw-semibold">{{ __('Total Revenue') }}</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">{{ __('ID') }}</th>
                                <th class="py-3 border-0">{{ __('User') }}</th>
                                <th class="py-3 border-0">{{ __('Course') }}</th>
                                <th class="py-3 border-0">{{ __('Amount') }}</th>
                                <th class="py-3 border-0 text-center">{{ __('Status') }}</th>
                                <th class="px-4 py-3 border-0 text-end">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td class="px-4 text-secondary fw-medium">#{{ $payment->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center d-print-none" style="width: 32px; height: 32px; font-size: 12px; font-weight: bold;">
                                            {{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $payment->user->name ?? __('Unknown User') }}</div>
                                            <small class="text-secondary">{{ $payment->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $payment->course->title ?? __('Unknown Course') }}</div>
                                    <small class="text-secondary">{{ $payment->course->teacher ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        ${{ number_format($payment->amount, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        {{ __(ucfirst($payment->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 text-end text-secondary">
                                    {{ $payment->created_at->format('M d, Y') }}<br>
                                    <small class="d-print-none">{{ $payment->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-secondary">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <p class="mb-0">{{ __('No records found for the selected period.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
            <div class="card-footer bg-white py-3 d-print-none">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    
    @media print {
        body { background: white !important; }
        .sidebar, .topbar, .btn, .d-print-none { display: none !important; }
        .content { margin-left: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .table th, .table td { border-bottom: 1px solid #dee2e6 !important; padding: 10px !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; }
        #printableReport { margin-top: 0 !important; }
        .badge { border: 1px solid #28a745 !important; color: #28a745 !important; background: transparent !important; }
    }
</style>
@endpush
