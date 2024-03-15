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
            <a class="btn btn-success" href="{{ route('payment_method.create') }}"> {{trans('lang.create_new')}}</a>
       


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<table class="table table-bordered">
 <tr>
   <th>{{trans('lang.number')}}</th>
   <th>{{trans('lang.name')}}</th>
   <th>{{trans('lang.slug')}}</th>
   <th>{{trans('lang.status')}}</th>
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($payments as $key => $payment)

  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $payment->name }}</td>
    <td>{{ $payment->slug }}</td>
    <td>
       @if($payment->status == 0)
       <a class="btn btn-warning text-center" href="{{ route('active',$payment->id) }}">{{trans('lang.deactive')}}</a>
       @else
       <a class="btn btn-success text-center" href="{{ route('inactive',$payment->id) }}">{{trans('lang.active')}}</a>
       @endif
    </td>
    
    <td>
       <a class="btn btn-primary" href="{{ route('payment_method.edit',$payment->id) }}">{{trans('lang.edit')}}</a>   
       {!! Form::open(['method' => 'DELETE','route' => ['payment_method.destroy', $payment->id],'style'=>'display:inline']) !!}
            {!! Form::submit(trans('lang.delete'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!} 
    </td>
  </tr>
 @endforeach
</table>


{{ $payments->onEachSide(1)->links('vendor.pagination.default') }}


        </div>
      </div>
    </div>
</div>
      </section>
@endsection