@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>{{ $user->name }}'s Bank Accounts</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{trans('lang.user_list')}}</a></li>
      <li class="breadcrumb-item active">Bank Accounts</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Bank Accounts</h5>
          <a class="btn btn-secondary mb-3" href="{{ route('users.index') }}">
            <i class="bi bi-arrow-left"></i> Back to Users
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

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th width="50px">#</th>
                  <th>Bank Name</th>
                  <th>Account Holder</th>
                  <th>Account Number</th>
                  <th>Status</th>
                  <th>Verified By</th>
                  <th>Verified At</th>
                  <th>Primary</th>
                  <th width="200px">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($bankAccounts as $key => $account)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                      <strong>{{ $account['bank_name'] }}</strong>
                      @if ($account['branch_code'])
                        <br><small class="text-muted">{{ $account['branch_code'] }}</small>
                      @endif
                    </td>
                    <td>{{ $account['account_holder_name'] }}</td>
                    <td>
                      <code>{{ $account['account_number'] }}</code>
                      @if ($account['iban'])
                        <br><small class="text-muted">IBAN: {{ $account['iban'] }}</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge 
                        @if($account['verification_status'] === 'verified') bg-success
                        @elseif($account['verification_status'] === 'rejected') bg-danger
                        @else bg-warning text-dark
                        @endif
                      ">
                        <i class="bi 
                          @if($account['verification_status'] === 'verified') bi-check-circle
                          @elseif($account['verification_status'] === 'rejected') bi-x-circle
                          @else bi-clock
                          @endif
                        "></i>
                        {{ ucfirst($account['verification_status']) }}
                      </span>
                    </td>
                    <td>
                      @if ($account['verified_by'])
                        <small>Admin</small>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if ($account['verified_at'])
                        <small>{{ \Carbon\Carbon::parse($account['verified_at'])->format('M d, Y') }}</small>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if ($account['is_primary'])
                        <span class="badge bg-primary">Primary</span>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if ($account['verification_status'] === 'pending')
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" 
                          data-bs-target="#verifyModal{{ $account['id'] }}">
                          <i class="bi bi-check"></i> Verify
                        </button>
                      @endif
                      <a class="btn btn-sm btn-info" href="#" data-bs-toggle="modal" 
                        data-bs-target="#detailsModal{{ $account['id'] }}">
                        <i class="bi bi-eye"></i> View
                      </a>
                    </td>
                  </tr>

                  <!-- Verify Modal -->
                  <div class="modal fade" id="verifyModal{{ $account['id'] }}" tabindex="-1">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Verify Bank Account</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('bank-accounts.verify', $account['id']) }}">
                          @csrf
                          @method('PATCH')
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label">Account Details</label>
                              <div class="card">
                                <div class="card-body">
                                  <p><strong>Bank:</strong> {{ $account['bank_name'] }}</p>
                                  <p><strong>Holder:</strong> {{ $account['account_holder_name'] }}</p>
                                  <p><strong>Number:</strong> {{ $account['account_number_full'] }}</p>
                                  @if ($account['iban'])
                                    <p><strong>IBAN:</strong> {{ $account['iban'] }}</p>
                                  @endif
                                </div>
                              </div>
                            </div>

                            <div class="mb-3">
                              <label for="status{{ $account['id'] }}" class="form-label">Verification Status <span class="text-danger">*</span></label>
                              <select class="form-select" id="status{{ $account['id'] }}" name="verification_status" required>
                                <option value="">-- Select Status --</option>
                                <option value="verified">Verify Account</option>
                                <option value="rejected">Reject Account</option>
                              </select>
                            </div>

                            <div class="mb-3">
                              <label for="notes{{ $account['id'] }}" class="form-label">Notes <span class="text-danger">*</span></label>
                              <textarea class="form-control" id="notes{{ $account['id'] }}" name="verification_notes" 
                                rows="3" placeholder="Enter verification notes..." required></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Confirm Verification</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Details Modal -->
                  <div class="modal fade" id="detailsModal{{ $account['id'] }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Bank Account Details</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row mb-3">
                            <div class="col-md-6">
                              <strong>Bank Name:</strong>
                              <p>{{ $account['bank_name'] }}</p>
                            </div>
                            <div class="col-md-6">
                              <strong>Branch Code:</strong>
                              <p>{{ $account['branch_code'] ?? 'N/A' }}</p>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col-md-6">
                              <strong>Account Holder Name:</strong>
                              <p>{{ $account['account_holder_name'] }}</p>
                            </div>
                            <div class="col-md-6">
                              <strong>Account Number:</strong>
                              <p><code>{{ $account['account_number_full'] }}</code></p>
                            </div>
                          </div>

                          @if ($account['iban'])
                            <div class="row mb-3">
                              <div class="col-md-12">
                                <strong>IBAN:</strong>
                                <p>{{ $account['iban'] }}</p>
                              </div>
                            </div>
                          @endif

                          <hr>

                          <div class="row">
                            <div class="col-md-6">
                              <strong>Status:</strong>
                              <p>
                                <span class="badge 
                                  @if($account['verification_status'] === 'verified') bg-success
                                  @elseif($account['verification_status'] === 'rejected') bg-danger
                                  @else bg-warning text-dark
                                  @endif
                                ">
                                  {{ ucfirst($account['verification_status']) }}
                                </span>
                              </p>
                            </div>
                            <div class="col-md-6">
                              <strong>Primary:</strong>
                              <p>{{ $account['is_primary'] ? 'Yes' : 'No' }}</p>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <strong>Submitted:</strong>
                              <p>{{ \Carbon\Carbon::parse($account['created_at'])->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                              <strong>Verified At:</strong>
                              <p>{{ $account['verified_at'] ? \Carbon\Carbon::parse($account['verified_at'])->format('M d, Y H:i') : 'Pending' }}</p>
                            </div>
                          </div>

                          @if ($account['verification_notes'])
                            <div class="row mt-3">
                              <div class="col-md-12">
                                <strong>Verification Notes:</strong>
                                <p class="alert alert-info">{{ $account['verification_notes'] }}</p>
                              </div>
                            </div>
                          @endif
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                      <i class="bi bi-inbox"></i> No bank accounts found
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
