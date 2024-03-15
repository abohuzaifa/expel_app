@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.product_create')}}</h1>
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
            
          @if ($message = Session::get('error'))
          <div class="alert alert-danger">
            <p>{{ $message }}</p>
          </div>
          @endif



{!! Form::open(array('route' => 'product.store','method'=>'POST', 'enctype'=>'multipart/form-data')) !!}
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 ">
        <div class="form-group">
            <strong>{{trans('lang.category')}}:</strong>
            {!! Form::select('category_id', $category,[], array('class' => 'form-control sel category', "required" =>"required")) !!}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 shop">
        <div class="form-group">
            <strong>{{trans('lang.shop')}}:</strong>
            {!! Form::select('shop_id', $shop,[], array('class' => 'form-control sel shop', "required" =>"required")) !!}
        </div>
    </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control"value="{{ old('name') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="image">{{trans('lang.image')}}:</label>
            <input type="file" class="form-control" name="image[]" multiple>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12"> 
            <label for="description">{{trans('lang.description')}}:</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="price">{{trans('lang.price')}}:</label>
            <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="tax">{{trans('lang.tax')}}:</label>
            <input type="text" class="form-control" name="tax" value="{{ old('tax') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="discount">{{trans('lang.discount')}}:</label>
            <input type="text" class="form-control" name="discount" value="{{ old('discount') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="taxable">{{trans('lang.taxable')}}:</label>
            <input type="checkbox"  name="taxable" value="{{ old('taxable') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="tax_inclusive">{{trans('lang.tax_inclusive')}}:</label>
            <input type="checkbox"  name="tax_inclusive" value="{{ old('tax_inclusive') }}">
        </div>

  
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
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
        $('.category').select2();
        $('.shop').select2()
    });
</script>

<!-- ALTER TABLE `products` ADD `shop_id` INT NOT NULL DEFAULT '0' AFTER `category_id`; -->