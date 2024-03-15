@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.banner_create')}}</h1>
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



            {!! Form::model($banner, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['banners.update', $banner->id]]) !!}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{trans('lang.slug')}}:</strong>
            {!! Form::text('slug', $banner->slug, array('placeholder' => trans('lang.slug'),'class' => 'form-control', 'required' =>'required')) !!}
        </div>
    </div><br>
    <div class="row mb-3">
                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.profile_image')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <img height="130px" width="150px" src="{{asset('images/'.$banner->image)}}" alt="{{trans('lang.profile')}}" id="previewImg">
                      <br>
                      <input type="file" style="display: none;"  name="image" id="image" onchange="previewImage()">
                      <div class="pt-2">
                        <label for="image"><a class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a></label>
                        <a onclick="removeImage()" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                      </div>
                    </div>
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
        function previewImage() {
      // Get the file input and image preview elements
      var fileInput = document.getElementById('image');
      var imagePreview = document.getElementById('previewImg');

      // Check if a file is selected
      if (fileInput.files && fileInput.files[0]) {
        // Create a FileReader object to read the file
        var reader = new FileReader();

        // Set a callback function to execute when the file is loaded
        reader.onload = function (e) {
          // Update the src attribute of the image preview with the loaded data URL
          imagePreview.src = e.target.result;
        };

        // Read the file as a data URL
        reader.readAsDataURL(fileInput.files[0]);
      } else {
        // If no file is selected, clear the image preview
        imagePreview.src = "";
      }
    }

    function removeImage() {
      var imagePreview = document.getElementById('previewImg');
      var fileInput = document.getElementById('image');

      // Clear the image preview and reset the file input
      imagePreview.src = "";
      fileInput.value = "";
    }
</script>