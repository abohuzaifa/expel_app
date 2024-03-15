@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.payment_method_create')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.create')}}</li>
    </ol>
  </nav>
</div>
  <section class="section">
<div class="row">
<div class="col-lg-12">
  <div class="card">
      <div class="card-body">
          <h5 class="card-title"></h5>
            
          @if ($message = Session::get('error'))
          <div class="alert alert-danger">
            <p>{{ $message }}</p>
          </div>
          @endif



{!! Form::open(array('route' => 'payment_method.store','method'=>'POST', 'enctype'=>'multipart/form-data')) !!}
<div class="row">

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control"value="{{ old('name') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="slug">{{trans('lang.slug')}}:</label>
            <select type="text" class="form-control" name="slug">
                <option value="click_pay">{{trans('lang.click_pay')}}</option>
                <option value="COD">{{trans('lang.cod')}}</option>
            </select>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="public_key">{{trans('lang.public_key')}}:</label>
            <input type="text" class="form-control" name="public_key" value="{{ old('public_key') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="secret_key">{{trans('lang.secret_key')}}:</label>
            <input type="text" class="form-control" name="secret_key" value="{{ old('secret_key') }}">
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
  $(document).ready(function() {
        // Assuming your <select> element has an id of 'mySelect'
        $('.shop_id').select2()
    });
</script>