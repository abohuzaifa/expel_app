  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{route('home')}}" class="logo d-flex align-items-center">
        <img src="<?= asset("img/logo.png") ?>" alt="">
        <span class="d-none d-lg-block">{{ trans('lang.labeey') }}{{Session::get('branch_id')}}</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
<?php

  use Illuminate\Support\Facades\DB;

  $new_users = DB::select('SELECT id,name, user_type, created_at FROM users WHERE is_read =0 AND user_type != 0 ORDER BY id DESC'); ?>
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <a href="{{ url('lang/en') }}" class="mx-30px">English</a>
        <a href="{{ url('lang/ar') }}">العربية</a>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">{{count($new_users)}}</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu {{ app()->isLocale('ar') ? 'dropdown-menu-start' : 'dropdown-menu-end' }} dropdown-menu-arrow notifications">
            <li class="dropdown-header">
            {{trans('lang.you_have')}} {{count($new_users)}} {{trans('lang.new_notifications')}}
              <a href="{{ route('notifications.edit',['all', "choice" => "is_read"]) }}"><span class="badge rounded-pill bg-primary p-2 ms-2">{{trans('lang.read_all')}}</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
          <?php foreach($new_users as $key => $row) {
              if($row->user_type == 1)
              {
                echo '
                <li class="notification-item">
                  <i class="bi bi-exclamation-circle text-warning"></i>
                  <a href="'. route('notifications.edit',[$row->id]) .'">
                  <div>
                    <h4>'.trans('lang.new_seller').' <a href="'. route('notifications.edit',[$row->id, "choice" => "is_read"]) .'" style="'.(app()->isLocale('ar') ? "margin-right:50px;" : "margin-left:100px;").'" class="text-sm" href="#"><small>'.trans('lang.read').'</small></a></h4>
                    <p>'.$row->name.' '.trans('lang.new_seller_msg').'</p>
                    <p>'.formatCreatedAt($row->created_at).'</p>
                  </div>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>';
              } else {
                echo '
                <li class="notification-item">
                  <i class="bi bi-exclamation-circle text-warning"></i>
                  <a href="'. route('notifications.edit',[$row->id]) .'">
                  <div>
                    <h4>'.trans('lang.new_buyer').' <a href="'. route('notifications.edit',[$row->id, "choice" => "is_read"]) .'" style="'.(app()->isLocale('ar') ? "margin-right:70px;" : "margin-left:100px;").'" class="text-sm" href="#"><small>'.trans('lang.read').'</small></a></h4>
                    <p>'.$row->name.' '.trans('lang.new_buyer_msg').'</p>
                    <p>'.formatCreatedAt($row->created_at).'</p>
                  </div>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>';
              }
          }
            ?>
            

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <!-- <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a> End Messages Icon 

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
            {{trans('lang.you_have')}} 3 {{trans('lang.new_messages')}}
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">{{trans('lang.view_all')}}</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-1.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-2.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="assets/img/messages-3.jpg" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">{{trans('lang.show_all_messages')}}</a>
            </li>

          </ul> End Messages Dropdown Items 

        </li> -->
        <!-- End Messages Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="{{route('profile')}}" data-bs-toggle="dropdown">
            <img src="{{asset('images/'.Auth::user()->image)}}" alt="" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">{{Auth::user()->name}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{Auth::user()->name}}</h6>
              {{-- <span>Web Designer</span> --}}
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('profile')}}">
                <i class="bi bi-person"></i>
                <span>{{trans('lang.my_profile')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <!-- <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>{{trans('lang.account_settings')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>{{trans('lang.need_help?')}}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li> -->

            <li>
              <a class="dropdown-item d-flex align-items-center"    onclick="event.preventDefault();
              document.getElementById('logout-form').submit();" href="{{ route('logout') }}">
                <i class="bi bi-box-arrow-right"></i>
                <span>{{trans('lang.sign_out')}}</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            {{ trans('lang.logout') }}
        </a>


        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->
