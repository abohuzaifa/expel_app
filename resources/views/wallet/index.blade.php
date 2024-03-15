@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.user_list')}}</h1>
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
            <!-- <a class="btn btn-success" href="{{ route('users.create') }}"> {{trans('lang.create_new_user')}}</a> -->
       


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<table class="table table-bordered">
 <tr>
   <th>{{trans('lang.number')}}</th>
   <th>{{trans('lang.name')}}</th>
   <th>{{trans('lang.wallet_amount')}}</th>
   <th>{{trans('lang.user_type')}}</th>
   <th>{{trans('lang.status')}}</th>
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($wallets as $key => $wallet)
 @php
    $user = DB::table('users')->where('id', $wallet->user_id)->get()[0];
 @endphp
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $wallet->amount }}</td>
    <td>{{$user->user_type == 0 ? "ADMIN" : ($user->user_type == 1? "SELLER" : "BUYER")}}</td>
    <td>
       @if($user->status == 0)
       <a class="btn btn-warning text-center" >{{trans('lang.deactive')}}</a>
       @else
       <a class="btn btn-success text-center" >{{trans('lang.active')}}</a>
       @endif</td>
    <td>
       <!-- <a class="btn btn-info" href="{{ route('users.show',$user->id) }}">Show</a> -->
       <!-- <a class="btn btn-primary" href="{{ route('wallet.edit',$wallet->id) }}">{{trans('lang.recharge')}}</a> -->
       <a class="btn btn-primary" href="{{ route('wallet.edit',$wallet->id) }}">{{trans('lang.update_wallet')}}</a>
        
    </td>
  </tr>
 @endforeach
</table>


{{ $wallets->onEachSide(1)->links('vendor.pagination.default') }}


        </div>
      </div>
    </div>
</div>
      </section>
@endsection