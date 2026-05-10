@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Add New Bank</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.management')}}</li>
      <li class="breadcrumb-item"><a href="{{ route('banks.index') }}">Banks</a></li>
      <li class="breadcrumb-item active">Create</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Create New Bank</h5>
          <a class="btn btn-secondary mb-3" href="{{ route('banks.index') }}">
            <i class="bi bi-arrow-left"></i> Back
          </a>

          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <h4 class="alert-heading">
                <i class="bi bi-exclamation-circle me-1"></i>
                Validation Error
              </h4>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          {!! Form::open([
            'route' => 'banks.store',
            'method' => 'POST',
            'enctype' => 'multipart/form-data',
            'class' => 'row g-3'
          ]) !!}

            <div class="col-12">
              <label for="name" class="form-label">Bank Name <span class="text-danger">*</span></label>
              <input 
                type="text" 
                id="name"
                name="name" 
                class="form-control @error('name') is-invalid @enderror"
                placeholder="Enter bank name (e.g., First National Bank)"
                value="{{ old('name') }}" 
                required
              >
              @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="branch_code" class="form-label">Branch Code</label>
              <input 
                type="text" 
                id="branch_code"
                name="branch_code" 
                class="form-control"
                placeholder="Enter branch code (optional)"
                value="{{ old('branch_code') }}"
              >
            </div>

            <div class="col-12">
              <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
              <select 
                id="status" 
                name="status" 
                class="form-select @error('status') is-invalid @enderror"
                required
              >
                <option value="">-- Select Status --</option>
                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>
                  <i class="bi bi-check-circle"></i> Active
                </option>
                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                  <i class="bi bi-x-circle"></i> Inactive
                </option>
              </select>
              @error('status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Create Bank
              </button>
              <a href="{{ route('banks.index') }}" class="btn btn-light">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
            </div>

          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
