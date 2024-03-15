@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans("lang.user_list")}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans("lang.home")}}</a></li>
      <li class="breadcrumb-item">{{trans("lang.forms")}}</li>
      <li class="breadcrumb-item active">{{trans("lang.elements")}}</li>
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


<table class="table table-bordered">
 <tr class="text-center">
   <th>{{trans("lang.number")}}</th>
   <th>{{trans("lang.name")}}</th>
   <th>{{trans("lang.email")}}</th>
   <th>{{trans("lang.mobile")}}</th>
   <th width="280px">{{trans("lang.action")}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($sellers as $key => $user)
  <tr>
    <td class="text-center">{{ ++$i }}</td>
    <td class="text-center">{{ $user->name }}</td>
    <td class="text-center">{{ $user->email }}</td>
    <td class="text-center">
        {{ $user->mobile }}
    </td>
    <td class="text-center">
        @if($user->status == 0)
       <a class="btn btn-warning text-center" href="{{ route('sellers_active',$user->id) }}">{{trans("lang.deactive")}}</a>
       @else
       <a class="btn btn-success text-center" href="{{ route('sellers_inactive',$user->id) }}">{{trans("lang.active")}}</a>
       @endif


    </td>
  </tr>
 @endforeach
</table>
{{ $sellers->onEachSide(1)->links('vendor.pagination.default') }}





        </div>
      </div>
    </div>
</div>
      </section>
@endsection
