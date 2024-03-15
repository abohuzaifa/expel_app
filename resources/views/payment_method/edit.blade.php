@extends('layouts.app')


@section('content')
<div class="pagetitle">
    <h1>{{trans('lang.update_payment_method')}}</h1>
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
                        <a class="btn btn-primary" href="{{ route('payment_method.index') }}"> {{trans('lang.back')}}</a>
       


                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                        <p class="msg">{{ $message }}</p>
                        </div>
                        @endif
                        <div class="alert alert-danger d-none">
                        <p class="msg"></p>
                        </div>



{!! Form::model($payment, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['payment_method.update', $payment->id]]) !!}
<div class="row">

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control" required value="{{$payment->name}}">
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="slug">{{trans('lang.slug')}}:</label>
            <select type="text" class="form-control" name="slug">
                <option {{$payment->slug == 'click_pay' ? "selected" : ""}} value="click_pay">{{trans('lang.click_pay')}}</option>
                <option {{$payment->slug == 'COD' ? "selected" : ""}} value="COD">{{trans('lang.cod')}}</option>
            </select>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="public_key">{{trans('lang.public_key')}}:</label>
            <input type="text" class="form-control" name="public_key" value="{{$payment->public_key}}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="secret_key">{{trans('lang.secret_key')}}:</label>
            <input type="text" class="form-control" name="secret_key" value="{{$payment->secret_key}}">
        </div><br>

  
    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><br>
        <button type="submit" class="btn btn-primary">{{ trans('lang.submit') }}</button>
    </div>
</div>
{!! Form::close() !!}


        </div>
    </div>
</div>
</div>
    </section>

@endsection
<script>

  </script>