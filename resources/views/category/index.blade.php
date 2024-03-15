@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.category_list')}}</h1>
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
   <th>{{trans('lang.name')}}</th>
   <th>{{trans('lang.image')}}</th>
   <th width="280px">{{trans('lang.action')}}</th>
 </tr>
 @php
 $i =1;
 @endphp
 
 @foreach ($category as $key => $item)
  <tr>
    <td>{{ ++$i }}</td>
    <td>{{ $item->name }}</td>
    <td>
      <img src="{{asset('uploads').'/'.$item->image}}" style="width:100px;height:100px" alt="">
    </td>

    <td>
       <!-- <a class="btn btn-info" href="{{ route('category.show',$item->id) }}">Show</a> -->
       <a class="btn btn-primary" href="{{ route('category.edit',$item->id) }}">{{trans('lang.edit')}}</a>
       <a class="<?= $item->admin_choice == 1 ? "btn btn-success": 'btn btn-warning'?>" href="{{ route('category.edit',[$item->id, "choice" => $item->admin_choice, "id" => $item->id]) }}">{{trans('lang.like')}}</a>
        {!! Form::open(['method' => 'DELETE','route' => ['category.destroy', $item->id],'style'=>'display:inline']) !!}
            {!! Form::submit(trans('lang.delete'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</table>




</div>
      </div>
    </div>
</div>
      </section>
@endsection