@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.banner_list')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.banner_list')}}</li>
    </ol>
  </nav>
</div>
  <section class="section">
<div class="row">
<div class="col-lg-12">
  <div class="card">
      <div class="card-body">
          <h5 class="card-title"></h5>
          <a class="btn btn-success" href="{{ route('banners.create') }}"> {{trans('lang.create_new_banner')}}</a>
@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<table class="table table-bordered">
 <tr>
   <th>{{trans('lang.number')}}</th>
   <!-- <th>Name</th> -->
   <th>{{trans('lang.image')}}</th>
   <th>{{trans('lang.status')}}</th>
   <!-- <th>price</th> -->
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($banner as $key => $item)
  <tr>
    <td>{{ ++$i }}</td>
    <!-- <td>{{ $item->p_name }}</td> -->
    <td>
    <img src="{{asset('images/'.$item->image)}}" style="width:100px;height:100px" alt="">
    </td>
    <td>
       @if($item->status == 0)
       <a class="btn btn-warning text-center" href="{{ route('banner_active',$item->id) }}">{{trans('lang.deactive')}}</a>
       @else
       <a class="btn btn-success text-center" href="{{ route('banner_inactive',$item->id) }}">{{trans('lang.active')}}</a>
       @endif</td>
       <td>
       <!-- <a class="btn btn-info" href="{{ route('product.show',$item->id) }}">Show</a> -->
       <a class="btn btn-primary" href="{{ route('banners.edit',$item->id) }}">{{trans('lang.edit')}}</a>
        {!! Form::open(['method' => 'DELETE','route' => ['banners.destroy', $item->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</table>
{{ $banner->onEachSide(1)->links('vendor.pagination.default') }}



</div>
      </div>
    </div>
</div>
      </section>
@endsection