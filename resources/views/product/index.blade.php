@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.product_list')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.product_list')}}</li>
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
   <th>{{trans('lang.name')}}</th>
   <th>{{trans('lang.image')}}</th>
   <th>{{trans('lang.description')}}</th>
   <th>{{trans('lang.shop')}}</th>
   <th>{{trans('lang.price')}}</th>
   <th width="280px">{{trans('lang.Action')}}</th>
 </tr>
 @php
 //echo "<pre>";print_r($perPage); exit;
 $page = $_GET['page'] ?? 1;
 $i = ($page*$perPage)-$perPage;
 @endphp
 @foreach ($Items as $key => $item)
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $item->p_name }}</td>
    <td>
      <?php  $images = json_decode($item->images, true);
            if(is_array($images))
            {
              foreach($images as $image)
              {
                echo '
                <img src="'.asset('images/'.$image).'" style="width:100px;height:100px" alt="">';
              }
            }
      ?>
    </td>
    
    <td>{{ $item->description }}</td>
    <td>{{ $item->shop_name }}</td>
    <td>{{ $item->price }}</td>
    <td>
       <!-- <a class="btn btn-info" href="{{ route('product.show',$item->id) }}">Show</a> -->
       <a class="btn btn-primary" href="{{ route('product.edit',$item->id) }}">{{trans('lang.edit')}}</a>
        {!! Form::open(['method' => 'DELETE','route' => ['product.destroy', $item->id],'style'=>'display:inline']) !!}
            {!! Form::submit(trans('lang.delete'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</table>
{{ $Items->onEachSide(1)->links('vendor.pagination.default') }}



</div>
      </div>
    </div>
</div>
      </section>
@endsection