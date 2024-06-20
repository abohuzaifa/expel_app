
@extends('layouts.app')


@section('content')
<style>
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 2rem;
    }

    .card-header {
        background-color: #4e73df;
        color: white;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .card-header h3 {
        margin: 0;
    }

    .card-body {
        padding: 2rem;
    }

    .card-body h4 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #4e73df;
    }

    .card-body p {
        margin: 0.5rem 0;
    }

    .card-body p strong {
        display: inline-block;
        min-width: 150px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }
</style>
<div class="pagetitle">
  <h1>{{trans('lang.request_view')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
    </ol>
  </nav>
</div>
<section class="section">
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>{{trans('lang.request_details')}}</h3>
        </div>
        <div class="card-body">
            <h4>{{trans('lang.request_information')}}</h4>
            <p><strong>{{trans('lang.id')}}:</strong> {{ $request->id }}</p>
            <p><strong>{{trans('lang.user_id')}}:</strong> {{ $request->user_id }}</p>
            <p><strong>{{trans('lang.from_date')}}:</strong> {{ $request->from_date }}</p>
            <p><strong>{{trans('lang.to_date')}}:</strong> {{ $request->to_date }}</p>
            <p><strong>{{trans('lang.parcel_address')}}:</strong> {{ $request->parcel_address }}</p>
            <p><strong>{{trans('lang.receiver_address')}}:</strong> {{ $request->receiver_address }}</p>
            <p><strong>{{trans('lang.receiver_mobile')}}:</strong> {{ $request->receiver_mobile }}</p>
            <p><strong>{{trans('lang.status')}}:</strong> {{ ($request->status == 0 ? trans("lang.pending") : ($request->status == 1 || $request->status == 4 ? trans("lang.processing") : ($request->status == 2 ? trans("lang.cancel") : trans("lang.complete")))) }}</p>
            <p><strong>{{trans('lang.payment_status')}}:</strong> {{ $request->payment_status == 1 ? trans('lang.paid') : trans('lang.unpaid') }}</p>
            <p><strong>{{trans('lang.amount')}}:</strong> ${{ $request->amount }}</p>
            
            @if($request->offer)
            <h4>{{trans('lang.offer_information')}}</h4>
            <p><strong>{{trans('lang.offer_id')}}:</strong> {{ $request->offer->id }}</p>
            <p><strong>{{trans('lang.offer_amount')}}:</strong> ${{ $request->offer->amount }}</p>
            <p><strong>{{trans('lang.driver_id')}}:</strong> {{ $request->offer->user_id }}</p>
            <p><strong>{{trans('lang.driver_name')}}:</strong> {{ $request->offer->user->name }}</p>
            <p><strong>{{trans('lang.driver_mobile')}}:</strong> {{ $request->offer->user->mobile }}</p>
            <p><strong>{{trans('lang.driver_license_no')}}:</strong> {{ $request->offer->user->driving_license }}</p>
            <p><strong>{{trans('lang.driver_vehicle_no')}}:</strong> {{ $request->offer->user->number_plate }}</p>
            @endif

            @if($request->user)
            <h4>{{trans('lang.user_information')}}</h4>
            <p><strong>{{trans('lang.name')}}:</strong> {{ $request->user->name }}</p>
            <p><strong>{{trans('lang.email')}}:</strong> {{ $request->user->email }}</p>
            <p><strong>{{trans('lang.mobile')}}:</strong> {{ $request->user->mobile }}</p>
            @endif
        </div>
    </div>
</div>
</section>
@endsection