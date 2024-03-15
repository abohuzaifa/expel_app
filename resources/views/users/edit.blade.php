@extends('layouts.app')


@section('content')
<div class="pagetitle">
    <h1>Update User</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Elements</li>
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


<!-- <div class="tab-pane fade profile-edit pt-3" id="profile-edit"> -->

                <!-- Profile Edit Form -->
                {!! Form::model($edit_user, ['method' => 'PATCH', 'route' => ['users.update', $edit_user->id], 'enctype' => 'multipart/form-data']) !!}
                  <div class="row mb-3">
                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.profile_image')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <img height="130px" width="150px" src="{{asset('images/'.$edit_user->image)}}" alt="{{trans('lang.profile')}}" id="previewImg">
                      <br>
                      <input type="file" style="display: none;"  name="image" id="image" onchange="previewImage()">
                      <div class="pt-2">
                        <label for="image"><a class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a></label>
                        <a onclick="removeImage()" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                      </div>
                    </div>
                  </div>
                 <input type="hidden" name="action" value="user_update">
                  <div class="row mb-3">
                    <label for="name" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.full_name')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="name" type="text" class="form-control" id="name" value="{{$edit_user->name}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="mobile" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.mobile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="mobile" type="mobile" class="form-control" id="mobile" value="{{$edit_user->mobile}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="email" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.email')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="email" value="{{$edit_user->email}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="city" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.city')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="city" type="city" class="form-control" id="city" value="{{$edit_user->city}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="state" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.state')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="state" type="state" class="form-control" id="state" value="{{$edit_user->state}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="country" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.country')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="country" type="country" class="form-control" id="country" value="{{$edit_user->country}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="street_address" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.street_address')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="street_address" type="street_address" class="form-control" id="street_address" value="{{$edit_user->street_address}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="latitude" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.latitude')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="latitude" type="latitude" class="form-control" id="latitude" value="{{$edit_user->latitude}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="longitude" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.longitude')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="longitude" type="longitude" class="form-control" id="longitude" value="{{$edit_user->longitude}}">
                    </div>
                  </div>
                  

                  

                  

                  <div class="row mb-3">
                    <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.twitter_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="twitter" type="text" class="form-control" id="Twitter" value="{{$edit_user->twitter}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.facebook_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="facebook" type="text" class="form-control" id="Facebook" value="{{$edit_user->facebook}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.instagram_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="instagram" type="text" class="form-control" id="Instagram" value="{{$edit_user->instagram}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.linkedin_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="linkedin" type="text" class="form-control" id="Linkedin" value="{{$edit_user->linkedin}}">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">{{trans('lang.save_changes')}}</button>
                  </div>
                  {!! Form::close() !!}<!-- End Profile Edit Form -->

              <!-- </div> -->

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