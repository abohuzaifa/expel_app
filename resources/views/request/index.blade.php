@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.request_list')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
    </ol>
  </nav>
</div>
  <section class="section">
<div class="row">
<div class="col-lg-12">
  <div class="card">
      <div class="card-body">
          <h5 class="card-title"></h5>
@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger">
  <p>{{ $message }}</p>
</div>
@endif

<!-- Search and Filter -->
<div class="row mb-3">
  <div class="col-md-6">
    <form action="{{ route('request.index') }}" method="GET" class="d-flex gap-2">
      <input type="text" name="search" class="form-control" placeholder="{{ trans('lang.search') }}..." value="{{ request('search') }}">
      <select name="status" class="form-select" style="width: auto;">
        <option value="">{{ trans('lang.all_status') }}</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ trans('lang.pending') }}</option>
        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ trans('lang.processing') }}</option>
        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ trans('lang.cancel') }}</option>
        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>{{ trans('lang.complete') }}</option>
        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>{{ trans('lang.processing') }}</option>
      </select>
      <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
      <a href="{{ route('request.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
    </form>
  </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-striped table-hover">
  <thead class="table-dark">
   <tr>
     <th>{{trans('lang.number')}}</th>
     <th>{{trans('lang.from')}}</th>
     <th>{{trans('lang.to')}}</th>
     <th>{{trans('lang.user')}}</th>
     <th>{{trans('lang.driver')}}</th>
     <th>{{trans('lang.amount')}}</th>
     <th>{{trans('lang.status')}}</th>
     <th width="280px">{{trans('lang.action')}}</th>
   </tr>
  </thead>
  <tbody>
  @php
  $page = request('page', 1);
  $i = ($page * $perPage) - $perPage;
  @endphp
  
  @forelse ($requests as $key => $item)
   <tr>
     <td>{{ ++$i }}</td>
     <td>
       <small>{{ $item->parcel_address }}</small>
       @if($item->from_city)
         <br><span class="badge bg-info">{{ $item->fromCity->name ?? $item->from_city }}</span>
       @endif
     </td>
     <td>
       <small>{{ $item->receiver_address }}</small>
       @if($item->to_city)
         <br><span class="badge bg-info">{{ $item->toCity->name ?? $item->to_city }}</span>
       @endif
     </td>
     <td>
       @if($item->user)
         <strong>{{ $item->user->name }}</strong>
         <br><small class="text-muted">{{ $item->user->mobile ?? '' }}</small>
       @else
         <span class="text-muted">N/A</span>
       @endif
     </td>
     <td>
       @if(isset($item->offer->user))
         <strong>{{ $item->offer->user->name }}</strong>
         <br><small class="text-muted">{{ $item->offer->user->mobile ?? '' }}</small>
       @else
         <span class="text-muted">—</span>
       @endif
     </td>
     <td>
       @if($item->amount)
         <strong>${{ number_format($item->amount, 2) }}</strong>
       @else
         <span class="text-muted">—</span>
       @endif
     </td>
     <td>
       @php
         $status = $item->status;
         $badgeClass = match($status) {
           0 => 'bg-warning text-dark',
           1, 4 => 'bg-primary',
           2 => 'bg-danger',
           3 => 'bg-success',
           default => 'bg-secondary'
         };
         $statusText = match($status) {
           0 => trans('lang.pending'),
           1, 4 => trans('lang.processing'),
           2 => trans('lang.cancel'),
           3 => trans('lang.complete'),
           default => trans('lang.unknown')
         };
       @endphp
       <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
       @if($item->payment_status == 1)
         <br><span class="badge bg-success mt-1">{{ trans('lang.paid') }}</span>
       @elseif($item->payment_status == 0 && $item->amount > 0)
         <br><span class="badge bg-warning text-dark mt-1">{{ trans('lang.unpaid') }}</span>
       @endif
     </td>
     <td>
       <div class="btn-group" role="group">
         <a class="btn btn-info btn-sm" href="{{ route('request.show',$item->id) }}" title="{{ trans('lang.view') }}">
           <i class="bi bi-eye"></i>
         </a>
         @if($item->status == 3 && $item->latestHistory)
           @php
             $googleMapsUrl = "https://www.google.com/maps?q={$item->latestHistory->lat},{$item->latestHistory->long}";
           @endphp
           <a class="btn btn-primary btn-sm" target="_blank" href="{{ $googleMapsUrl }}" title="{{ trans('lang.tracking') }}">
             <i class="bi bi-geo-alt"></i>
           </a>
         @endif
       </div>
     </td>
   </tr>
  @empty
   <tr>
     <td colspan="8" class="text-center">{{ trans('lang.no_records_found') }}</td>
   </tr>
  @endforelse
  </tbody>
</table>
</div>
{{ $requests->onEachSide(1)->appends(request()->query())->links('vendor.pagination.default') }}

</div>
      </div>
    </div>
</div>
</section>
@endsection
