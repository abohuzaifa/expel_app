@extends('layouts.app')


@section('content')
<div class="pagetitle">
    <h1>{{trans('lang.create_user')}}</h1>
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
                        <a class="btn btn-primary" href="{{ route('users.index') }}"> {{trans('lang.back')}}</a>
       


                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                        </div>
                        @endif



{!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-3" style="text-align: right;">
                <div class="form-group">
                    <input style="height: 15px; width:15px;" type="checkbox"  name="is_seller" id="is_seller" value="1" checked onchange="check_box()">
                    <strong>{{trans('lang.is_seller')}}</strong>
                </div>
            </div>

            <div class="col-3" style="text-align: right;">
                <div class="form-group">
                    <input style="height: 15px; width:15px;" type="checkbox"  name="is_buyer" id="is_buyer" value="1" onchange="check_box1()">
                    <strong>{{trans('lang.is_buyer')}}</strong>
                </div>
            </div>

            <div class="col-3" style="text-align: right;">
                <div class="form-group">
                    <input style="height: 15px; width:15px;" type="checkbox"  name="is_admin" id="is_admin" value="1" onchange="check_box2()">
                    <strong>{{trans('lang.is_admin')}}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.name')}}:</strong>
            {!! Form::text('name', null, array('placeholder' => trans('lang.name'),'class' => 'form-control', "required" =>"required")) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.mobile')}}:</strong>
            {!! Form::text('mobile', null, array('placeholder' => trans('lang.mobile'),'class' => 'form-control', "required" =>"required")) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.email')}}:</strong>
            {!! Form::text('email', null, array('placeholder' => trans('lang.email'),'class' => 'form-control', "required" =>"required")) !!}
        </div>
    </div>
    <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="image">{{trans('lang.image')}}:</label>
            <input type="file" class="form-control" name="image">
        </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.password')}}:</strong>
            {!! Form::password('password', array('placeholder' => trans('lang.password'),'class' => 'form-control', "required" =>"required")) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.confirm_password')}}:</strong>
            {!! Form::password('confirm-password', array('placeholder' => trans("lang.confirm_password"),'class' => 'form-control', "required" =>"required")) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12  seller category">
        <div class="form-group">
            <strong>{{trans('lang.category')}}:</strong>
            {!! Form::select('category', $category,[], array('class' => 'form-control sel req', "required" =>"required")) !!}
        </div>
    </div>

        <div  class="col-xs-12 col-sm-12 col-md-12 shop seller ">
            <label for="shop_name">{{trans('lang.shop_name')}}:</label>
            <input type="text" class="form-control sel req" name="shop_name" value="{{ old('shop_name') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12 reg_no seller ">
            <label for="reg_no">{{trans('lang.registration_no')}}:</label>
            <input type="text" class="form-control sel req" name="reg_no" value="{{ old('reg_no') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="street_address">{{trans('lang.street_address')}}:</label>
            <input type="text" class="form-control" name="street_address" value="{{ old('street_address') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="city">{{trans('lang.city')}}:</label>
            <input type="text" class="form-control" name="city" value="{{ old('city') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="state">{{trans('lang.state')}}:</label>
            <input type="text" class="form-control" name="state" value="{{ old('state') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="postal_code">{{trans('lang.postal_code')}}:</label>
            <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="latitude">{{trans('lang.latitude')}}:</label>
            <input type="text" class="form-control sel req" name="latitude" value="{{ old('latitude') }}" required>
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12 seller buyer ">
            <label for="longitude">{{trans('lang.longitude')}}:</label>
            <input type="text" class="form-control sel req" name="longitude" value="{{ old('longitude') }}" required>
        </div>


    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><br>
        <button type="submit" class="btn btn-primary submit">{{trans('lang.submit')}}</button>
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
    // $(document).ready(function() {
        // alert(123);
      // Attach click event handler to checkboxes by ID
      function check_box()
      {
        if ($("#is_seller").prop('checked', true)) {
          $(".seller").removeClass("d-none");
          $('#is_buyer, #is_admin').not(this).prop('checked', false);
        }
      }
      function check_box1()
      {
        if ($("#is_buyer").prop('checked', true)) {
            $(".seller").addClass("d-none");
            $(".buyer").removeClass("d-none");
            $(".sel").removeAttr("required");
          $('#is_seller, #is_admin').not(this).prop('checked', false);
        }
      }
      function check_box2()
      {
        if ($("#is_admin").prop('checked', true)) {
            $(".category").addClass("d-none");
          $(".buyer").addClass("d-none");
          $(".seller").addClass("d-none");
          
          $(".sel").removeAttr("required");
            $(".req").removeAttr("required");
          $('#is_seller, #is_buyer').not(this).prop('checked', false);
        }
      }
     
    // });
  </script>