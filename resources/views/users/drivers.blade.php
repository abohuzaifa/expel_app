@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Driver Verification</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('lang.home') }}</a></li>
      <li class="breadcrumb-item">Users</li>
      <li class="breadcrumb-item active">Driver Verification</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Review uploaded driver documents and account status</h5>

          @if ($message = Session::get('success'))
          <div class="alert alert-success">
            <p>{{ $message }}</p>
          </div>
          @endif

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead>
                <tr>
                  <th>{{ trans('lang.number') }}</th>
                  <th>{{ trans('lang.name') }}</th>
                  <th>{{ trans('lang.mobile') }}</th>
                  <th>{{ trans('lang.email') }}</th>
                  <th>Vehicle</th>
                  <th>Status</th>
                  <th>Verification</th>
                  <th>{{ trans('lang.action') }}</th>
                </tr>
              </thead>
              <tbody>
                @php
                $page = request('page', 1);
                $i = ($page * $perPage) - $perPage;
                @endphp
                @forelse ($drivers as $driver)
                <tr>
                  <td>{{ ++$i }}</td>
                  <td>{{ $driver->name }}</td>
                  <td>{{ $driver->mobile }}</td>
                  <td>{{ $driver->email ?? 'N/A' }}</td>
                  <td>
                    <div>{{ $driver->number_plate ?? 'N/A' }}</div>
                    <small class="text-muted">License: {{ $driver->driving_license ?? 'N/A' }}</small>
                  </td>
                  <td>
                    <span class="badge {{ $driver->status ? 'bg-success' : 'bg-secondary' }}">
                      {{ $driver->status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    @php $status = $driver->verification_status ?? 'pending'; @endphp
                    <span class="badge {{ $status === 'verified' ? 'bg-success' : ($status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                      {{ ucfirst($status) }}
                    </span>
                  </td>
                  <td>
                    <a class="btn btn-primary btn-sm" href="{{ route('drivers.verifications.show', $driver->id) }}">Review</a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="text-center text-muted">No drivers found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{ $drivers->onEachSide(1)->links('vendor.pagination.default') }}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection