@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.shop_create')}}</h1>
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



{!! Form::open(array('route' => 'shop.store','method'=>'POST', 'enctype'=>'multipart/form-data')) !!}
<div class="row">
                                <div class="col-12" >
                                    <label for="cat_ids" class="text-capitalize">{{trans('lang.category')}}</label>
                                    <select id="cat_ids" onchange="addPill();"
                                    class="svselect form-select form-select-sm">
                                      <option value="0"></option>
                                      @foreach($category as $value)
                                      <option value="{{$value->id}}">{{$value->name}}</option>
                                      @endforeach
                                    </select>
                                    <div id="pillContainer" class="pill-container"></div>
                                </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control"value="{{ old('name') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="image">{{trans('lang.image')}}:</label>
            <input type="file" class="form-control" name="image">
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12"> 
            <label for="description">{{trans('lang.description')}}:</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="reg_no">{{trans('lang.registration_no')}}:</label>
            <input type="text" class="form-control" name="reg_no" value="{{ old('reg_no') }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="address">{{trans('lang.address')}}:</label>
            <input type="text" class="form-control" name="address" value="{{ old('address') }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="latitude">{{trans('lang.latitude')}}:</label>
            <input type="text" class="form-control" name="latitude" value="{{ old('latitude') }}" required>
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="longitude">{{trans('lang.longitude')}}:</label>
            <input type="text" class="form-control" name="longitude" value="{{ old('longitude') }}" required>
        </div>
  
    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><br>
        <button type="submit" class="btn btn-primary">{{trans('lang.submit')}}</button>
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
    function addPill() {
      var selectField = document.getElementById('cat_ids');
      var selectedOption = selectField.options[selectField.selectedIndex];

      // Get text and value of the selected option
      var text = selectedOption.text;
      var selectedValue = selectedOption.value;
      if (selectedValue !== "") {
        var pillContainer = document.getElementById('pillContainer');

        // Check if the pill already exists
        if (!document.getElementById(selectedValue + '-pill') && selectedValue > 0) {
          var pill = document.createElement('div');
          pill.className = 'pill';
          pill.id = selectedValue + '-pill';
          pill.innerHTML = selectedValue+" - "+text + '<a href="javascript:;"><span style=" margin-left:30px; border:1px;" class="close-btn" onclick="removePill(\'' + selectedValue + '\')">&#10006;</span></a><input type="hidden" name="categories[]" value="'+selectedValue+'">';
          
          pillContainer.appendChild(pill);
        }

        // Reset the select field
        selectField.value = "";
      }
    }

    function removePill(value) {
      var pillToRemove = document.getElementById(value + '-pill');
      if (pillToRemove) {
        pillToRemove.remove();
      }
    }
  </script>