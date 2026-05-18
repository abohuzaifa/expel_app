@extends('layouts.app')

@section('content')
@php
    $balance = $wallet->amount ?? 0;
@endphp
<style>
    .wallet-history-page {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        min-height: 100vh;
        padding-bottom: 32px;
    }
    .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
        color: #fff;
        border: 0;
        border-radius: 22px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
    }
    .metric-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }
    .metric-label {
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .08em;
    }
    .metric-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
    }
    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }
    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-soft-info {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .badge-soft-warning {
        background: #fef3c7;
        color: #92400e;
    }
    .history-table thead th {
        border-top: 0;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .entry-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex: 0 0 42px;
    }
    .entry-icon.credit {
        background: #dcfce7;
        color: #166534;
    }
    .entry-icon.debit {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .entry-icon.failed {
        background: #fee2e2;
        color: #b91c1c;
    }
</style>

<div class="wallet-history-page">
    <div class="container py-4">
        <div class="hero-card card mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="text-uppercase small opacity-75 mb-2">Wallet history</div>
                        <h2 class="mb-2">{{ $wallet->user->name ?? 'Wallet' }}</h2>
                        <div class="opacity-75">Track credits, debits, and failed entries in one place.</div>
                    </div>
                    <div class="text-md-end">
                        <div class="small text-uppercase opacity-75">Current Balance</div>
                        <div style="font-size: 34px; font-weight: 800;">{{ number_format($balance, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Balance</div>
                        <div class="metric-value">{{ number_format($balance, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Credit total</div>
                        <div class="metric-value text-success">{{ number_format($summary['credits'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Debit total</div>
                        <div class="metric-value text-primary">{{ number_format($summary['debits'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Failed entries</div>
                        <div class="metric-value text-danger">{{ $summary['failed'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card metric-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="mb-1">Transactions</h5>
                        <div class="text-muted">Credits and debits are shown separately with status indicators.</div>
                    </div>
                    <a href="{{ route('wallet.index') }}" class="btn btn-outline-primary">Back to wallet list</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle history-table mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $entry)
                                @php
                                    $isCredit = (int) $entry->is_deposite === 1;
                                    $isDebit = (int) $entry->is_expanse === 1;
                                    $isFailed = isset($entry->status) && (int) $entry->status === 0;
                                    $entryType = $isCredit ? 'Credit' : ($isDebit ? 'Debit' : 'Entry');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="entry-icon {{ $isFailed ? 'failed' : ($isCredit ? 'credit' : 'debit') }}">
                                                @if($isFailed)
                                                    <i class="bi bi-x-circle-fill"></i>
                                                @elseif($isCredit)
                                                    <i class="bi bi-arrow-down-circle-fill"></i>
                                                @else
                                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                                @endif
                                            </span>
                                            <div>
                                                <div class="fw-semibold">{{ $entryType }}</div>
                                                <div class="text-muted small">#{{ $entry->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-primary' }}">
                                        {{ $isCredit ? '+' : '-' }}{{ number_format((float) $entry->amount, 2) }}
                                    </td>
                                    <td>{{ $entry->description ?? '-' }}</td>
                                    <td>
                                        @if($isFailed)
                                            <span class="badge badge-soft-danger">Failed</span>
                                        @else
                                            <span class="badge badge-soft-success">Completed</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($entry->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No wallet history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $history->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection