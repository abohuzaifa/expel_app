@extends('layouts.app')


@section('content')

<div class="pagetitle">
  <h1>{{trans('lang.my_profile')}}</h1>
  <?php //print_r($user)  ?>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.my_profile')}}</li>
    </ol>
  </nav>
</div>
<section class="section profile">
    <div class="row">
      <div class="col-xl-4">

        <div class="card">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img src="{{asset('images/'.$user->image)}}" alt="Profile" class="rounded-circle">
            <h2>{{Auth::user()->name}}</h2>
            <h3></h3>
            <div class="social-links mt-2">
              <a target="_blank" href="{{Auth::user()->twitter}}" class="twitter"><i class="bi bi-twitter"></i></a>
              <a target="_blank" href="{{Auth::user()->facebook}}" class="facebook"><i class="bi bi-facebook"></i></a>
              <a target="_blank" href="{{Auth::user()->instagram}}" class="instagram"><i class="bi bi-instagram"></i></a>
              <a target="_blank" href="{{Auth::user()->linkedin}}" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
        </div>

      </div>

      <div class="col-xl-8">

        <div class="card">
        @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                        </div>
                        @endif
          <div class="card-body pt-3">
            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered">

              <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">{{trans('lang.overview')}}</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">{{trans('lang.edit_profile')}}</button>
              </li>

              {{-- <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
              </li> --}}

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">{{trans('lang.change_password')}}</button>
              </li>

            </ul>
            <div class="tab-content pt-2">

              <div class="tab-pane fade show active profile-overview" id="profile-overview">
                <h5 class="card-title">{{trans('lang.About')}}</h5>
                <p class="small fst-italic"><?php if(app()->isLocale('ar')){ ?>
                  🌟 مرحبًا بك في {{trans('lang.labeey')}} يقابل {{Auth::user()->name}}، الشخص الذي تلجأ إليه في كل الأمور خلف الكواليس. بدءًا من سحر الطلب إلى سحر الموقع، يضمن {{Auth::user()->name}} أن تكون تجربة التسوق الخاصة بك على أعلى مستوى. تحتاج مساعدة؟ {{Auth::user()->name}} مجرد رسالة! تسوق سعيد! 🛒🔧 #الفريق_الإداري
                    <?php } else {?>🌟 Welcome to {{trans('lang.labeey')}}! Meet {{Auth::user()->name}}, your go-to person for all things behind the scenes. From order magic to site wizardry, {{Auth::user()->name}} ensures your shopping experience is top-notch. Need help? {{Auth::user()->name}} is just a message away! Happy shopping! 🛒🔧 #AdminTeam.</p>
                    <?php } ?>
                <h5 class="card-title">{{trans('lang.profile_details')}}</h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label ">{{trans('lang.full_name')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->name}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.mobile')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->mobile}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.email')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->email}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.city')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->city}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.state')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->state}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.country')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->country}}</div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">{{trans('lang.street_address')}}</div>
                  <div class="col-lg-9 col-md-8">{{Auth::user()->street_address}}</div>
                </div> 

                

              </div>

              <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                <!-- Profile Edit Form -->
                {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id], 'enctype' => 'multipart/form-data']) !!}
                  <div class="row mb-3">
                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.profile_image')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <img src="{{asset('images/'.$user->image)}}" alt="{{trans('lang.profile')}}" id="previewImg">
                      <br>
                      <input type="file" style="display: none;"  name="image" id="image" onchange="previewImage()">
                      <div class="pt-2">
                        <label for="image"><a class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a></label>
                        <a onclick="removeImage()" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="name" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.full_name')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="name" type="text" class="form-control" id="name" value="{{$user->name}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="mobile" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.mobile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="mobile" type="mobile" class="form-control" id="mobile" value="{{Auth::user()->mobile}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="email" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.email')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="email" value="{{Auth::user()->email}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="city" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.city')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="city" type="city" class="form-control" id="city" value="{{Auth::user()->city}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="state" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.state')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="state" type="state" class="form-control" id="state" value="{{Auth::user()->state}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="country" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.country')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="country" type="country" class="form-control" id="country" value="{{Auth::user()->country}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="street_address" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.street_address')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="street_address" type="street_address" class="form-control" id="street_address" value="{{Auth::user()->street_address}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="latitude" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.latitude')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="latitude" type="latitude" class="form-control" id="latitude" value="{{Auth::user()->latitude}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="longitude" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.longitude')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="longitude" type="longitude" class="form-control" id="longitude" value="{{Auth::user()->longitude}}">
                    </div>
                  </div>
                  

                  

                  

                  <div class="row mb-3">
                    <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.twitter_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="twitter" type="text" class="form-control" id="Twitter" value="{{Auth::user()->twitter}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.facebook_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="facebook" type="text" class="form-control" id="Facebook" value="{{Auth::user()->facebook}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.instagram_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="instagram" type="text" class="form-control" id="Instagram" value="{{Auth::user()->instagram}}">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.linkedin_profile')}}</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="linkedin" type="text" class="form-control" id="Linkedin" value="{{Auth::user()->linkedin}}">
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">{{trans('lang.save_changes')}}</button>
                  </div>
                  {!! Form::close() !!}<!-- End Profile Edit Form -->

              </div>

              <div class="tab-pane fade pt-3" id="profile-settings">

                <!-- Settings Form -->
                <form>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                    <div class="col-md-8 col-lg-9">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="changesMade" checked>
                        <label class="form-check-label" for="changesMade">
                          Changes made to your account
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="newProducts" checked>
                        <label class="form-check-label" for="newProducts">
                          Information on new products and services
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="proOffers">
                        <label class="form-check-label" for="proOffers">
                          Marketing and promo offers
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                        <label class="form-check-label" for="securityNotify">
                          Security alerts
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End settings Form -->

              </div>
            

              <div class="tab-pane fade pt-3" id="profile-change-password">
                <!-- Change Password Form -->
                {!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id], 'enctype' => 'multipart/form-data']) !!}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="is_password" value="1">
                  {{-- <div class="row mb-3">
                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="password" type="password" class="form-control"  id="currentPassword">
                    </div>
                  </div> --}}

                  <div class="row mb-3">
                    <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.new_password')}}</label>
                    <div class="col-md-8 col-lg-9">
                        <input class="form-control" name="password" id="password" value="" required onkeyup="checkPassword()">
                 
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">{{trans('lang.re_enter_new_password')}}</label>
                    <div class="col-md-8 col-lg-9">
                        <input class="form-control" name="confirm_password" id="confirm_password" required onkeyup="checkPassword()" value="">
                      <span id="pass_message"></span>
                    </div>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn btn-primary passbtn">{{trans('lang.change_password')}}</button>
                  </div>
                </form><!-- End Change Password Form -->

              </div>

            </div><!-- End Bordered Tabs -->

          </div>
        </div>

      </div>
    </div>
  </section>
  @endsection
  <script>
    function checkPassword() {
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();

      if(confirm_password != '')
      {
            if(password != confirm_password)
            {
                $("#pass_message").html('<span style="color:red;">Password Don,t Match ! </span>');
                $(".btn-success").attr("disabled","disabled");
                $(".passbtn").attr('disabled', "disabled");
            }
            else
                {
                    $("#pass_message").html('<span style="color:green;">Password Matched ! </span>');
                    $(".btn-success").removeAttr("disabled")
                    $(".passbtn").removeAttr("disabled");
                }
        }
    }
    
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