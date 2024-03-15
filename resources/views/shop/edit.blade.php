@extends('layouts.app')


@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{trans('lang.shop_edit')}}</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('shop.index') }}"> {{trans('lang.back')}}</a>
        </div>
    </div>
</div>


@if ($message = Session::get('error'))
          <div class="alert alert-danger">
            <p>{{ $message }}</p>
          </div>
          @endif


{!! Form::model($shop, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['shop.update', $shop->id]]) !!}
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
                                    <div id="pillContainer" class="pill-container">
                                        <?php 
                                            $ids = explode(",",$shop->category_id);
                                            if(is_array($ids) && count($ids) > 0)
                                            {
                                                foreach($category as $cat)
                                                {
                                                    if(in_array($cat->id,$ids))
                                                    {
                                                        echo '<div class="pill" id="'.$cat->id.'-pill">
                                                                '.$cat->id.' - '.$cat->name.'
                                                                <a href="javascript:;">
                                                                <span style=" margin-left:30px; border:1px;" class="close-btn" onclick="removePill('.$cat->id.')">✖</span>
                                                                </a><input type="hidden" name="categories[]" value="'.$cat->id.'">
                                                            </div>';
                                                    }
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="name">{{trans('lang.name')}}:</label>
            <input type="text" name="name" class="form-control"value="{{ $shop->name }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="image">{{trans('lang.image')}}:</label>
            <input type="file" class="form-control" name="image">
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12"> 
            <label for="description">{{trans('lang.description')}}:</label>
            <textarea name="description" class="form-control">{{ $shop->description }}</textarea>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="reg_no">{{trans('lang.registration_no')}}:</label>
            <input type="text" class="form-control" name="reg_no" value="{{ $shop->reg_no }}" required>
        </div>

        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="address">{{trans('lang.address')}}:</label>
            <input type="text" class="form-control" name="address" value="{{ $shop->location }}">
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="latitude">{{trans('lang.latitude')}}:</label>
            <input type="text" class="form-control" name="latitude" value="{{ $shop->latitude }}" required>
        </div>
        <div  class="col-xs-12 col-sm-12 col-md-12">
            <label for="longitude">{{trans('lang.longitude')}}:</label>
            <input type="text" class="form-control" name="longitude" value="{{ $shop->longitude }}" required>
        </div>
  
    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><br>
        <button type="submit" class="btn btn-primary">{{trans('lang.submit')}}</button>
    </div>
</div>
{!! Form::close() !!}

        </div></section></div>
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