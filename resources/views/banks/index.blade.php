@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>Banks Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.management')}}</li>
      <li class="breadcrumb-item active">Banks</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Banks List</h5>
          <a class="btn btn-success mb-3" href="{{ route('banks.create') }}"> 
            <i class="bi bi-plus-circle"></i> Add New Bank
          </a>

          @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-1"></i>
              {{ $message }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-circle me-1"></i>
              {{ $message }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th width="50px">#</th>
                <th>Bank Name</th>
                <th>Branch Code</th>
                <th>Status</th>
                <th width="280px">Actions</th>
              </tr>
            </thead>
            <tbody>
              @php
                $page = $_GET['page'] ?? 1;
                $i = ($page * $perPage) - $perPage;
              @endphp

              @forelse ($banks as $bank)
                <tr>
                  <td>{{ ++$i }}</td>
                  <td>{{ $bank->name }}</td>
                  <td>{{ $bank->branch_code ?? 'N/A' }}</td>
                  <td>
                    <span class="badge {{ $bank->status ? 'bg-success' : 'bg-secondary' }}">
                      <i class="bi {{ $bank->status ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                      {{ $bank->status ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-primary" href="{{ route('banks.edit', $bank->id) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>

                    @if ($bank->status == 0)
                      <a class="btn btn-sm btn-warning" href="{{ route('banks.active', $bank->id) }}">
                        <i class="bi bi-check"></i> Activate
                      </a>
                    @else
                      <a class="btn btn-sm btn-secondary" href="{{ route('banks.inactive', $bank->id) }}">
                        <i class="bi bi-x"></i> Deactivate
                      </a>
                    @endif

                    {!! Form::open([
                      'method' => 'DELETE',
                      'route' => ['banks.destroy', $bank->id],
                      'style' => 'display:inline',
                      'onsubmit' => 'return confirm("Are you sure you want to delete this bank?")',
                    ]) !!}
                      {!! Form::submit('Delete', ['class' => 'btn btn-sm btn-danger']) !!}
                    {!! Form::close() !!}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox"></i> No banks found
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>

          {{ $banks->onEachSide(1)->links('vendor.pagination.default') }}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
