@extends('layouts.app')


@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{trans('lang.edit_product')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('product.index') }}"> {{trans('lang.back')}}</a>
        </div>
    </div>
</div>


@if ($message = Session::get('error'))
          <div class="alert alert-danger">
            <p>{{ $message }}</p>
          </div>
          @endif


{!! Form::model($product, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['product.update', $product->id]]) !!}
<div class="row">
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="category_id">{{trans('lang.category')}}:</label>
            {!! Form::select('category_id', $category, $product->category_id, ['class' => 'form-control sel category', 'required' => 'required']) !!}
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="shop_id">{{trans('lang.shop')}}:</label>
            {!! Form::select('shop_id', $shop, $product->shop_id, ['class' => 'form-control sel shop', 'required' => 'required']) !!}
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control"value="{{ $product->p_name }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="images">{{trans('lang.image')}}:</label>
            <input type="file" class="form-control" name="images[]" multiple>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12"> 
            <label for="description">{{trans('lang.description')}}:</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="price">{{trans('lang.price')}}:</label>
            <input type="text" class="form-control" name="price" value="{{ $product->price }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="tax">{{trans('lang.tax')}}:</label>
            <input type="text" class="form-control" name="tax" value="{{ $product->tax }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="discount">{{trans('lang.discount')}}:</label>
            <input type="text" class="form-control" name="discount" value="{{ $product->discount }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="taxable">{{trans('lang.taxable')}}:</label>
            <input type="checkbox"  name="taxable" value="{{ old('taxable') }}" <?= $product->taxable == 1 ? 'checked' : "" ?>>
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="tax_inclusive">{{trans('lang.tax_inclusive')}}:</label>
            <input type="checkbox"  name="tax_inclusive" value="{{ old('tax_inclusive') }}" <?= $product->tax_inclusive == 1 ? 'checked' : "" ?>>
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="status">{{trans('lang.status')}}:</label>
            <input type="checkbox"  name="status" value="{{ old('status') }}" <?= $product->status == 1 ? 'checked' : "" ?>>
        </div>

  
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">{{ trans('lang.submit') }}</button>
    </div>
</div>
{!! Form::close() !!}

        </div></section></div>
@endsection