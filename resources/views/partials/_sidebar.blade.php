<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{ asset('assets/images/updent.png')}}"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">UP Dentistry</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src='{{ (Auth::user()->avatar) ? "/images/profile_photos/".Auth::user()->avatar : "https://i.stack.imgur.com/34AD2.jpg"  }}' class="img-circle elevation-2" alt="User Image" style="height: 30px !important;">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->first_name." ".Auth::user()->last_name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
   
          @if(Auth::user()->user_type != 1)
          <!-- admin -->
          <li class="nav-item">
            <a href="/home" class="nav-link {{ request()->is('home') ? 'active-link' : '' }}">
              <i class="nav-icon fa fa-dashboard"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/user-management" class="nav-link {{ request()->is('user-management') ? 'active-link' : '' }}">
              <i class="nav-icon fa fa-users"></i>
              <p>
                User Management
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/batch" class="nav-link {{ request()->is('batch') ? 'active-link' : '' }}">
              <i class="nav-icon fa fa-calendar"></i>
              <p>
                Batch Management
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/exam" class="nav-link {{ request()->is('exam') ? 'active-link' : '' }}">
              <i class="nav-icon fa fa-list"></i>
              <p>
                Exam Management
              </p>
            </a>
          </li>

          <li class="nav-header">REPORTS</li>
          <li class="nav-item">
            <a href="/exam_report" class="nav-link {{ request()->is('exam_report') ? 'active-link' : '' }}">
              <i class="nav-icon fa fa-circle-o text-danger"></i>
              <p>Exam Results</p>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-circle-o text-danger"></i>
              <p>Top Examinees</p>
            </a>
          </li> -->

          @else
          <!-- student -->
          <li class="nav-item">
            <a href="/student/exams" class="nav-link {{ request()->is('batch') ? 'active-link' : '' }}">
              <i class="nav-icon fa  fa-file-text"></i>
              <p>
               Exam/s
              </p>
            </a>
          </li>
          @endif

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>