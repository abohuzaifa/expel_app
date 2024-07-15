<?php //use App\Models\Offer; 

use App\Models\History;
use App\Models\Offer;

?>
@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.shop_list')}}</h1>
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


<table class="table table-bordered">
 <tr>
   <th>{{trans('lang.number')}}</th>
   <th>{{trans('lang.from')}}</th>
   <th>{{trans('lang.to')}}</th>
   <th>{{trans('lang.user')}}</th>
   <th>{{trans('lang.driver')}}</th>
   <th>{{trans('lang.status')}}</th>
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 
 @foreach ($requests as $key => $item)
 <?php 
 $requestId = $item->id; // or the specific request_id you are looking for

 $history = History::where('request_id', $requestId)
                   ->orderBy('created_at', 'desc') // assuming you have a created_at timestamp
                   ->first();
 ?>
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $item->parcel_address }}</td>
    <td>{{ $item->receiver_address }}</td>
    <td>{{ $item->user->name }}</td>
    <td>{{ isset($item->offer->user->name ) ? $item->offer->user->name  : ""}}</td>
    <td>{{ ($item->status == 0 ? trans("lang.pending") : ($item->status == 1 || $item->status == 4 ? trans("lang.processing") : ($item->status == 2 ? trans("lang.cancel") : trans("lang.complete")))) }}</td>
    <td>
      <a class="btn btn-info" href="{{ route('request.show',$item->id) }}">{{trans('lang.view')}}</a>
      <?php if($history && $item->status == 3) {
        $googleMapsUrl = "https://www.google.com/maps?q={$history->lat},{$history->long}";
        echo '<a class="btn btn-primary" target="_blank" href="'.$googleMapsUrl.'">'.trans('lang.tracking').'</a>';
      } ?>
      
    </td>
    
  </tr>
 @endforeach
</table>
{{ $requests->onEachSide(1)->links('vendor.pagination.default') }}




</div>
      </div>
    </div>
</div>
</section>
@endsection