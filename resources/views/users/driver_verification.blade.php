@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Driver Verification</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('lang.home') }}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('drivers.verifications.index') }}">Driver Verification</a></li>
      <li class="breadcrumb-item active">{{ $driver->name }}</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Driver Details</h5>

          @if ($message = Session::get('success'))
          <div class="alert alert-success">
            <p>{{ $message }}</p>
          </div>
          @endif

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ trans('lang.name') }}</strong>
              <div>{{ $driver->name }}</div>
            </div>
            <div class="col-md-6">
              <strong>{{ trans('lang.mobile') }}</strong>
              <div>{{ $driver->mobile }}</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ trans('lang.email') }}</strong>
              <div>{{ $driver->email ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <strong>Vehicle Type</strong>
              <div>{{ $driver->category_id ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Number Plate</strong>
              <div>{{ $driver->number_plate ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <strong>Driving License</strong>
              <div>{{ $driver->driving_license ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Bank Account</strong>
              <div>{{ $driver->bank_account ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <strong>IBAN</strong>
              <div>{{ $driver->iban ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>Current Verification Status</strong>
              <div>{{ ucfirst($driver->verification_status ?? 'pending') }}</div>
            </div>
            <div class="col-md-6">
              <strong>Verified At</strong>
              <div>{{ $driver->verified_at ?? 'N/A' }}</div>
            </div>
          </div>

          {!! Form::model($driver, ['method' => 'PATCH', 'route' => ['drivers.verifications.update', $driver->id]]) !!}
          <div class="mb-3">
            <label class="form-label" for="verification_status">Verification Status</label>
            <select class="form-control" name="verification_status" id="verification_status" required>
              <option value="pending" {{ ($driver->verification_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="verified" {{ ($driver->verification_status ?? '') === 'verified' ? 'selected' : '' }}>Verified</option>
              <option value="rejected" {{ ($driver->verification_status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label" for="verification_notes">Admin Notes</label>
            <textarea class="form-control" name="verification_notes" id="verification_notes" rows="4">{{ old('verification_notes', $driver->verification_notes) }}</textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Verification</button>
            <a href="{{ route('drivers.verifications.index') }}" class="btn btn-light">Back</a>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Uploaded Documents</h5>

          <div class="mb-4">
            <strong>Driving License Image</strong>
            <div class="mt-2">
              @if ($driver->driving_license_image)
              <a href="{{ asset('images/' . $driver->driving_license_image) }}" target="_blank" rel="noopener">
                <img src="{{ asset('images/' . $driver->driving_license_image) }}" alt="Driving License" class="img-fluid rounded border">
              </a>
              @else
              <div class="text-muted">No driving license image uploaded.</div>
              @endif
            </div>
          </div>

          <div>
            <strong>Vehicle Registration Image</strong>
            <div class="mt-2">
              @if ($driver->vehicle_registration_image)
              <a href="{{ asset('images/' . $driver->vehicle_registration_image) }}" target="_blank" rel="noopener">
                <img src="{{ asset('images/' . $driver->vehicle_registration_image) }}" alt="Vehicle Registration" class="img-fluid rounded border">
              </a>
              @else
              <div class="text-muted">No vehicle registration image uploaded.</div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection