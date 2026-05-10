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
            <a class="btn btn-success" href="{{ route('users.create') }}"> {{trans('lang.create_new_user')}}</a>
       


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<table class="table table-bordered">
 <tr>
   <th>{{trans('lang.number')}}</th>
   <th>{{trans('lang.name')}}</th>
   <th>{{trans('lang.email')}}</th>
   <th>{{trans('lang.user_type')}}</th>
   <th>{{trans('lang.status')}}</th>
   <th>Verification</th>
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($buyers as $key => $user)
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ \App\Models\User::roleLabel($user->user_type) }}</td>
    <td>
       <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
         {{ $user->status ? trans('lang.active') : 'Inactive' }}
       </span>
    </td>
    <td>
      @if ((int) $user->user_type === 2)
      @php $status = $user->verification_status ?? 'pending'; @endphp
      <span class="badge {{ $status === 'verified' ? 'bg-success' : ($status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
        {{ ucfirst($status) }}
      </span>
      @else
      <span class="text-muted">N/A</span>
      @endif
    </td>
    <td>
       <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">{{trans('lang.edit')}}</a>
       <a class="btn btn-info" href="{{ route('users.bank-accounts',$user->id) }}">Bank Accounts</a>
       @if ((int) $user->user_type === 2)
       <a class="btn btn-info" href="{{ route('drivers.verifications.show', $user->id) }}">Review</a>
       @endif
       @if($user->status == 0)
       <a class="btn btn-warning text-center" href="{{ route('sellers_active',$user->id) }}">Activate</a>
       @else
       <a class="btn btn-secondary text-center" href="{{ route('sellers_inactive',$user->id) }}">Deactivate</a>
       @endif
        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</table>


{{ $buyers->onEachSide(1)->links('vendor.pagination.default') }}


        </div>
      </div>
    </div>
</div>
      </section>
@endsection